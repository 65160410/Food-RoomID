<?php
header('Content-Type: application/json');
// รวมไฟล์เชื่อมต่อฐานข้อมูล
include '../config/db.php';

// ตรวจสอบว่ามีการส่ง restaurantID หรือไม่
if (!isset($_GET['restaurantID']) || empty($_GET['restaurantID'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing restaurantID'
    ]);
    exit;
}

$restaurantID = $_GET['restaurantID'];

try {
    // ดึงข้อมูลเมนูอาหารจากตาราง menuitems
    $stmt = $pdo->prepare("SELECT RestaurantID, ItemName, Description, Price, ImageURL
                           FROM menuitems 
                           WHERE RestaurantID = :restaurantID");
    $stmt->bindParam(':restaurantID', $restaurantID);
    $stmt->execute();
    
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ตัวอย่างการแปลงข้อมูลรูปภาพ (ถ้ามีคอลัมน์ Image เก็บข้อมูล BLOB)
    foreach ($menuItems as &$item) {
        if (isset($item['Image'])) {
            // ถ้าข้อมูลในคอลัมน์ Image เก็บเป็น BLOB หรือ mediumtext ให้แปลงเป็น Base64
            $item['imageBase64'] = base64_encode($item['Image']);
            unset($item['Image']);
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'menuItems' => $menuItems
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
