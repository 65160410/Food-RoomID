<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

$action = $_POST['action'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($action === 'login') {
    // ตรวจสอบข้อมูลล็อกอิน
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode(['success' => true, 'message' => 'ล็อกอินสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง']);
    }
} elseif ($action === 'signup') {
    // ตรวจสอบการสมัครสมาชิก
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    if ($stmt->execute([$email, $hashedPassword])) {
        echo json_encode(['success' => true, 'message' => 'สมัครสมาชิกสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'สมัครสมาชิกไม่สำเร็จ']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'การดำเนินการไม่ถูกต้อง']);
}