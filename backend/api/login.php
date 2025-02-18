<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");
// เปิด CORS ตามต้องการ
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// ถ้าเป็นพวก Preflight (OPTIONS) ก็ exit
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode([
        "status"  => "error",
        "message" => "Email and Password are required"
    ]);
    exit;
}

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

// ### วิธีที่ 1: เขียน SQL ค้นหา email + password ตรงกัน ###
$sql  = "SELECT * FROM users WHERE email = :email AND password = :password LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'email'    => $data['email'],
    'password' => $data['password'],
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ถ้าหาเจอ แสดงว่า email/password ตรงกัน
if ($user) {
    echo json_encode([
        "status"  => "success",
        "message" => "Login successful"
    ]);
} else {
    // ไม่พบ หรือ password ไม่ตรง
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid email or password"
    ]);
}
exit;
