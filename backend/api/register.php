<?php
// register.php - การสมัครสมาชิก
include('../config/db.php'); // เชื่อมต่อฐานข้อมูล

// รับข้อมูลจากฟอร์ม
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm-password'];

// ตรวจสอบข้อมูลที่ได้รับ
if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// ตรวจสอบว่ารหัสผ่านและยืนยันรหัสผ่านตรงกันหรือไม่
if ($password !== $confirm_password) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

// การเข้ารหัสรหัสผ่านก่อนเก็บลงในฐานข้อมูล
$password_hashed = password_hash($password, PASSWORD_DEFAULT);

// สร้างคำสั่ง SQL เพื่อตรวจสอบว่ามีอีเมลนี้อยู่ในฐานข้อมูลหรือไม่
$sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // หากพบว่าอีเมลซ้ำ
    echo json_encode(["status" => "error", "message" => "Email is already registered"]);
    exit;
}

// สร้างคำสั่ง SQL เพื่อบันทึกผู้ใช้ใหม่
$sql = "INSERT INTO users (Email, Username, Password) VALUES (:email, :fullname, :password)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'email' => $email,
    'fullname' => $fullname,
    'password' => $password_hashed
]);

?>
