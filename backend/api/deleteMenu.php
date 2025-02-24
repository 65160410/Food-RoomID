<?php
// deleteMenu.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตั้งค่า CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// รับ JSON จาก body
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "No input data received"
    ]);
    exit;
}

// ดึง id ของเมนูจากข้อมูล
$menuId = isset($data['id']) ? intval($data['id']) : null;

if (!$menuId) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required parameter: id"
    ]);
    exit;
}

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

try {
    // ลบเมนูในตาราง selected_menus ตาม id
    $sql = "DELETE FROM selected_menus WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $menuId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Menu deleted successfully."
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No menu found with the given id."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
