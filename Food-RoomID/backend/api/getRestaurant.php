<?php
header("Content-Type: application/json; charset=utf-8");

// ปรับ path ให้ถูกต้อง หาก db.php อยู่ในตำแหน่งอื่น
include '../config/db.php';

if (!isset($_GET['restaurantID'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing restaurantID parameter."
    ]);
    exit;
}

$restaurantID = $_GET['restaurantID'];

try {
    // ดึงข้อมูลร้านอาหารจากตาราง restaurants
    $stmt = $pdo->prepare("SELECT * FROM restaurants WHERE RestaurantID = :restaurantID");
    $stmt->execute(['restaurantID' => $restaurantID]);
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($restaurant) {
        // ถ้ามีข้อมูลรูปในคอลัมน์ image ให้แปลงเป็น Base64
        if (!empty($restaurant['image'])) {
            $restaurant['image'] = base64_encode($restaurant['image']);
        }

        // ดึงรายการเมนูอาหารจากตาราง menuitems ที่มี RestaurantID ตรงกัน
        $stmt2 = $pdo->prepare("SELECT ItemName FROM menuitems WHERE RestaurantID = :restaurantID");
        $stmt2->execute(['restaurantID' => $restaurantID]);
        $menus = $stmt2->fetchAll(PDO::FETCH_COLUMN);

        $restaurant['menus'] = $menus;
        $restaurant['status'] = "success";

        echo json_encode($restaurant);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Restaurant not found."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>
