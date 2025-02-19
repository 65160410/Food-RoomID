<?php
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

// รับข้อมูล JSON จาก Frontend
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่ามี room_name และ joinerId ถูกส่งมาหรือไม่
if (!isset($data['room_name']) || !isset($data['joinerId'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'room_name and joinerId are required.'
    ]);
    exit;
}

$room_name = trim($data['room_name']);
$joinerId  = intval($data['joinerId']);

// เชื่อมต่อฐานข้อมูลด้วย PDO (ตรวจสอบให้แน่ใจว่า path ถูกต้อง)
include '../config/db.php';

// ค้นหาห้องด้วย room_name
$sql = "SELECT RoomID, CreatedBy FROM rooms WHERE room_name = :room_name";
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

// (ตัวเลือก) ตรวจสอบว่า user ที่เข้าร่วมไม่ใช่ host เองหรือไม่
// ถ้าไม่ต้องการบังคับเงื่อนไขนี้ สามารถลบได้
if ($room['CreatedBy'] == $joinerId) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Host cannot join as a participant.'
    ]);
    exit;
}

// ตรวจสอบว่าผู้ใช้ได้เข้าร่วมห้องนี้ไปแล้วหรือไม่
$sql = "SELECT * FROM roommembers WHERE RoomID = :roomID AND UserID = :joinerId";
$stmt = $pdo->prepare($sql);
$stmt->execute([':roomID' => $room['RoomID'], ':joinerId' => $joinerId]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

// ถ้าเจอว่ามี record แล้ว ให้ส่ง status=success แทนการส่ง error
if ($existing) {
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Already joined this room.',
        'hostId'   => $room['CreatedBy']  // ส่ง host id กลับไปด้วยหากต้องการ
    ]);
    exit;
}

// ถ้ายังไม่เคย join ให้เพิ่มข้อมูลผู้เข้าร่วมลงในตาราง roommembers
$sql = "INSERT INTO roommembers (RoomID, UserID) VALUES (:roomID, :joinerId)";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([
        ':roomID'  => $room['RoomID'],
        ':joinerId'=> $joinerId
    ]);

    echo json_encode([
        'status'   => 'success',
        'message'  => 'Joined room successfully.',
        'hostId'   => $room['CreatedBy']  // ส่ง host id กลับไปด้วย หากต้องการใช้งานต่อ
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to join room: ' . $e->getMessage()
    ]);
}
