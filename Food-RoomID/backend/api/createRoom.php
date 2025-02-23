<?php
// createRoom.php
// ไม่มี whitespace หรือข้อความใดๆ ก่อน <?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตั้งค่า CORS และ Content-Type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
header('Content-Type: application/json; charset=utf-8');

// ล้าง output buffer หากมีข้อมูลเกินมา
if (ob_get_length()) {
    ob_clean();
}

// รับข้อมูล JSON จาก Frontend
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่ามี roomName และ createdBy
if (!isset($data['roomName']) || empty(trim($data['roomName']))) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'roomName is required.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
if (!isset($data['createdBy']) || intval($data['createdBy']) <= 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Valid UserID is required.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$roomName  = trim($data['roomName']);
$createdBy = intval($data['createdBy']);

/**
 * ฟังก์ชันสำหรับสร้างรหัสห้องที่ไม่ซ้ำกัน
 * โดยในที่นี้จะสร้างรหัสความยาว 6 ตัวอักษร
 */
function generateRoomCode($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, $charactersLength - 1)];
    }
    return $code;
}

$roomCode = generateRoomCode();

include '../config/db.php';

$sql = "INSERT INTO rooms (room_name, RoomCode, CreatedBy) VALUES (:room_name, :roomCode, :createdBy)";
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':room_name' => $roomName,
        ':roomCode'  => $roomCode,
        ':createdBy' => $createdBy
    ]);
    $newRoomId = $pdo->lastInsertId();
    // ล้าง output buffer อีกครั้งก่อนส่ง response
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Room created successfully.',
        'RoomID'   => $newRoomId,
        'RoomCode' => $roomCode
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to create room: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
