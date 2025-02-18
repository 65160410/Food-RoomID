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

// ตัวอย่างเชื่อมต่อ DB
include '../config/db.php';

$sql  = "SELECT * FROM users WHERE email = :email LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // หากตอนสมัคร เก็บรหัสผ่านด้วย password_hash()
    if (password_verify($data['password'], $user['password'])) {
        // ล็อกอินสำเร็จ
        echo json_encode([
            "status"  => "success",
            "message" => "Login successful"
        ]);
    } else {
        // รหัสผ่านผิด
        echo json_encode([
            "status"  => "error",
            "message" => "Invalid email or password"
        ]);
    }
} else {
    // ไม่พบ email นี้
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid email or password"
    ]);
}
exit;
