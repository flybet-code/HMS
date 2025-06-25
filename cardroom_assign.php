<?php
session_start();
include 'db_connection.php';

if (isset($_POST['assign_patient'])) {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];  // Assume doctor ID is provided in a dropdown
    
    // Update patient table with doctor assignment
    $query = "UPDATE patients SET doctor_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $doctor_id, $patient_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Patient successfully assigned to the doctor.";
    } else {
        $_SESSION['error'] = "Failed to assign patient.";
    }

    header("Location: cardroom_dashboard.php");
}
?>
