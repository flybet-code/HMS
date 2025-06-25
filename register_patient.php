<?php
include('db_connection.php');

$response = ["success" => false, "message" => ""];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $patient_name = $_POST['patient_name'];
    $age = $_POST['age'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $home_number = $_POST['home_number'];
    $patient_type = $_POST['patient_type'];

    $check_sql = "SELECT * FROM patient_records WHERE patient_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $patient_id);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        $response["message"] = "Patient ID already exists.";
    } else {
        $sql = "INSERT INTO patient_records (patient_id, patient_name, age, contact, address, home_number, patient_type) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissss", $patient_id, $patient_name, $age, $contact, $address, $home_number, $patient_type);

        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Patient registered successfully!";
        } else {
            $response["message"] = "Error: " . $stmt->error;
        }
    }
    echo json_encode($response);
}
?>