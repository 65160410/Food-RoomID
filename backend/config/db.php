<?php
// db.php - การเชื่อมต่อฐานข้อมูล

$host = 'localhost';  // ชื่อโฮสต์ของฐานข้อมูล
$dbname = 'foodmeet';  // ชื่อฐานข้อมูล
$username = 'root';  // ชื่อผู้ใช้
$password = '';  // รหัสผ่าน

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'เชื่อมต่อฐานข้อมูลสำเร็จ';
} catch (PDOException $e) {
    echo 'การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $e->getMessage();
}
?>
