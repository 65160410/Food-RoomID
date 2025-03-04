<?php
// login.php
// ไม่มี whitespace หรือ BOM ก่อน <?php>

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตั้งค่า CORS และ Content-Type ให้เป็น JSON พร้อม charset UTF-8
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}
header("Content-Type: application/json; charset=utf-8");

// ล้าง output buffer หากมีข้อมูลเกินมา
if (ob_get_length()) {
    ob_clean();
}

// อ่านข้อมูล JSON จาก body
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// ตรวจสอบว่ามี email / password
if (!isset($data['email']) || !isset($data['password'])) {
    echo json_encode([
        "status"  => "error",
        "message" => "Email and Password are required"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// เชื่อมต่อฐานข้อมูล (ตรวจสอบว่าไฟล์ db.php ไม่มี output ใดๆ)
include '../config/db.php';

// เก็บค่าจากฟอร์ม
$email    = $data['email'];
$password = $data['password']; // Plain text password จากฟอร์ม

// 1) ค้นหา user ด้วย email และ password แบบตรง ๆ (Plain text)
$sql  = "SELECT * FROM users WHERE email = :email AND password = :password LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'email'    => $email,
    'password' => $password
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ล้าง output buffer อีกครั้งก่อนส่ง JSON
if (ob_get_length()) {
    ob_clean();
}

// 2) ตรวจสอบผลลัพธ์และส่ง JSON response
if ($user) {
    echo json_encode([
        "status"  => "success",
        "message" => "Login successful",
        "userID"  => $user['UserID']
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid email or password"
    ], JSON_UNESCAPED_UNICODE);
}

exit;
?>
