<?php
// Include your database connection
include('db_connection.php');

// Check if the data is received
if (isset($_POST['patient_id']) && isset($_POST['doctor_id'])) {
    $patientId = $_POST['patient_id'];
    $doctorId = $_POST['doctor_id'];

    // Update the patient's record with the assigned doctor
    $sql = "UPDATE patients SET assigned_doctor_id = ? WHERE patient_id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind the parameters to the SQL query
        mysqli_stmt_bind_param($stmt, "si", $doctorId, $patientId);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            // Success message
            echo "Patient has been successfully assigned to Doctor ID: " . $doctorId;
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}

// Close the database connection
mysqli_close($conn);
?>
