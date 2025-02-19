<?php
// login.php
// เปิด Error Reporting เฉพาะช่วงพัฒนา (ใน Production ควรปิดหรือปรับลง)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตอบกลับเป็น JSON
header("Content-Type: application/json");

// อนุญาต CORS (กรณี Frontend & Backend คนละโดเมน)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// ถ้าเป็น preflight (OPTIONS) ให้หยุดทำงานทันที
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// อ่านข้อมูล JSON จาก body
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// ตรวจสอบว่ามี email / password
if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode([
        "status"  => "error",
        "message" => "Email and Password are required"
    ]);
    exit;
}

// เชื่อมต่อฐานข้อมูล (db.php ต้องมี $pdo = new PDO(...); และห้าม echo อย่างอื่น)
include '../config/db.php';

// เก็บค่าจากฟอร์ม
$email    = $data['email'];
$password = $data['password']; // Plain text password จากฟอร์ม

// 1) ค้นหา user ด้วย email + password แบบตรง ๆ (Plain text)
$sql  = "SELECT * FROM users 
         WHERE email = :email 
           AND password = :password 
         LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'email'    => $email,
    'password' => $password
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// 2) เช็คผลลัพธ์
if ($user) {
    echo json_encode([
        "status"  => "success",
        "message" => "Login successful",
        "userID"  => $user['UserID'] // สมมติว่าในฐานข้อมูลคอลัมน์นี้ชื่อ UserID
    ]);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid email or password"
    ]);
}


exit;
