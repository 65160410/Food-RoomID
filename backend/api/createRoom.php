<?php
// เปิดการแสดง error (สำหรับการพัฒนา)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS headers เพื่ออนุญาตให้เข้าถึง API จากทุกแหล่ง (ระหว่างพัฒนา)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// หากเป็น preflight request (OPTIONS) ให้หยุดที่นี่
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

header('Content-Type: application/json; charset=utf-8');

// รับข้อมูล JSON จาก Frontend
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่ามีการส่ง roomName มาหรือไม่
if (!isset($data['roomName'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'roomName is required.'
    ]);
    exit;
}

$roomName  = trim($data['roomName']);
$createdBy = isset($data['createdBy']) ? intval($data['createdBy']) : 0; // ถ้าไม่ได้ส่งมา ให้ใช้ 0

// เชื่อมต่อฐานข้อมูลด้วย PDO (ไฟล์ db.php ควรอยู่ใน path ที่ถูกต้อง)
include '../config/db.php';

// คำสั่ง SQL สำหรับ Insert ห้องใหม่
$sql = "INSERT INTO rooms (room_name, CreatedBy) VALUES (:room_name, :createdBy)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':room_name'  => $roomName,
        ':createdBy'  => $createdBy
    ]);
    
    // ดึง RoomID ที่เพิ่ง Insert
    $newRoomId = $pdo->lastInsertId();
    error_log("createdBy: " . $createdBy);

    echo json_encode([
        'status'   => 'success',
        'message'  => 'Room created successfully.',
        'RoomID'   => $newRoomId
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to create room: ' . $e->getMessage()
    ]);
}
?>
