<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $transport_id = $_POST['transport_id'];
    $seats = $_POST['seats'];

    // Get transport details
    $sql = "SELECT * FROM transports WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $transport_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $transport = $result->fetch_assoc();
        
        // Check if enough seats available
        if ($seats <= $transport['seats']) {
            $total_price = $seats * $transport['price'];
            
            // Insert booking
            $insert = "INSERT INTO bookings (user_id, transport_id, seats_booked, total_price) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert);
            $insert_stmt->bind_param("iiid", $user_id, $transport_id, $seats, $total_price);
            
            if($insert_stmt->execute()){
                // Update available seats
                $update_sql = "UPDATE transports SET seats = seats - ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ii", $seats, $transport_id);
                $update_stmt->execute();
                
                echo "Booking successful! <a href='dashboard.php'>Go to Dashboard</a>";
            } else {
                echo "Error: " . $conn->error;
            }
            $insert_stmt->close();
        } else {
            echo "Not enough seats available!";
        }
    } else {
        echo "Transport not found!";
    }
    $stmt->close();
}
?>