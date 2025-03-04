<?php
// createGuest.php

session_start();

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

// รับข้อมูลจาก JSON
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบ guestName ว่าถูกส่งมาหรือไม่
if (!isset($data['guestName']) || empty(trim($data['guestName']))) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Guest name is required.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$guestName   = trim($data['guestName']);
$guestAvatar = (isset($data['guestAvatar']) && !empty(trim($data['guestAvatar']))) 
    ? trim($data['guestAvatar']) 
    : "default-avatar.png";

// สำหรับ guest account ให้ Email เป็น NULL หรือ dummy ที่ไม่ซ้ำ
$email = null;

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

try {
    // 1) ตรวจสอบชื่อซ้ำ
    $checkSql = "SELECT COUNT(*) AS cnt FROM users 
                 WHERE Username = :guestName AND isGuest = 1";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':guestName' => $guestName]);
    $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($row && intval($row['cnt']) > 0) {
        // ถ้ามีชื่อซ้ำอยู่แล้ว
        if (ob_get_length()) {
            ob_clean();
        }
        echo json_encode([
            'status'  => 'error',
            'message' => 'มีชื่อนี้แล้ว กรุณาใช้ชื่ออื่น'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // 2) ถ้าไม่ซ้ำ ให้ทำการ Insert
    $sql = "INSERT INTO users (Username, Avatar, Email, isGuest) 
            VALUES (:guestName, :guestAvatar, :email, 1)";
    $stmt = $pdo->prepare($sql);
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
    
    // รีเซ็ต session ในกรณีที่มีข้อมูลของผู้ใช้ที่ล็อกอินอยู่
    session_unset();
    session_regenerate_id();

    // เซ็ต session ใหม่สำหรับ guest user
    $_SESSION['user'] = [
        'id'       => $guestUserId,
        'username' => $guestName,
        'avatar'   => $guestAvatar,
        'isGuest'  => true
    ];

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
