<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user bookings
$user_id = $_SESSION['user_id'];
$bookings_sql = "SELECT b.*, t.type, t.name, t.source, t.destination, t.travel_date, t.travel_time 
                 FROM bookings b 
                 JOIN transports t ON b.transport_id = t.id 
                 WHERE b.user_id = ? 
                 ORDER BY b.booking_date DESC";
$bookings_stmt = $conn->prepare($bookings_sql);
$bookings_stmt->bind_param("i", $user_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();

// Get booking stats
$stats_sql = "SELECT 
                COUNT(*) as total_bookings,
                SUM(seats_booked) as total_seats,
                SUM(total_price) as total_spent
              FROM bookings 
              WHERE user_id = ?";
$stats_stmt = $conn->prepare($stats_sql);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TransportPro</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="index.html" class="logo">
            <i class="fas fa-bus"></i> TransportPro
        </a>
        <div class="nav-links">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="#bookings"><i class="fas fa-ticket-alt"></i> My Bookings</a>
            <a href="#transports"><i class="fas fa-bus"></i> Available</a>
            <a href="admin_dashboard.php"><i class="fas fa-cog"></i> Admin</a>
            <a href="logout.php" class="btn btn-primary" style="background: #f56565;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container">
        <!-- Welcome Header -->
        <div class="dashboard-header">
            <div class="user-info">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
                <div>
                    <h2>Welcome back, <?php echo $_SESSION['user_name']; ?>!</h2>
                    <p><?php echo $_SESSION['user_email']; ?></p>
                </div>
            </div>
            
            <div class="stats">
                <div class="stat-card">
                    <i class="fas fa-ticket-alt"></i>
                    <h3><?php echo $stats_result['total_bookings'] ?? 0; ?></h3>
                    <p>Total Bookings</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-chair"></i>
                    <h3><?php echo $stats_result['total_seats'] ?? 0; ?></h3>
                    <p>Seats Booked</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-dollar-sign"></i>
                    <h3>$<?php echo $stats_result['total_spent'] ?? '0.00'; ?></h3>
                    <p>Total Spent</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h3><?php echo date('d M Y'); ?></h3>
                    <p>Today's Date</p>
                </div>
            </div>
        </div>

        <!-- Available Transports -->
        <section id="transports" style="margin: 40px 0;">
            <h2><i class="fas fa-bus"></i> Available Transports</h2>
            <div id="transports-list" style="margin-top: 20px;">
                <div class="loading" style="text-align: center; padding: 40px;">
                    <i class="fas fa-spinner fa-spin fa-3x" style="color: #667eea;"></i>
                    <p>Loading available transports...</p>
                </div>
            </div>
        </section>

        <!-- Your Bookings -->
        <section id="bookings" style="margin: 40px 0;">
            <h2><i class="fas fa-history"></i> Your Booking History</h2>
            <?php if ($bookings_result->num_rows > 0): ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Transport</th>
                                <th>Type</th>
                                <th>Route</th>
                                <th>Date & Time</th>
                                <th>Seats</th>
                                <th>Total Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($booking = $bookings_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $booking['name']; ?></strong>
                                </td>
                                <td>
                                    <span class="transport-type <?php echo $booking['type']; ?>">
                                        <i class="fas fa-<?php echo $booking['type'] == 'bus' ? 'bus' : ($booking['type'] == 'train' ? 'train' : 'plane'); ?>"></i>
                                        <?php echo ucfirst($booking['type']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo $booking['source']; ?> → <?php echo $booking['destination']; ?>
                                </td>
                                <td>
                                    <?php echo date('d M Y', strtotime($booking['travel_date'])); ?><br>
                                    <small><?php echo $booking['travel_time']; ?></small>
                                </td>
                                <td>
                                    <span class="badge"><?php echo $booking['seats_booked']; ?> seats</span>
                                </td>
                                <td>
                                    <strong style="color: #48bb78;">$<?php echo $booking['total_price']; ?></strong>
                                </td>
                                <td>
                                    <?php
                                    $travel_date = strtotime($booking['travel_date']);
                                    $today = time();
                                    if ($travel_date > $today) {
                                        echo '<span class="status upcoming">Upcoming</span>';
                                    } else {
                                        echo '<span class="status completed">Completed</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state" style="text-align: center; padding: 40px; background: #f7fafc; border-radius: 10px;">
                    <i class="fas fa-ticket-alt fa-3x" style="color: #cbd5e0; margin-bottom: 20px;"></i>
                    <h3>No Bookings Yet</h3>
                    <p>You haven't made any bookings yet. Start booking now!</p>
                    <a href="#transports" class="btn btn-primary" style="margin-top: 20px;">
                        <i class="fas fa-search"></i> Browse Transports
                    </a>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Add CSS for badges and status -->
    <style>
        .transport-type {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .transport-type.bus {
            background: #bee3f8;
            color: #2c5282;
        }
        
        .transport-type.train {
            background: #fed7d7;
            color: #742a2a;
        }
        
        .transport-type.flight {
            background: #c6f6d5;
            color: #22543d;
        }
        
        .badge {
            background: #e2e8f0;
            padding: 5px 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status.upcoming {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status.completed {
            background: #d1fae5;
            color: #065f46;
        }
    </style>

    <script src="js/main.js"></script>
    <script>
    // Fetch and display available transports
    fetch('fetch_transports.php')
        .then(response => response.json())
        .then(transports => {
            const container = document.getElementById('transports-list');
            
            if (transports.length === 0) {
                container.innerHTML = `
                    <div class="empty-state" style="text-align: center; padding: 40px; background: #f7fafc; border-radius: 10px;">
                        <i class="fas fa-bus fa-3x" style="color: #cbd5e0; margin-bottom: 20px;"></i>
                        <h3>No Transports Available</h3>
                        <p>Check back later for available transports.</p>
                    </div>
                `;
                return;
            }
            
            let html = '<div class="transport-cards">';
            
            transports.forEach(transport => {
                const icon = transport.type === 'bus' ? 'bus' : 
                            transport.type === 'train' ? 'train' : 'plane';
                
                html += `
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-${icon}"></i>
                        <h3>${transport.name}</h3>
                        <small>${transport.type.toUpperCase()}</small>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom: 15px;">
                            <p><i class="fas fa-route"></i> ${transport.source} → ${transport.destination}</p>
                            <p><i class="fas fa-calendar"></i> ${transport.travel_date}</p>
                            <p><i class="fas fa-clock"></i> ${transport.travel_time}</p>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; margin: 15px 0;">
                            <div>
                                <strong style="font-size: 24px; color: #48bb78;">$${transport.price}</strong>
                                <p style="font-size: 12px; color: #718096;">per seat</p>
                            </div>
                            <div>
                                <strong style="font-size: 20px;">${transport.seats}</strong>
                                <p style="font-size: 12px; color: #718096;">seats left</p>
                            </div>
                        </div>
                        
                        <form action="book.php" method="POST" onsubmit="return validateBooking(this)">
                            <input type="hidden" name="transport_id" value="${transport.id}">
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <div style="flex: 1;">
                                    <input type="number" 
                                           name="seats" 
                                           min="1" 
                                           max="${transport.seats}" 
                                           value="1" 
                                           style="width: 100%; padding: 10px;"
                                           onchange="updatePrice(this, ${transport.price})">
                                </div>
                                <div style="flex: 1; text-align: center;">
                                    <strong id="price-${transport.id}">$${transport.price}</strong>
                                    <p style="font-size: 12px;">Total</p>
                                </div>
                               