<?php
// joinRoom.php
// ไม่มี whitespace หรือ BOM ก่อน <?php

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

// ล้าง output buffer หากมีข้อมูลเกินมา
if (ob_get_length()) {
    ob_clean();
}

$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่า room_name ถูกส่งมาหรือไม่
$room_name = isset($data['room_name']) ? $data['room_name'] : (isset($_GET['room_name']) ? $_GET['room_name'] : null);
if (!$room_name) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'room_name is required.'
    ], JSON_UNESCAPED_UNICODE);
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
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

include '../config/db.php';

// ดึงข้อมูลห้องด้วย room_name
$sql = "SELECT RoomID, RoomCode, CreatedBy FROM rooms WHERE room_name = :room_name LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':room_name' => $room_name]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Room not found.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ตรวจสอบว่าผู้ใช้มีอยู่ในตาราง users หรือไม่
$sqlUser = "SELECT UserID FROM users WHERE UserID = :userID LIMIT 1";
$stmtUser = $pdo->prepare($sqlUser);
$stmtUser->execute([':userID' => $userID]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'User not found in the system. Provided ID: ' . $userID
    ], JSON_UNESCAPED_UNICODE);
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
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO roommembers (RoomID, UserID) VALUES (:roomID, :userID)");
    $stmt->execute([
        ':roomID' => $room['RoomID'],
        ':userID' => $userID
    ]);
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status'   => 'success',
        'message'  => 'Joined room successfully.',
        'roomID'   => $room['RoomID'],
        'roomCode' => $room['RoomCode'],
        'hostId'   => $room['CreatedBy']
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to join room: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
