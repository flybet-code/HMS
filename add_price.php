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
    $price = $_POST['price'];
    $type = $_POST['type']; // 'lab' or 'pharmacy'

    // Update the price in the laboratory_test table if type is 'lab'
    if ($type === 'lab') {
        $updateSql = "UPDATE laboratory_test SET price = ? WHERE patient_id = ? AND status = 'Pending'";
        $stmt = $conn->prepare($updateSql);
        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }
        $stmt->bind_param("is", $price, $patientId);
        if (!$stmt->execute()) {
            die("Execution Error: " . $stmt->error);
        }
    } elseif ($type === 'pharmacy') {
        // Update the price in the medication table if type is 'pharmacy'
        $updateSql = "UPDATE medication SET price = ? WHERE patient_id = ? AND status = 'Pending'";
        $stmt = $conn->prepare($updateSql);
        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }
        $stmt->bind_param("is", $price, $patientId);
        if (!$stmt->execute()) {
            die("Execution Error: " . $stmt->error);
        }
    }

    echo "Price added"; // Response message
}

$conn->close();
?>