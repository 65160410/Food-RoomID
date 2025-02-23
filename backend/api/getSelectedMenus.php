<?php
// เปิดการแสดง error (สำหรับการพัฒนา)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// เพิ่ม header สำหรับ CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ถ้าเป็น preflight request (OPTIONS) ให้หยุดการทำงาน
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

// รับ roomID จาก query string
$roomID = isset($_GET['roomID']) ? $_GET['roomID'] : '';

try {
    // ดึงข้อมูลเมนูที่สมาชิกเลือกไว้ใน room นั้น
    $sql = "SELECT * FROM selected_menus WHERE roomID = :roomID ORDER BY selected_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':roomID' => $roomID]);
    $selectedMenus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'selectedMenus' => $selectedMenus
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
