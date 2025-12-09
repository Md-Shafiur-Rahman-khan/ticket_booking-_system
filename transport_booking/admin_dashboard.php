<?php
session_start();
include 'db.php';

// Check if user is logged in (you might want to add admin check)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all transports
$transports_sql = "SELECT * FROM transports ORDER BY travel_date, travel_time";
$transports_result = $conn->query($transports_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>
        <p><a href="dashboard.php">← Back to User Dashboard</a></p>
        
        <h3>Add New Transport</h3>
        <form action="add_transport.php" method="POST">
            <select name="type" required>
                <option value="">Select Type</option>
                <option value="bus">Bus</option>
                <option value="train">Train</option>
                <option value="flight">Flight</option>
            </select>
            <input type="text" name="name" placeholder="Transport Name" required>
            <input type="text" name="source" placeholder="Source" required>
            <input type="text" name="destination" placeholder="Destination" required>
            <input type="number" name="seats" placeholder="Available Seats" required>
            <input type="number" step="0.01" name="price" placeholder="Price per seat" required>
            <input type="date" name="travel_date" required>
            <input type="time" name="travel_time" required>
            <input type="submit" value="Add Transport">
        </form>
        
        <h3>All Transports</h3>
        <?php if ($transports_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Route</th>
                    <th>Seats</th>
                    <th>Price</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
                <?php while($transport = $transports_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $transport['id']; ?></td>
                    <td><?php echo $transport['name']; ?></td>
                    <td><?php echo ucfirst($transport['type']); ?></td>
                    <td><?php echo $transport['source']; ?> → <?php echo $transport['destination']; ?></td>
                    <td><?php echo $transport['seats']; ?></td>
                    <td>$<?php echo $transport['price']; ?></td>
                    <td><?php echo $transport['travel_date'] . ' ' . $transport['travel_time']; ?></td>
                    <td>
                        <form action="delete_transport.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="id" value="<?php echo $transport['id']; ?>">
                            <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No transports available.</p>
        <?php endif; ?>
    </div>
</body>
</html>