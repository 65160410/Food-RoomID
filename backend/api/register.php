<?php
// เปิด error reporting ช่วงพัฒนา
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ถ้า Frontend กับ Backend อยู่คนละโดเมน/พอร์ต ต้องตั้ง CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit; // preflight request จบได้เลย
}

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

// รับ raw data ที่ส่งมาเป็น JSON
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// ตรวจสอบคีย์ต่าง ๆ
$fullname = $data['fullname'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$confirm = $data['confirm-password'] ?? '';

// ตรวจสอบความถูกต้อง
if (empty($fullname) || empty($email) || empty($password) || empty($confirm)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}
if ($password !== $confirm) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

// เข้ารหัสรหัสผ่าน
$password_hashed = password_hash($password, PASSWORD_DEFAULT);

// ตรวจสอบอีเมลซ้ำ
$sql = "SELECT * FROM users WHERE Email = :email LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(["status" => "error", "message" => "Email is already registered"]);
    exit;
}

// สร้างผู้ใช้ใหม่
$sql = "INSERT INTO users (Email, Username, Password) VALUES (:email, :fullname, :password)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'email' => $email,
    'fullname' => $fullname,
    'password' => $password_hashed
]);

// สมัครสำเร็จ
echo json_encode(["status" => "success", "message" => "Registration successful"]);
exit;
