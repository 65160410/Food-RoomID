<?php
// leaveRoom.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

header('Content-Type: application/json; charset=utf-8');

// รับข้อมูล JSON จาก body
$data = json_decode(file_get_contents('php://input'), true);

$roomID   = isset($data['roomID']) ? intval($data['roomID']) : null;
$joinerId = isset($data['joinerId']) ? intval($data['joinerId']) : null;

if (!$roomID || !$joinerId) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Missing parameters: roomID and joinerId are required.'
    ]);
    exit;
}

include '../config/db.php';

// ดึงข้อมูลห้อง
$sql = "SELECT * FROM rooms WHERE RoomID = :roomID";
$stmt = $pdo->prepare($sql);
$stmt->execute([':roomID' => $roomID]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Room not found.'
    ]);
    exit;
}

// ฟังก์ชันสำหรับลบ Guest ออกจากตาราง users
function deleteGuestFromUsers($pdo, $joinerId) {
    // ตรวจสอบ isGuest
    $sqlUser = "SELECT isGuest FROM users WHERE UserID = :joinerId LIMIT 1";
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->execute([':joinerId' => $joinerId]);
    $userRecord = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($userRecord && intval($userRecord['isGuest']) === 1) {
        // ลบออกจากตาราง users
        $stmtDeleteUser = $pdo->prepare("DELETE FROM users WHERE UserID = :joinerId");
        $stmtDeleteUser->execute([':joinerId' => $joinerId]);
    }
}

// กรณี Host Leave
if ($room['CreatedBy'] == $joinerId) {
    // Host กำลังออกจากห้อง
    $pdo->beginTransaction();
    try {
        // ลบสมาชิกทั้งหมดในห้อง
        $stmt = $pdo->prepare("DELETE FROM roommembers WHERE RoomID = :roomID");
        $stmt->execute([':roomID' => $roomID]);

        // ลบห้อง
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE RoomID = :roomID");
        $stmt->execute([':roomID' => $roomID]);

        // หาก Host เป็น Guest -> ลบออกจาก users
        deleteGuestFromUsers($pdo, $joinerId);

        $pdo->commit();

        echo json_encode([
            'status'  => 'success',
            'message' => 'Room deleted successfully because host left.'
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to delete room: ' . $e->getMessage()
        ]);
    }
} else {
    // Non-host Leave
    $stmt = $pdo->prepare("DELETE FROM roommembers WHERE RoomID = :roomID AND UserID = :joinerId");
    $stmt->execute([':roomID' => $roomID, ':joinerId' => $joinerId]);

    if ($stmt->rowCount() > 0) {
        // ลบ Guest จาก users ถ้าเป็น Guest
        deleteGuestFromUsers($pdo, $joinerId);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Left room successfully.'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to leave room or user not found in roommembers.'
        ]);
    }
}
