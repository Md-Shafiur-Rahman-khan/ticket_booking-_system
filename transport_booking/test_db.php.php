<?php
include 'db.php';

if ($conn->ping()) {
    echo "✅ Database connected successfully!";
    
    // Test if tables exist
    $result = $conn->query("SHOW TABLES");
    echo "<br>Tables found: " . $result->num_rows;
    
    while($row = $result->fetch_array()) {
        echo "<br> - " . $row[0];
    }
} else {
    echo "❌ Connection failed: " . $conn->connect_error;
}
?>