<?php
// deleteMenu.php
// ไม่มี whitespace หรือ BOM ก่อน <?php>

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ล้าง output buffer หากมีข้อมูลเกินมา
if (ob_get_length()) {
    ob_clean();
}

// รับ JSON จาก body
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode([
        "status" => "error",
        "message" => "No input data received"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ดึง id ของเมนูจากข้อมูล
$menuId = isset($data['id']) ? intval($data['id']) : null;

if (!$menuId) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required parameter: id"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

include '../config/db.php';

try {
    $sql = "DELETE FROM selected_menus WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $menuId]);

    // ล้าง output buffer อีกครั้งก่อนส่ง response
    if (ob_get_length()) {
        ob_clean();
    }

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "status"  => "success",
            "message" => "Menu deleted successfully."
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status"  => "error",
            "message" => "No menu found with the given id."
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        "status"  => "error",
        "message" => "Failed to delete menu: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
