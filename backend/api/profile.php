<?php
// profile.php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

// ตรวจสอบว่ามี userId ใน GET parameter
if (!isset($_GET['userId'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'User id is required.'
    ]);
    exit;
}

$userId = intval($_GET['userId']);
include '../config/db.php'; // ตรวจสอบ path ตรงนี้ด้วยว่าอยู่ถูกที่หรือไม่

$sql = "SELECT UserID, Username, Email, Preferences, DietaryRestrictions FROM users WHERE UserID = :userId";
$stmt = $pdo->prepare($sql);
$stmt->execute([':userId' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode([
        'status' => 'success',
        'user'   => $user
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'User not found.'
    ]);
}

?>
