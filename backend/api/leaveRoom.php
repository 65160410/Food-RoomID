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

// ตรวจสอบค่าที่จำเป็น
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

// ฟังก์ชันสำหรับลบ Guest จากตาราง users
function deleteGuestFromUsers($pdo, $joinerId) {
    $sqlUser = "SELECT isGuest FROM users WHERE UserID = :joinerId LIMIT 1";
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->execute([':joinerId' => $joinerId]);
    $userRecord = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if ($userRecord && intval($userRecord['isGuest']) === 1) {
        $stmtDeleteUser = $pdo->prepare("DELETE FROM users WHERE UserID = :joinerId");
        $stmtDeleteUser->execute([':joinerId' => $joinerId]);
    }
}

// ตรวจสอบว่า joinerId เป็น host หรือไม่ (เปรียบเทียบเป็นตัวเลข)
if ((int)$room['CreatedBy'] === $joinerId) {
    // Host Leave: Host อาจไม่อยู่ใน roommembers จึงไม่ต้องตรวจสอบ rowCount
    $pdo->beginTransaction();
    try {
        // ลบสมาชิกทั้งหมดในห้อง
        $stmt = $pdo->prepare("DELETE FROM roommembers WHERE RoomID = :roomID");
        $stmt->execute([':roomID' => $roomID]);

        // ลบห้องออกจากตาราง rooms
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE RoomID = :roomID");
        $stmt->execute([':roomID' => $roomID]);

        // หาก host เป็น Guest ให้ลบออกจาก users ด้วย
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
    // Non-host Leave: ลบ record จาก roommembers
    $stmt = $pdo->prepare("DELETE FROM roommembers WHERE RoomID = :roomID AND UserID = :joinerId");
    $stmt->execute([':roomID' => $roomID, ':joinerId' => $joinerId]);

    // หาก rowCount เป็น 0 ให้ถือว่าไม่มี recordใน roommembers (แต่ถือว่าผู้ใช้ไม่ได้อยู่ในห้องแล้ว)
    if ($stmt->rowCount() >= 0) { 
        // ตรวจสอบและลบ Guest จาก users หากเป็น Guest
        deleteGuestFromUsers($pdo, $joinerId);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Left room successfully.'
        ]);
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to leave room.'
        ]);
    }
}
?>
