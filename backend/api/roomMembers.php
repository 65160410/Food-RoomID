<?php
// getRoomMembers.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

if (!isset($_GET['room_name'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'room_name is required.'
    ]);
    exit;
}

$room_name = trim($_GET['room_name']);

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

// สมมติว่าตาราง rooms มี field room_name และ roomID
// แล้วตาราง rooms มี RoomID, UserID
// และตาราง users มี UserID, Username, profileImage
$sql = "SELECT u.UserID, u.Username,
               CASE WHEN r.CreatedBy = rp.UserID THEN 1 ELSE 0 END AS isHost
        FROM rooms r
        JOIN roommembers rp ON r.RoomID = rp.RoomID
        JOIN users u ON u.UserID = rp.UserID
        WHERE r.room_name = :room_name";

$stmt = $pdo->prepare($sql);
$stmt->execute([':room_name' => $room_name]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($members) {
    echo json_encode([
        'status'  => 'success',
        'members' => $members
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'No members found for this room.'
    ]);
}
?>
