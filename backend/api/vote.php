<?php
// vote.php

// เปิดการแสดง error สำหรับการพัฒนา (ปิดใน production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// กำหนด header สำหรับ CORS และ Content-Type เป็น JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=utf-8");

// หากเป็น preflight request (OPTIONS) ให้ตอบกลับและหยุดที่นี่
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// ฟังก์ชันสำหรับส่งผลลัพธ์ JSON และหยุดการทำงานของสคริปต์
function sendResponse($data) {
    die(json_encode($data));
}

// รับข้อมูล JSON จาก body
$rawInput = file_get_contents('php://input');
// Uncomment บรรทัดด้านล่างสำหรับ debug หากต้องการดูข้อมูล raw input
// error_log("Raw input: " . $rawInput);
$data = json_decode($rawInput, true);

// ตรวจสอบว่ามีข้อมูลครบหรือไม่ (ใช้ key ที่ Client ส่งมา)
if (
    !isset($data['userID']) || 
    !isset($data['room_name']) || 
    !isset($data['foodName']) || 
    !isset($data['vote'])
) {
    http_response_code(400);
    sendResponse(["error" => "Missing required fields."]);
}

$UserID    = intval($data['userID']);
$room_name = trim($data['room_name']);
$foodName  = trim($data['foodName']);
$vote      = trim($data['vote']);

// เชื่อมต่อฐานข้อมูล (ปรับ path ให้ถูกต้อง)
include '../config/db.php';

// ค้นหา RoomID จากตาราง rooms โดยใช้ room_name
$sql = "SELECT RoomID, CreatedBy FROM rooms WHERE room_name = :room_name";
$stmt = $pdo->prepare($sql);
$stmt->execute([':room_name' => $room_name]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    http_response_code(404);
    sendResponse(["error" => "Room not found."]);
}

$RoomID = intval($room['RoomID']);

try {
    // ตรวจสอบว่ามีโหวตสำหรับอาหารนี้ในห้องนี้โดยผู้ใช้คนนี้อยู่แล้วหรือไม่
    $sql = "SELECT * FROM votes 
            WHERE UserID = :UserID 
              AND RoomID = :RoomID 
              AND foodName = :foodName";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':UserID'   => $UserID,
        ':RoomID'   => $RoomID,
        ':foodName' => $foodName
    ]);
    $existingVote = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingVote) {
        // ถ้ามีอยู่แล้ว ให้ update vote
        $sqlUpdate = "UPDATE votes 
                      SET vote = :vote 
                      WHERE UserID = :UserID 
                        AND RoomID = :RoomID 
                        AND foodName = :foodName";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':vote'     => $vote,
            ':UserID'   => $UserID,
            ':RoomID'   => $RoomID,
            ':foodName' => $foodName
        ]);

        sendResponse([
            'hostId' => null  // สามารถปรับส่งข้อมูลเพิ่มเติมได้ตามต้องการ
        ]);
    } else {
        // ถ้ายังไม่มี ให้ insert ใหม่
        $sqlInsert = "INSERT INTO votes (UserID, RoomID, foodName, vote) 
                      VALUES (:UserID, :RoomID, :foodName, :vote)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->execute([
            ':UserID'   => $UserID,
            ':RoomID'   => $RoomID,
            ':foodName' => $foodName,
            ':vote'     => $vote
        ]);

        sendResponse([
            'hostId' => null  // สามารถปรับส่งข้อมูลเพิ่มเติมได้ตามต้องการ
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    sendResponse(["error" => "Database error: " . $e->getMessage()]);
}
?>
