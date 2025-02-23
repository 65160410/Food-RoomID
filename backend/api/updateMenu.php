<?php
// updateMenu.php

// เปิดแสดง error (สำหรับการพัฒนา)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตั้งค่า CORS headers และ Content-Type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: PUT, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// หากเป็น preflight request (OPTIONS) ให้หยุดการทำงานทันที
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// รับข้อมูลจาก POST (JSON)
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "No input data received"
    ]);
    exit;
}

// รับค่าจากข้อมูลที่ส่งเข้ามา
$id = isset($data['id']) ? intval($data['id']) : null;
$ItemName = isset($data['ItemName']) ? $data['ItemName'] : null;
$description = isset($data['description']) ? $data['description'] : "";
$ImageURL = isset($data['ImageURL']) ? $data['ImageURL'] : "";

// ตรวจสอบค่าที่จำเป็น: ต้องมี id และ ItemName
if (!$id || !$ItemName) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required parameters: id and ItemName are required"
    ]);
    exit;
}

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

try {
    // สร้าง SQL สำหรับ update ข้อมูลในตาราง selected_menus
    $sql = "UPDATE selected_menus 
            SET ItemName = :ItemName, 
                description = :description, 
                ImageURL = :ImageURL 
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':ItemName'    => $ItemName,
        ':description' => $description,
        ':ImageURL'    => $ImageURL,
        ':id'          => $id
    ]);

    // ตรวจสอบผลลัพธ์การอัปเดต
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status"  => "success",
            "message" => "Menu updated successfully."
        ]);
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "No record was updated. Check if the provided id exists or if data is unchanged."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ]);
}
