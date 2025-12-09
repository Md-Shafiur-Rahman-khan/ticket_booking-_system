<?php
session_start();
include 'db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    $name = $_POST['name'];
    $source = $_POST['source'];
    $destination = $_POST['destination'];
    $seats = $_POST['seats'];
    $price = $_POST['price'];
    $travel_date = $_POST['travel_date'];
    $travel_time = $_POST['travel_time'];

    $sql = "INSERT INTO transports (type, name, source, destination, seats, price, travel_date, travel_time) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiiss", $type, $name, $source, $destination, $seats, $price, $travel_date, $travel_time);

    if($stmt->execute()){
        echo "Transport added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
?>