<?php
session_start();
include('../config/db.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าได้รับข้อมูลจากฟอร์มหรือไม่
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
} else {
    echo json_encode(["status" => "error", "message" => "Email and password are required"]);
    exit;
}

// สร้างคำสั่ง SQL เพื่อตรวจสอบอีเมลและรหัสผ่าน
$sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่าเจอผู้ใช้หรือไม่
if ($user) {
    // ตรวจสอบรหัสผ่าน (หากมีการเข้ารหัส)
    if (password_verify($password, $user['password'])) {
        // เก็บข้อมูลผู้ใช้ใน session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];

        // ส่งกลับการเข้าสู่ระบบสำเร็จ
        echo json_encode(["status" => "success", "message" => "Login successful"]);
    } else {
        // รหัสผ่านไม่ถูกต้อง
        echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
    }
} else {
    // ไม่พบอีเมลในฐานข้อมูล
    echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
}
?>
