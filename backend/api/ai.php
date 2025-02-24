<?php
// ai.php - ฟังก์ชัน AI

// สมมุติว่าเรามีฟังก์ชันบางอย่างที่เกี่ยวข้องกับ AI
// นี่เป็นแค่ตัวอย่างที่ใช้การตอบคำถาม

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question = $_POST['question'] ?? '';

    if (!empty($question)) {
        // สมมุติว่าเรามีการตอบคำถาม AI
        $response = "คำตอบจาก AI สำหรับคำถาม: $question";

        echo json_encode(['status' => 'success', 'response' => $response]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please provide a question']);
    }
}
?>
