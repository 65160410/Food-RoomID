<?php
// resetRoomCountdown.php

ini_set('display_errors', 0);
error_reporting(0);

ob_start();
header('Content-Type: application/json');

$roomID = $_GET['roomID'];
include '../config/db.php'; 

try {

    $newStartTime = round(microtime(true) * 1000);

    $stmt = $pdo->prepare("UPDATE rooms SET countdownStartTime = ? WHERE roomID = ?");
    $stmt->execute([$newStartTime, $roomID]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "newStartTime" => $newStartTime]);
    } else {
       
        echo json_encode(["status" => "error", "message" => "Room not found or no update made"]);
    }

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

ob_end_flush();
?>
