<?php
// เปิด error reporting ช่วงพัฒนา (ควรปิดหรือปรับเป็น error_reporting(0) ใน production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ถ้า Frontend กับ Backend อยู่คนละโดเมน/พอร์ต ต้องตั้ง CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// ตรวจสอบว่าเป็น Preflight (OPTIONS) หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit; // ถ้าเป็น OPTIONS ให้จบได้เลย
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

// ตรวจสอบความถูกต้องเบื้องต้น
if (empty($fullname) || empty($email) || empty($password) || empty($confirm)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

if ($password !== $confirm) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

// เปลี่ยนเป็นเก็บรหัสผ่านแบบ Plain Text (ไม่เข้ารหัส) **ไม่ปลอดภัย**
// $password_hashed = password_hash($password, PASSWORD_DEFAULT); // ยกเลิก
$password_plain = $password;

// ตรวจสอบอีเมลซ้ำ
$sql = "SELECT * FROM users WHERE Email = :email LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(["status" => "error", "message" => "Email is already registered"]);
    exit;
}

// บันทึกผู้ใช้ใหม่ โดยเก็บรหัสผ่านเป็น plain text
$sql = "INSERT INTO users (Email, Username, Password) VALUES (:email, :fullname, :password)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'email'    => $email,
    'fullname' => $fullname,
    'password' => $password_plain
]);

// สมัครสำเร็จ
echo json_encode(["status" => "success", "message" => "Registration successful"]);
exit;
