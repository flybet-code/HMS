<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId = $_POST['patient_id'];

    // Update the status in the laboratory_test table to 'Approved'
    $updateSql = "UPDATE laboratory_test SET status = 'Approved' WHERE patient_id = ? AND status = 'Pending'";
    $stmt = $conn->prepare($updateSql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("s", $patientId);
    if (!$stmt->execute()) {
        die("Execution Error: " . $stmt->error);
    }

    // Also update the medication table if needed
    $updateSqlMed = "UPDATE medication SET status = 'Approved' WHERE patient_id = ? AND status = 'Pending'";
    $stmtMed = $conn->prepare($updateSqlMed);
    if (!$stmtMed) {
        die("SQL Error: " . $conn->error);
    }
    $stmtMed->bind_param("s", $patientId);
    $stmtMed->execute();

    echo "Payment approved"; // Response message
}

$conn->close();
?>