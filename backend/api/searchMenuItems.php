<?php
// searchMenuItems.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

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
    // ถ้าผู้ใช้ไม่ได้พิมพ์อะไรเลย อาจให้ดึงข้อมูลทั้งหมด หรือเช็คเงื่อนไข
    if ($q === "") {
        $sql = "SELECT * FROM menuitems LIMIT 20"; // สมมติให้ดึง 20 รายการ
        $stmt = $pdo->query($sql);
    } else {
        // ค้นหาแบบ LIKE
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
