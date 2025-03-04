<?php
// selectMenu.php
// ไม่มี whitespace หรือ BOM ก่อน <?php>

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ล้าง output buffer หากมีข้อมูลเกินมา
if (ob_get_length()) {
    ob_clean();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode([
        "status"  => "error",
        "message" => "No input data received"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// รับ roomID และข้อมูลผู้ใช้ (memberID สำหรับล็อกอิน หรือ guestUserID สำหรับ guest mode)
$roomID = isset($data['roomID']) ? $data['roomID'] : null;
$memberID = isset($data['memberID']) ? $data['memberID'] : null;
$guestUserID = isset($data['guestUserID']) ? $data['guestUserID'] : null;
$ItemName = isset($data['ItemName']) ? $data['ItemName'] : null;
$description = isset($data['description']) ? $data['description'] : "";
$ImageURL = isset($data['ImageURL']) ? $data['ImageURL'] : "";

if (!$roomID || !$ItemName) {
    echo json_encode([
        "status"  => "error",
        "message" => "Missing required fields: roomID and ItemName are required"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ตรวจสอบว่ามีข้อมูลผู้ใช้หรือไม่ (ต้องมี memberID หรือ guestUserID)
if (!$memberID && !$guestUserID) {
    echo json_encode([
        "status"  => "error",
        "message" => "Missing required user information: Provide memberID or guestUserID"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// กำหนดค่า userType และ effectiveUserID โดยใช้ memberID ถ้ามี ถ้าไม่มีใช้ guestUserID
if ($memberID) {
    $effectiveUserID = $memberID;
    $userType = "login";
} else {
    $effectiveUserID = $guestUserID;
    $userType = "guest";
}

// เชื่อมต่อฐานข้อมูล
include '../config/db.php';

try {
    // ตรวจสอบว่ามีการเลือกเมนูครบ 4 เมนูใน room นี้หรือยัง
    $checkSql = "SELECT COUNT(*) as total FROM selected_menus WHERE roomID = :roomID";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':roomID' => $roomID]);
    $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['total'] >= 4) {
        echo json_encode([
            "status"  => "error",
            "message" => "เลือกเมนูครบ 4 เมนูแล้ว ไม่สามารถเลือกเพิ่มได้"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // บันทึกข้อมูลเมนูที่เลือกลงในตาราง selected_menus
    $sql = "INSERT INTO selected_menus (roomID, memberID, ItemName, description, ImageURL, userType)
            VALUES (:roomID, :memberID, :ItemName, :description, :ImageURL, :userType)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':roomID'     => $roomID,
        ':memberID'   => $effectiveUserID,
        ':ItemName'   => $ItemName,
        ':description' => $description,
        ':ImageURL'   => $ImageURL,
        ':userType'   => $userType
    ]);

    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        "status"  => "success",
        "message" => "Menu selection saved"
    ], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode([
        "status"  => "error",
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
