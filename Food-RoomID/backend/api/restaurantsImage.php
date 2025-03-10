<?php
header('Content-Type: application/json; charset=utf-8');

// สมมติว่าคุณได้สร้างและกำหนดตัวแปร $pdo สำหรับการเชื่อมต่อฐานข้อมูลไว้แล้ว

include '../config/db.php';

if (isset($_GET['restaurantID'])) {
    $restaurantID = $_GET['restaurantID'];
    
    $stmt = $pdo->prepare("SELECT image FROM restaurants WHERE RestaurantID = ?");
    $stmt->execute([$restaurantID]);
    $row = $stmt->fetch();
    
    if ($row) {
        $base64Image = base64_encode($row['image']);
        echo json_encode(['image' => $base64Image]);
    } else {
        echo json_encode(['error' => 'ไม่พบข้อมูลร้านอาหารที่มี id นี้']);
    }
} else {
    echo json_encode(['error' => 'ไม่พบ parameter restaurantID']);
}
?>
