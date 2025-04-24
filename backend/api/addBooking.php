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

$customer_name = $data['fullName'];
$phone_number = $data['phoneNumber'];
$pickup_location = $data['pickupLocation'];
$dropoff_location = $data['dropoffLocation'];
$moving_date = $data['movingDate'];
$items = json_encode($data['items']);
$price = $data['price'];

$sql = "INSERT INTO bookings (customer_name, phone_number, pickup_location, dropoff_location, moving_date, items, price, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssd", $customer_name, $phone_number, $pickup_location, $dropoff_location, $moving_date, $items, $price);

if ($stmt->execute()) {
    echo json_encode(["message" => "Booking added successfully"]);
} else {
    echo json_encode(["error" => "Failed to add booking: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>