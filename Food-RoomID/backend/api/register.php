<?php
// register.php
// ไม่มี whitespace หรือ BOM ก่อน <?php>

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ถ้า Frontend กับ Backend อยู่คนละโดเมน/พอร์ต ต้องตั้ง CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// ตรวจสอบว่าเป็น Preflight (OPTIONS) หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// ตั้งค่า Content-Type เป็น JSON พร้อม charset UTF-8
header('Content-Type: application/json; charset=utf-8');

// ล้าง output buffer หากมี output เกินมา
if (ob_get_length()) {
    ob_clean();
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
    echo json_encode(["status" => "error", "message" => "All fields are required"], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($password !== $confirm) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"], JSON_UNESCAPED_UNICODE);
    exit;
}

// เก็บรหัสผ่านเป็น plain text (ไม่แนะนำใน production)
$password_plain = $password;

// ตรวจสอบอีเมลซ้ำ
$sql = "SELECT * FROM users WHERE Email = :email LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode(["status" => "error", "message" => "Email is already registered"], JSON_UNESCAPED_UNICODE);
    exit;
}

// บันทึกผู้ใช้ใหม่
$sql = "INSERT INTO users (Email, Username, Password) VALUES (:email, :fullname, :password)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'email'    => $email,
    'fullname' => $fullname,
    'password' => $password_plain
]);

// ล้าง output buffer อีกครั้งก่อนส่ง response
if (ob_get_length()) {
    ob_clean();
}

// สมัครสำเร็จ
echo json_encode(["status" => "success", "message" => "Registration successful"], JSON_UNESCAPED_UNICODE);
exit;
?>
