<?php
// leaveRoom.php

// เปิดการแสดง error (สำหรับการพัฒนา)
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

// เชื่อมต่อฐานข้อมูล (ตรวจสอบให้แน่ใจว่าไฟล์ db.php มีการสร้าง PDO connection ไว้ในตัวแปร $pdo)
include '../config/db.php';

// ดึงข้อมูลห้องเพื่อตรวจสอบว่าใครเป็น host
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

// ตรวจสอบว่า joinerId ที่ส่งมาเป็น host หรือไม่
if ($room['CreatedBy'] == $joinerId) {
    // หาก host กำลังออกจากห้อง ให้ลบห้องและสมาชิกทั้งหมดในห้อง
    $pdo->beginTransaction();
    try {
        // ลบข้อมูลสมาชิกในห้อง (ตาราง roommembers)
        $stmt = $pdo->prepare("DELETE FROM roommembers WHERE RoomID = :roomID");
        $stmt->execute([':roomID' => $roomID]);
        
        // ลบห้องจากตาราง rooms
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE RoomID = :roomID");
        $stmt->execute([':roomID' => $roomID]);
        
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
    // หากไม่ใช่ host ให้ลบเฉพาะสมาชิกออกจากห้อง (แต่ในกรณีนี้ เราสนใจเฉพาะกรณี host leave)
    $stmt = $pdo->prepare("DELETE FROM roommembers WHERE RoomID = :roomID AND UserID = :joinerId");
    if ($stmt->execute([':roomID' => $roomID, ':joinerId' => $joinerId])) {
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
