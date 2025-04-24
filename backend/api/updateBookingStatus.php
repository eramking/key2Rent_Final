<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed. Only POST requests are accepted.']);
    exit;
}

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'key2rent';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if ($data === null || !isset($data['id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input. Missing id or status.']);
    $conn->close();
    exit;
}

$bookingId = $data['id'];
$newStatus = $data['status'];

if (!is_numeric($bookingId) || empty(trim($newStatus))) {
     http_response_code(400);
     echo json_encode(['error' => 'Invalid input format for id or status.']);
     $conn->close();
     exit;
}

$sql = "UPDATE bookings SET status = ? WHERE id = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param("si", $newStatus, $bookingId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        http_response_code(200);
        echo json_encode(['message' => 'Booking status updated successfully.']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Booking not found or status already set to the target value.']);
    }
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to execute statement: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>