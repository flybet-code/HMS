<?php
/*session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'laboratory') {
    header('Location: index.php');
    exit();
}*/

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch pending lab tests from the laboratory_test table
$sql = "SELECT id, patient_id, patient_name, doctor_name, test_type, status 
        FROM laboratory_test 
        WHERE status = 'Pending'";
$result = $conn->query($sql);

// Handle sending lab results
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_lab_results'])) {
    $patientId = $_POST['patient_id'];
    $labResult = $_POST['lab_result'];
    $testId = $_POST['test_id'];
    $doctorName = $_POST['doctor_name'];
    $testType = $_POST['test_type'];

    // Insert into laboratory_results table
    $insertSql = "INSERT INTO laboratory_results (test_id, patient_id, doctor_name, test_type, lab_results) 
                  VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    // Bind parameters excluding patient name
    $stmt->bind_param("iisss", $testId, $patientId, $doctorName, $testType, $labResult);
    if (!$stmt->execute()) {
        die("Execution Error: " . $stmt->error);
    }

    // Update the laboratory test status to 'Completed'
    $updateSql = "UPDATE laboratory_test SET status = 'Completed' WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("i", $testId);
    if (!$stmt->execute()) {
        die("Execution Error: " . $stmt->error);
    }

    header('Location: laboratory_dashboard.php'); // Redirect after submission
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background-color: #f9f9f9; display: flex; height: 100vh; }
        .sidebar { width: 250px; background-color: #3949ab; color: white; position: fixed; height: 100%; padding-top: 30px; }
        .sidebar ul { list-style: none; }
        .sidebar ul li { padding: 15px 20px; }
        .sidebar ul li a { text-decoration: none; color: white; font-size: 16px; display: block; font-weight: bold; }
        .sidebar ul li a:hover { background-color: #283593; border-radius: 5px; padding-left: 10px; }
        .main-content { margin-left: 250px; padding: 40px; width: calc(100% - 250px); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        button { padding: 10px 15px; background-color: #3949ab; color: white; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background-color: #283593; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.5); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 10px; }
    </style>
</head>
<body>

<div class="sidebar">
    <ul>
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Pending Lab Tests</a></li>
        <li><a href="#">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Laboratory Dashboard</h1>
    <h2>Pending Lab Tests</h2>
    <table>
        <thead>
            <tr>
                <th>Test ID</th>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Test Type</th>
                <th>Status</th>
                <th>Send Lab Results</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['patient_id']; ?></td>
                    <td><?php echo $row['patient_name']; ?></td>
                    <td><?php echo $row['doctor_name']; ?></td>
                    <td><?php echo $row['test_type']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <button onclick="openResultModal('<?php echo $row['id']; ?>', '<?php echo $row['patient_id']; ?>', '<?php echo $row['patient_name']; ?>', '<?php echo $row['doctor_name']; ?>', '<?php echo $row['test_type']; ?>')">Send Results</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal for entering lab results -->
<div id="resultModal" class="modal">
    <div class="modal-content">
        <span onclick="closeResultModal()" style="cursor:pointer; float:right; font-size:20px;">&times;</span>
        <form method="POST">
            <h3>Enter Lab Result for <span id="modalPatientName"></span></h3>
            <input type="hidden" name="patient_id" id="modalPatientId" />
            <input type="hidden" name="test_id" id="modalTestId" />
            <input type="hidden" name="doctor_name" id="modalDoctorName" />
            <input type="hidden" name="test_type" id="modalTestType" />
            <textarea name="lab_result" required placeholder="Enter Lab Result"></textarea>
            <button type="submit" name="send_lab_results">Send Lab Results</button>
            <button type="button" onclick="closeResultModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
function openResultModal(testId, patientId, patientName, doctorName, testType) {
    document.getElementById('modalTestId').value = testId;
    document.getElementById('modalPatientId').value = patientId;
    document.getElementById('modalPatientName').innerText = patientName; // Display patient name for reference
    document.getElementById('modalDoctorName').value = doctorName; // Set doctor name
    document.getElementById('modalTestType').value = testType; // Set test type
    document.getElementById('resultModal').style.display = 'block';
}

function closeResultModal() {
    document.getElementById('resultModal').style.display = 'none';
}
</script>

</body>
</html>

<?php
$conn->close();
?>