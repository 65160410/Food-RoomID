<?php
// getSelectedMenus.php

// ไม่มี whitespace หรือ BOM ก่อน <?php>

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตั้งค่า header สำหรับ CORS และ JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// หากเป็น preflight request (OPTIONS) ให้หยุดที่นี่
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ล้าง output buffer หากมีข้อมูลเกินมา
if (ob_get_length()) {
    ob_clean();
}

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

// รับ roomID จาก query string
$roomID = isset($_GET['roomID']) ? trim($_GET['roomID']) : '';

if (!$roomID) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'roomID is required.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // ดึงข้อมูลเมนูที่สมาชิกเลือกไว้ใน room นั้น
    $sql = "SELECT * FROM selected_menus WHERE roomID = :roomID ORDER BY selected_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':roomID' => $roomID]);
    $selectedMenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ล้าง output buffer อีกครั้งก่อนส่ง JSON
    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode([
        'status' => 'success',
        'selectedMenus' => $selectedMenus
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
