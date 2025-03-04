<?php
// searchMenuItems.php

// ไม่มี whitespace หรือ BOM ก่อน <?php>
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ตั้งค่า CORS และ Content-Type ให้เป็น UTF-8
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// หากเป็น preflight request (OPTIONS) ให้หยุดการทำงานทันที
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ล้าง output buffer หากมีข้อมูลเกินมา
if (ob_get_length()) {
    ob_clean();
}

// รับค่า q จาก query string หรือ JSON payload
$q = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = isset($_GET['q']) ? trim($_GET['q']) : "";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $q = isset($data['q']) ? trim($data['q']) : "";
}

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

try {
    if ($q === "") {
        $sql = "SELECT * FROM menuitems LIMIT 20";
        $stmt = $pdo->query($sql);
    } else {
        $sql = "SELECT * FROM menuitems WHERE ItemName LIKE :search LIMIT 20";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':search' => "%$q%"]);
    }
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status' => 'success',
        'data' => $results
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
