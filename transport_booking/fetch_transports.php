<?php
include 'db.php';

$sql = "SELECT * FROM transports";
$result = $conn->query($sql);

$transports = array();
while($row = $result->fetch_assoc()){
    $transports[] = $row;
}

echo json_encode($transports);
?>