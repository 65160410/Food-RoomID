<?php
require_once 'db.php'; // เชื่อมต่อฐานข้อมูล

// ฟังก์ชันสำหรับดึงข้อมูลร้านอาหารที่แนะนำ
function getRecommendedRestaurants($pdo)
{
    $stmt = $pdo->query("
        SELECT RestaurantID, RestaurantName, Description, AverageRating, CuisineType 
        FROM Restaurant
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ฟังก์ชันสำหรับบันทึกการโหวต
function saveVote($pdo, $userId, $roomId, $restaurantId)
{
    $stmt = $pdo->prepare("
        INSERT INTO Votes (UserID, RoomID, RestaurantID, VoteTime) 
        VALUES (:user_id, :room_id, :restaurant_id, NOW())
    ");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':room_id', $roomId);
    $stmt->bindParam(':restaurant_id', $restaurantId);

    if ($stmt->execute()) {
        return true;
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("Database error: " . $errorInfo[2]);
        return false;
    }
}

// ฟังก์ชันสำหรับดึงสถานะการโหวต
function getVotingStatus($pdo, $roomId)
{
    $stmt = $pdo->prepare("
        SELECT Users.Username, Restaurant.RestaurantName, Votes.VoteTime
        FROM Votes
        JOIN Users ON Votes.UserID = Users.UserID
        JOIN Restaurant ON Votes.RestaurantID = Restaurant.RestaurantID
        WHERE Votes.RoomID = :room_id
    ");
    $stmt->bindParam(':room_id', $roomId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ตรวจสอบว่ามีการส่งคำขอ POST มาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'vote') {
        $userId = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
        $roomId = filter_var($_POST['room_id'], FILTER_VALIDATE_INT);
        $restaurantId = filter_var($_POST['restaurant_id'], FILTER_VALIDATE_INT);

        if (!$userId || !$roomId || !$restaurantId) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            exit;
        }

        if (saveVote($pdo, $userId, $roomId, $restaurantId)) {
            echo json_encode(['status' => 'success', 'message' => 'Vote recorded successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to record vote']);
        }
        exit;
    }
}

// ส่งข้อมูลกลับไปยัง Front-End ในรูปแบบ JSON
header('Content-Type: application/json');

$restaurants = getRecommendedRestaurants($pdo);
$votingStatus = getVotingStatus($pdo, $_GET['room_id'] ?? 1); // ใช้ RoomID จาก query string

echo json_encode([
    'restaurants' => $restaurants,
    'voting_status' => $votingStatus
]);
