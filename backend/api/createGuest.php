<?php
// createGuest.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(0); }
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['guestName']) || empty(trim($data['guestName']))) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Guest name is required.'
    ]);
    exit;
}

$guestName = trim($data['guestName']);
$guestAvatar = (isset($data['guestAvatar']) && !empty(trim($data['guestAvatar']))) ? trim($data['guestAvatar']) : "default-avatar.png";

// สำหรับ Guest account ให้ Email เป็น NULL หรือค่า dummy ที่ไม่ซ้ำกัน
$email = null; // หรือเช่น "guest_" . time() . "@example.com";

include '../config/db.php';

// สมมุติว่าตาราง users มีคอลัมน์ UserID, Username, Avatar, Email และ isGuest
$sql = "INSERT INTO users (Username, Avatar, Email, isGuest) VALUES (:guestName, :guestAvatar, :email, 1)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        ':guestName' => $guestName,
        ':guestAvatar' => $guestAvatar,
        ':email' => $email
    ]);
    $guestUserId = $pdo->lastInsertId();
    echo json_encode([
        'status'      => 'success',
        'guestUserId' => $guestUserId
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to create guest: ' . $e->getMessage()
    ]);
}
?>
