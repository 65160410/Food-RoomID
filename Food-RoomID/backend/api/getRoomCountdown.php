<?php
// getRoomCountdown.php

// ปิดการแสดงผล error (สำหรับ production; ถ้าพัฒนาอยู่ อาจเปิดไว้เพื่อตรวจสอบ)
ini_set('display_errors', 0);
error_reporting(0);

// เริ่ม output buffering
ob_start();

$roomID = $_GET['roomID'];

// ใช้งานไฟล์ db.php ที่ใช้ PDO
include '../config/db.php';

try {
    // ค้นหาค่า countdownStartTime จากตาราง rooms
    $stmt = $pdo->prepare("SELECT countdownStartTime FROM rooms WHERE roomID = ?");
    $stmt->execute([$roomID]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        // พบแถวในฐานข้อมูล
        if ($result['countdownStartTime'] !== null) {
            // ถ้ามีค่า countdownStartTime อยู่แล้ว (ไม่เป็น NULL) ให้ใช้ค่านั้น
            $countdownStartTime = $result['countdownStartTime'];
        } else {
            // ถ้ามีแถวแต่คอลัมน์เป็น NULL ให้ตั้งค่าใหม่แล้วอัปเดต
            $countdownStartTime = round(microtime(true) * 1000);
            $updateStmt = $pdo->prepare("UPDATE rooms SET countdownStartTime = ? WHERE roomID = ?");
            $updateStmt->execute([$countdownStartTime, $roomID]);
        }
    } else {
        // ไม่พบแถวใดในตาราง rooms ให้ทำการ INSERT
        $countdownStartTime = round(microtime(true) * 1000);
        $insertStmt = $pdo->prepare("INSERT INTO rooms (roomID, countdownStartTime) VALUES (?, ?)");
        $insertStmt->execute([$roomID, $countdownStartTime]);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
    ob_end_flush();
    exit;
}

header('Content-Type: application/json');
echo json_encode(["countdownStartTime" => $countdownStartTime]);

ob_end_flush();
?>
