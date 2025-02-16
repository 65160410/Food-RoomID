<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $username = $data['username'];
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (Username, Email, Password) VALUES ('$username', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        // ส่งข้อมูล JSON กลับไปยัง frontend
        echo json_encode(["success" => true, "message" => "Sign up successful!"]);
    } else {
        // ส่งข้อผิดพลาดกลับไปยัง frontend
        echo json_encode(["success" => false, "error" => "Error: " . $conn->error]);
    }
} else {
    // หากไม่ใช่ POST request
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}

$conn->close();
?>