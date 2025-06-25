<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch approved transactions from laboratory_test
$laboratoryTransactionsSql = "SELECT patient_id, patient_name, doctor_name, test_type, price, status FROM laboratory_test WHERE status = 'Approved'";
$laboratoryTransactionsResult = $conn->query($laboratoryTransactionsSql);

// Fetch approved transactions from medication
$pharmacyTransactionsSql = "SELECT patient_id, patient_name, doctor_name, medications, price, status FROM medication WHERE status = 'Approved'";
$pharmacyTransactionsResult = $conn->query($pharmacyTransactionsSql);

// Combine results into one array
$transactions = [];
if ($laboratoryTransactionsResult) {
    while ($row = $laboratoryTransactionsResult->fetch_assoc()) {
        $transactions[] = $row;
    }
}
if ($pharmacyTransactionsResult) {
    while ($row = $pharmacyTransactionsResult->fetch_assoc()) {
        $transactions[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($transactions);

$conn->close();
?>