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

$roomName  = $data['roomName'];
$createdBy = $data['createdBy'] ?? 0; // ถ้าไม่ได้ส่งมา ให้ใช้ 0

// สร้าง RoomCode แบบสุ่ม 6 ตัวอักษร/ตัวเลข
$roomCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);

// เชื่อมต่อฐานข้อมูลด้วย PDO (ไฟล์ db.php ควรอยู่ใน path ที่ถูกต้อง)
include '../config/db.php';

$sql = "INSERT INTO rooms (room_name, CreatedBy, RoomCode) VALUES (:room_name, :createdBy, :roomCode)";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':room_name' => $roomName,
        ':createdBy' => $createdBy,
        ':roomCode' => $roomCode
    ]);
    
    // ดึง RoomID ที่เพิ่ง insert
    $newRoomId = $pdo->lastInsertId();
    error_log("createdBy: " . $createdBy);

    echo json_encode([
        'status'   => 'success',
        'message'  => 'Room created successfully.',
        'RoomID'   => $newRoomId,
        'RoomCode' => $roomCode
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to create room: ' . $e->getMessage()
    ]);
}
