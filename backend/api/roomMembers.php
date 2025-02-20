<?php
// roomMembers.php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

// ตรวจสอบว่ามีการส่ง roomID มาหรือไม่
if (!isset($_GET['roomID'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'roomID is required.'
    ]);
    exit;
}

$roomID = trim($_GET['roomID']);

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

// ดึง room_name จากตาราง rooms โดยใช้ roomID
$sql = "SELECT room_name FROM rooms WHERE RoomID = :roomID LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([':roomID' => $roomID]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Room not found.'
    ]);
    exit;
}

$room_name = $room['room_name'];

/*
  ใช้ UNION query เพื่อดึงข้อมูล:
  - Host (ผู้สร้างห้อง) จากตาราง rooms กับ users โดยกำหนด isHost เป็น 1
  - สมาชิกจาก roommembers กับ users โดยกำหนด isHost เป็น 0 (ยกเว้น host เพื่อไม่ให้ซ้ำ)
*/
$sql = "SELECT UserID, Username, isHost FROM (
    SELECT u.UserID, u.Username, 1 AS isHost
    FROM rooms r
    JOIN users u ON u.UserID = r.CreatedBy
    WHERE r.room_name = :room_name

    UNION

    SELECT u.UserID, u.Username, 0 AS isHost
    FROM rooms r
    JOIN roommembers rm ON r.RoomID = rm.RoomID
    JOIN users u ON u.UserID = rm.UserID
    WHERE r.room_name = :room_name2 AND rm.UserID <> r.CreatedBy
) AS members";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':room_name'  => $room_name,
    ':room_name2' => $room_name
]);

$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($members && count($members) > 0) {
    echo json_encode([
        'status'    => 'success',
        'members'   => $members,
        'room_name' => $room_name // ส่งกลับ room_name เพื่อใช้แสดงผลในฝั่ง client
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'No members found for this room.'
    ]);
}
?>
