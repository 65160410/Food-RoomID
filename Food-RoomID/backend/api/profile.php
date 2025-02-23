<?php
// profile.php
// ไม่มี whitespace หรือ BOM ก่อน <?php>

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

// ล้าง output buffer หากมีข้อมูลเกินมา
if (ob_get_length()) {
    ob_clean();
}

// ตรวจสอบว่ามี userId ใน GET parameter
if (!isset($_GET['userId'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'User id is required.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$userId = intval($_GET['userId']);
include '../config/db.php';

// Query ดึงข้อมูลผู้ใช้
$sql = "SELECT UserID, Username, Email, Preferences, DietaryRestrictions FROM users WHERE UserID = :userId";
$stmt = $pdo->prepare($sql);
$stmt->execute([':userId' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status' => 'success',
        'user'   => $user
    ], JSON_UNESCAPED_UNICODE);
} else {
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        'status'  => 'error',
        'message' => 'User not found.'
    ], JSON_UNESCAPED_UNICODE);
}

exit;
?>
