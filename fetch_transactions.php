<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cashier') {
    header('Location: index.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch approved transactions from laboratory_test
$laboratorySql = "SELECT patient_id, patient_name, doctor_name, test_type AS details, price, status FROM laboratory_test WHERE status = 'Approved'";
$laboratoryResult = $conn->query($laboratorySql);

// Fetch approved transactions from medication
$medicationSql = "SELECT patient_id, patient_name, doctor_name, medications AS details, price, status FROM medication WHERE status = 'Approved'";
$medicationResult = $conn->query($medicationSql);

$allPayments = [];

if ($laboratoryResult) {
    while ($row = $laboratoryResult->fetch_assoc()) {
        $allPayments[] = $row;
    }
}

if ($medicationResult) {
    while ($row = $medicationResult->fetch_assoc()) {
        $allPayments[] = $row;
    }
}

echo json_encode($allPayments); // Return the results as JSON
$conn->close();
?>