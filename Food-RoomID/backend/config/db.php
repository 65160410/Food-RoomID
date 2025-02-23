<?php
// db.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host     = 'localhost';  // หรือ IP/hostname ของโฮสต์
$dbname   = 's65160410';
$username = 's65160410';
$password = 'AcKbyJsV';          

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo"การเชื่อมต่อสำเร็จ";
} catch (PDOException $e) {
    die('การเชื่อมต่อฐานข้อมูลล้มเหลว: ' . $e->getMessage());
}
?>
