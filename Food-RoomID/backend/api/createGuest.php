<?php
// createGuest.php
// ไม่มี whitespace ก่อน <?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตั้งค่า CORS และ Content-Type ให้เป็น UTF-8
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(0); }
header('Content-Type: application/json; charset=utf-8');

// ล้าง output buffer หากมีข้อมูลเกินมา
if (ob_get_length()) {
    ob_clean();
}

$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบ guestName
if (!isset($data['guestName']) || empty(trim($data['guestName']))) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Guest name is required.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$guestName = trim($data['guestName']);
$guestAvatar = (isset($data['guestAvatar']) && !empty(trim($data['guestAvatar']))) 
    ? trim($data['guestAvatar']) 
    : "default-avatar.png";

// สำหรับ guest account ให้ Email เป็น NULL หรือ dummy ที่ไม่ซ้ำ
$email = null; // หรือ "guest_" . time() . "@example.com";

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

$sql = "INSERT INTO users (Username, Avatar, Email, isGuest) VALUES (:guestName, :guestAvatar, :email, 1)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        ':guestName'   => $guestName,
        ':guestAvatar' => $guestAvatar,
        ':email'       => $email
    ]);
    $guestUserId = $pdo->lastInsertId();
    // ล้าง output buffer อีกครั้งก่อนส่ง response
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status'      => 'success',
        'guestUserId' => $guestUserId
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to create guest: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
