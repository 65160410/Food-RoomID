<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['action'])) {
    if ($input['action'] === 'like') {
        $userID   = $input['userID'] ?? null;
        $roomID   = $input['roomID'] ?? null;
        $foodName = $input['foodName'] ?? null;
        $vote     = $input['vote'] ?? 1;
        $voteTime = date("Y-m-d H:i:s");

        if ($userID && $roomID && $foodName) {
            try {
                $stmt = $pdo->prepare("INSERT INTO votes (UserID, RoomID, VoteTime, foodName, vote) VALUES (:userID, :roomID, :voteTime, :foodName, :vote)");
                $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
                $stmt->bindParam(':roomID', $roomID, PDO::PARAM_INT);
                $stmt->bindParam(':voteTime', $voteTime);
                $stmt->bindParam(':foodName', $foodName);
                $stmt->bindParam(':vote', $vote, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode([
                    'status'  => 'success',
                    'message' => 'Vote recorded successfully',
                    'data'    => [
                        'UserID'   => $userID,
                        'RoomID'   => $roomID,
                        'VoteTime' => $voteTime,
                        'foodName' => $foodName,
                        'vote'     => $vote
                    ]
                ]);
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Missing required fields'
            ]);
        }
        exit;
    } elseif ($input['action'] === 'delete') {
        $userID   = $input['userID'] ?? null;
        $roomID   = $input['roomID'] ?? null;
        $foodName = isset($input['foodName']) ? trim($input['foodName']) : null;
        if ($userID && $roomID && $foodName) {
            try {
                $stmt = $pdo->prepare("DELETE FROM votes WHERE UserID = :userID AND RoomID = :roomID AND TRIM(foodName) = :foodName");
                $stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
                $stmt->bindParam(':roomID', $roomID, PDO::PARAM_INT);
                $stmt->bindParam(':foodName', $foodName);
                $stmt->execute();
                $deletedRows = $stmt->rowCount();
                if ($deletedRows > 0) {
                    echo json_encode([
                        'status'  => 'success',
                        'message' => 'Vote deleted successfully'
                    ]);
                } else {
                    echo json_encode([
                        'status'  => 'error',
                        'message' => 'No matching vote found to delete'
                    ]);
                }
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Missing required fields for deletion'
            ]);
        }
        exit;
    } elseif ($input['action'] === 'aggregate') {
        // Code for aggregation (unchanged)
        $roomID = $input['roomID'] ?? null;
        if ($roomID) {
            try {
                $stmt = $pdo->prepare("SELECT foodName, SUM(vote) AS total FROM votes WHERE RoomID = :roomID GROUP BY foodName");
                $stmt->bindParam(':roomID', $roomID, PDO::PARAM_INT);
                $stmt->execute();
                $votes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $aggregatedResult = [
                    'roomID' => $roomID,
                    'votes'  => $votes
                ];

                echo json_encode([
                    'status' => 'success',
                    'data'   => $aggregatedResult
                ]);
            } catch (PDOException $e) {
                error_log("Database error: " . $e->getMessage());
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Database error: ' . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Missing required roomID for aggregation'
            ]);
        }
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    exit;
}
?>
