<?php
header('Content-Type: application/json');
include '../config/db.php';

// ตรวจสอบ roomID ที่ส่งเข้ามา
if (!isset($_GET['roomID'])) {
    echo json_encode(["error" => "roomID not provided"]);
    exit;
}
$roomID = $_GET['roomID'];

try {
    // ดึง vote counts จากตาราง votes โดยกรองตาม roomID
    $stmt = $pdo->prepare("SELECT foodName, COUNT(*) as voteCount FROM votes WHERE roomID = ? GROUP BY foodName");
    $stmt->execute([$roomID]);
    $voteCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
    exit;
}

// Debug: ตรวจสอบข้อมูลที่ query ได้จาก DB
error_log("VoteCounts from DB: " . print_r($voteCounts, true));

// แปลงผลลัพธ์จาก DB ให้อยู่ในรูปแบบ array ของชื่ออาหารตามจำนวนโหวต
$transformedVotes = [];
foreach ($voteCounts as $row) {
    $food = $row['foodName'];
    $count = (int)$row['voteCount'];
    // Debug: ตรวจสอบแต่ละ row ที่ถูกอ่าน
    error_log("Processing food: $food, voteCount: $count");
    for ($i = 0; $i < $count; $i++) {
        $transformedVotes[] = $food;
    }
}

$payload = ['votes' => $transformedVotes];
$jsonPayload = json_encode($payload);

// Debug: แสดงค่า JSON Payload ที่จะส่งไป
error_log("JSON Payload to calculate.php: " . $jsonPayload);

// ส่งข้อมูลไปยัง API calculate.php ด้วย cURL
$ch = curl_init('https://angsila.informatics.buu.ac.th/~65160410/AJTae/calculate.php');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonPayload)
]);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(["error" => curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

// แสดงผลลัพธ์ที่ได้รับจาก API calculate.php
echo $result;
?>
