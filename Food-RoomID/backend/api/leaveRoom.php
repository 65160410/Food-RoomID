<?php
// leaveRoom.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}
header('Content-Type: application/json; charset=utf-8');

if (ob_get_length()) {
    ob_clean();
}

$data = json_decode(file_get_contents('php://input'), true);

$roomID   = isset($data['roomID']) ? intval($data['roomID']) : null;
$joinerId = isset($data['joinerId']) ? intval($data['joinerId']) : null;

if (!$roomID || !$joinerId) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Missing parameters: roomID and joinerId are required.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

include '../config/db.php';

// ตรวจสอบว่าห้องมีอยู่จริงหรือไม่
$sql = "SELECT * FROM rooms WHERE RoomID = :roomID";
$stmt = $pdo->prepare($sql);
$stmt->execute([':roomID' => $roomID]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Room not found.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ฟังก์ชันสำหรับลบ Guest ออกจากตาราง users
function deleteGuestFromUsers($pdo, $joinerId) {
    $sqlUser = "SELECT isGuest FROM users WHERE UserID = :joinerId LIMIT 1";
    $stmtUser = $pdo->prepare($sqlUser);
    $stmtUser->execute([':joinerId' => $joinerId]);
    $userRecord = $stmtUser->fetch(PDO::FETCH_ASSOC);
    if ($userRecord && intval($userRecord['isGuest']) === 1) {
        $stmtDeleteUser = $pdo->prepare("DELETE FROM users WHERE UserID = :joinerId");
        $stmtDeleteUser->execute([':joinerId' => $joinerId]);
    }
}

// ถ้า joinerId เป็น Host (CreatedBy) ลบห้องทันที
if ((int)$room['CreatedBy'] === $joinerId) {
    $pdo->beginTransaction();
    try {
        // ลบสมาชิกทั้งหมดในห้อง
        $stmt = $pdo->prepare("DELETE FROM roommembers WHERE RoomID = :roomID");
        $stmt->execute([':roomID' => $roomID]);

        // ลบข้อมูลห้อง
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE RoomID = :roomID");
        $stmt->execute([':roomID' => $roomID]);

        // ลบ Guest (ถ้าเป็น Guest)
        deleteGuestFromUsers($pdo, $joinerId);

        $pdo->commit();
        if (ob_get_length()) { ob_clean(); }
        echo json_encode([
            'status'  => 'success',
            'message' => 'Room deleted successfully because host left.'
        ], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        $pdo->rollBack();
        if (ob_get_length()) { ob_clean(); }
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to delete room: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    // ผู้ใช้ทั่วไป (ไม่ใช่ Host) ออกจากห้อง
    $stmt = $pdo->prepare("DELETE FROM roommembers WHERE RoomID = :roomID AND UserID = :joinerId");
    $stmt->execute([':roomID' => $roomID, ':joinerId' => $joinerId]);

    if ($stmt->rowCount() >= 0) {
        // ลบ Guest ออกจากตาราง users (ถ้าเป็น guest)
        deleteGuestFromUsers($pdo, $joinerId);

        // ตรวจสอบว่าหลังลบแล้ว มีสมาชิกเหลือไหม
        $checkSql = "SELECT COUNT(*) AS cnt FROM roommembers WHERE RoomID = :roomID";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':roomID' => $roomID]);
        $countResult = $checkStmt->fetch(PDO::FETCH_ASSOC);
        $memberCount = $countResult ? intval($countResult['cnt']) : 0;

        if ($memberCount === 0) {
            // ถ้าไม่มีสมาชิกเหลือ ลบห้องออกด้วย
            // (หากมีตารางอื่น ๆ ที่ต้องลบข้อมูลด้วย เช่น selectedMenus ให้ลบตรงนี้ได้)
            $delRoomStmt = $pdo->prepare("DELETE FROM rooms WHERE RoomID = :roomID");
            $delRoomStmt->execute([':roomID' => $roomID]);

            // ส่งข้อความกลับ
            if (ob_get_length()) { ob_clean(); }
            echo json_encode([
                'status'  => 'success',
                'message' => 'Left room successfully. Room deleted because no members left.'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            // ยังมีสมาชิกเหลือในห้อง
            if (ob_get_length()) { ob_clean(); }
            echo json_encode([
                'status'  => 'success',
                'message' => 'Left room successfully.'
            ], JSON_UNESCAPED_UNICODE);
        }
    } else {
        if (ob_get_length()) { ob_clean(); }
        echo json_encode([
            'status'  => 'error',
            'message' => 'Failed to leave room.'
        ], JSON_UNESCAPED_UNICODE);
    }
}
