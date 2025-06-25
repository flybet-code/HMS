<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pharmacyTransactionsSql = "SELECT patient_id, patient_name, doctor_name, medications, price, status FROM medication WHERE status = 'Approved'";
$result = $conn->query($pharmacyTransactionsSql);
$transactions = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($transactions);

$conn->close();
?>