<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include '../db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

$fullName = $data['fullName'];
$email = $data['email'];
$password = $data['password'];
$confirmPassword = $data['confirmPassword'];

if ($password !== $confirmPassword) {
    echo json_encode(["error" => "Passwords do not match"]);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["error" => "Failed to prepare statement: " . $conn->error]);
    exit;
}

$stmt->bind_param("sss", $fullName, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["message" => "User registered successfully"]);
} else {
    if ($stmt->errno === 1062) {
        echo json_encode(["error" => "Email is already registered"]);
    } else {
        echo json_encode(["error" => "Failed to register user: " . $stmt->error]);
    }
}

$stmt->close();
$conn->close();
?>