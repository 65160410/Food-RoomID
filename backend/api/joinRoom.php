<?php
// joinRoom.php 
// เปิดการแสดง error (สำหรับการพัฒนา)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS headers เพื่ออนุญาตให้เข้าถึง API จากทุกแหล่ง (สำหรับการพัฒนา)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// หากเป็น preflight request (OPTIONS) ให้หยุดที่นี่
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

header('Content-Type: application/json; charset=utf-8');

// รับข้อมูล JSON จาก body
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่า room_name ถูกส่งมาหรือไม่
$room_name = isset($data['room_name']) ? $data['room_name'] : null;
if (!$room_name) {
    // หากไม่พบ room_name ใน JSON payload ให้ลองตรวจสอบใน URL
    $room_name = isset($_GET['room_name']) ? $_GET['room_name'] : null;
}

if (!$room_name) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'room_name is required.'
    ]);
    exit;
}

// ตรวจสอบว่า UserID ถูกส่งมาหรือไม่
if (isset($data['userID'])) {
    $userID = intval($data['userID']);
} else {
    $userID = isset($_GET['userID']) ? intval($_GET['userID']) : null;
}

if (!$userID) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'UserID is missing.'
    ]);
    exit;
}

// เชื่อมต่อฐานข้อมูลด้วย PDO
include '../config/db.php';

// ค้นหาห้องด้วย room_name และดึงข้อมูลที่จำเป็น
$sql = "SELECT RoomID, RoomCode, CreatedBy FROM rooms WHERE room_name = :room_name LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':room_name' => $room_name]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Room not found.'
    ]);
    exit;
}

// ตรวจสอบว่าผู้ใช้ (UserID) มีอยู่ในตาราง users หรือไม่
$sqlUser = "SELECT UserID FROM users WHERE UserID = :userID LIMIT 1";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute([':userID' => $userID]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'User not found in the system. Provided ID: ' . $userID
    ]);
    exit;
}

// ตรวจสอบว่าผู้ใช้ได้เข้าร่วมห้องนี้ไปแล้วหรือไม่
$sql = "SELECT * FROM roommembers WHERE RoomID = :roomID AND UserID = :userID";
$stmt = $pdo->prepare($sql);
$stmt->execute([':roomID' => $room['RoomID'], ':userID' => $userID]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Already joined this room.',
        'roomID'   => $room['RoomID'],
        'roomCode' => $room['RoomCode'],
        'hostId'   => $room['CreatedBy']
    ]);
    exit;
}

// เพิ่มข้อมูลลงใน roommembers
$sql = "INSERT INTO roommembers (RoomID, UserID) VALUES (:roomID, :userID)";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([
        ':roomID'   => $room['RoomID'],
        ':userID'   => $userID
    ]);

    echo json_encode([
        'status'   => 'success',
        'message'  => 'Joined room successfully.',
        'roomID'   => $room['RoomID'],
        'roomCode' => $room['RoomCode'],
        'hostId'   => $room['CreatedBy']
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to join room: ' . $e->getMessage()
    ]);
}
?>
