<?php
header('Content-Type: application/json');
// getRestaurantDetails.php
include '../config/db.php';

// ตรวจสอบว่ามีการส่ง restaurantID มาหรือไม่
if (!isset($_GET['restaurantID']) || empty($_GET['restaurantID'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing restaurantID'
    ]);
    exit;
}

$restaurantID = $_GET['restaurantID'];

try {
    // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลร้านอาหารตาม restaurantID
    $stmt = $pdo->prepare("SELECT RestaurantID, RestaurantName, Image, AverageRating, CuisineType,Address FROM restaurants WHERE RestaurantID = :restaurantID");
    $stmt->bindParam(':restaurantID', $restaurantID);
    $stmt->execute();
    
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($restaurant) {
        // สมมติว่าใน DB คอลัมน์ Image ถูกเก็บในรูปแบบ mediumtext 
        // ซึ่งอาจเป็นข้อมูล Base64 string อยู่แล้ว หรือเป็นข้อมูลไบนารีที่ถูกแปลงเป็น string
        $imgData = $restaurant['Image'];
        
        // ลอง decode ถ้าเป็น Base64 string (decode จะคืน false ถ้าไม่ถูกต้อง)
        $blob = base64_decode($imgData, true);
        if ($blob === false) {
            // ถ้า decode ไม่ได้ ให้ถือว่าข้อมูลใน DB เป็นข้อมูลไบนารีจริง ๆ อยู่แล้ว
            $blob = $imgData;
        }
        
        // จากนั้นเข้ารหัสให้กลับเป็น Base64 เพื่อส่งไปให้ client
        $restaurant['imageBase64'] = base64_encode($blob);
        unset($restaurant['Image']);

        echo json_encode([
            'status' => 'success',
            'restaurant' => $restaurant
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Restaurant not found'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
