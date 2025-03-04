<?php
// getVoteCounts.php
header('Content-Type: application/json');
include '../config/db.php';

if (!isset($_GET['roomID'])) {
    echo json_encode(["error" => "roomID not provided"]);
    exit;
}
$roomID = $_GET['roomID'];

try {
    $stmt = $pdo->prepare("SELECT foodName, COUNT(*) as voteCount FROM votes WHERE roomID = ? GROUP BY foodName");
    $stmt->execute([$roomID]);
    $voteCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // แปลงผลลัพธ์ให้เป็นรูปแบบ votes ที่มี array ของชื่ออาหาร
    $votes = [];
    foreach ($voteCounts as $row) {
        $foodName = $row['foodName'];
        $voteCount = (int)$row['voteCount'];
        for ($i = 0; $i < $voteCount; $i++) {
            $votes[] = $foodName;
        }
    }
    
    echo json_encode(["votes" => $votes]);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
