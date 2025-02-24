<?php
// searchMenuItems.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// กำหนด CORS header เพียงครั้งเดียว
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ถ้าเป็น OPTIONS request (preflight) ให้หยุดการทำงาน
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
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
    echo json_encode([
        'status' => 'success',
        'data' => $results
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
