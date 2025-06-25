<?php
include('db_connection.php'); // Include database connection

// Fetch assigned patients from the database
$sql = "SELECT id, patient_id, name, doctor_assigned, assigned_date FROM assigned_patients";
$assignedPatients = mysqli_query($conn, $sql);

if (!$assignedPatients) {
    die("Error fetching assigned patients: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients Assign Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; }
        .header { background-color: #3949ab; color: white; padding: 20px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .add-patient { margin: 20px 0; }
        .add-button { padding: 10px 15px; background-color: #3949ab; color: white; border: none; cursor: pointer; }
        .add-button:hover { background-color: #283593; }
    </style>
</head>
<body>

<div class="header">
    <h1>Patients Assign Dashboard</h1>
    <p>View and manage assigned patients.</p>
</div>

<div class="add-patient">
    <button class="add-button" onclick="addPatient()">Add Patient</button>
</div>

<table>
    <thead>
        <tr>
            <th>Assignment ID</th>
            <th>Patient ID</th>
            <th>Patient Name</th>
            <th>Assigned Doctor</th>
            <th>Assignment Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($patient = mysqli_fetch_assoc($assignedPatients)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($patient['id']); ?></td>
                <td><?php echo htmlspecialchars($patient['patient_id']); ?></td>
                <td><?php echo htmlspecialchars($patient['name']); ?></td>
                <td><?php echo htmlspecialchars($patient['doctor_assigned']); ?></td>
                <td><?php echo htmlspecialchars($patient['assigned_date']); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<script>
    function addPatient() {
        // Implement the logic to add a patient
        const patientId = prompt("Enter Patient ID:");
        const patientName = prompt("Enter Patient Name:");
        const doctorAssigned = prompt("Enter Doctor Assigned:");
        const assignedDate = prompt("Enter Assignment Date (YYYY-MM-DD):");

        if (patientId && patientName && doctorAssigned && assignedDate) {
            // Simulate adding the patient
            alert(`Patient ${patientName} has been assigned to ${doctorAssigned} on ${assignedDate}.`);
            // Here you would normally make an AJAX request to add the patient to the database
        } else {
            alert("Please provide all required information.");
        }
    }
</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>