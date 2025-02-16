<?php
// room.php - จัดการห้อง

require_once '../config/db.php';  // เรียกใช้การเชื่อมต่อฐานข้อมูล

// ตัวอย่าง API สำหรับการดึงห้องทั้งหมด
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $pdo->query("SELECT * FROM rooms");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $rooms]);
}

// ตัวอย่าง API สำหรับการสร้างห้องใหม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'] ?? '';

    if (!empty($room_name)) {
        $stmt = $pdo->prepare("INSERT INTO rooms (room_name) VALUES (:room_name)");
        $stmt->bindParam(':room_name', $room_name);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Room created successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Room name is required']);
    }
}
?>
