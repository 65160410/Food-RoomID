<?php
header('Content-Type: application/json');
require_once 'db.php'; // เชื่อมต่อฐานข้อมูล

// ฟังก์ชันสำหรับดึงข้อมูลรายละเอียดร้านอาหาร
function getRestaurantDetails($pdo, $restaurantId)
{
    try {
        // Query สำหรับดึงข้อมูลพื้นฐานของร้านอาหาร
        $stmt = $pdo->prepare("
            SELECT 
                RestaurantID, 
                RestaurantName, 
                Description, 
                AverageRating, 
                CuisineType, 
                Address 
            FROM restaurants 
            WHERE RestaurantID = :restaurant_id
        ");
        $stmt->bindParam(':restaurant_id', $restaurantId);
        $stmt->execute();
        $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$restaurant) {
            return ['status' => 'error', 'message' => 'Restaurant not found'];
        }

        // Query สำหรับดึงเมนูยอดนิยมของร้านอาหาร
        $menuStmt = $pdo->prepare("
            SELECT ItemID, ItemName, Price, ImageURL 
            FROM menuitems 
            WHERE RestaurantID = :restaurant_id
        ");
        $menuStmt->bindParam(':restaurant_id', $restaurantId);
        $menuStmt->execute();
        $popularDishes = $menuStmt->fetchAll(PDO::FETCH_ASSOC);

        // ส่งข้อมูลกลับในรูปแบบ JSON
        return [
            'status' => 'success',
            'data' => [
                'restaurant' => $restaurant,
                'popular_dishes' => $popularDishes
            ]
        ];
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return ['status' => 'error', 'message' => 'Database error'];
    }
}

// ตรวจสอบว่ามีการส่งคำขอ GET มาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $restaurantId = filter_var($_GET['id'], FILTER_VALIDATE_INT); // รับ ID ร้านอาหารจาก Query String

    if (!$restaurantId) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid restaurant ID']);
        exit;
    }

    // เรียกฟังก์ชันเพื่อดึงข้อมูลร้านอาหาร
    $response = getRestaurantDetails($pdo, $restaurantId);
    echo json_encode($response);
    exit;
}
