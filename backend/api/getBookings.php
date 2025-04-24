<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include '../db_connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM bookings";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$bookings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items = json_decode($row["items"], true);

        $bookings[] = [
            "id" => $row["id"],
            "customer_name" => $row["customer_name"],
            "phone_number" => $row["phone_number"],
            "pickup_location" => $row["pickup_location"],
            "dropoff_location" => $row["dropoff_location"],
            "moving_date" => $row["moving_date"],
            "items" => $items,
            "price" => $row["price"],
            "status" => $row["status"],
            "created_at" => $row["created_at"],
            "updated_at" => $row["updated_at"]
        ];
    }
}

if (empty($bookings)) {
    echo json_encode(["message" => "No bookings found"]);
    exit;
}

header('Content-Type: application/json');
echo json_encode($bookings);

$conn->close();