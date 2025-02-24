<?php
// db.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host     = 'localhost';  // หรือ IP/hostname ของโฮสต์
$dbname   = 'foodmeet';
$username = 'root';
$password = '';           // ใน XAMPP ส่วนใหญ่ไม่ใส่รหัสผ่าน

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ไม่ต้องแสดงผลข้อความเมื่อเชื่อมต่อสำเร็จ
} catch (PDOException $e) {
    die('การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $e->getMessage());
}
?>
