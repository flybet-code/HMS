<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'doctor') {
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

// Fetch assigned patients
$loggedInUsername = $_SESSION['username'];
$sql = "SELECT patient_id, patient_name, age, contact, address, patient_type, status 
        FROM patient_records 
        WHERE status = 'Assigned' 
        AND doctor_assigned = (SELECT full_name FROM users WHERE username = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInUsername);
$stmt->execute();
$assignedPatients = $stmt->get_result();

// Fetch laboratory results
$resultSql = "SELECT lr.patient_id, lr.lab_results, lt.patient_name, lt.doctor_name, lt.test_type 
              FROM laboratory_results lr 
              JOIN laboratory_test lt ON lr.test_id = lt.id 
              WHERE lr.patient_id IN (SELECT patient_id FROM patient_records WHERE doctor_assigned = (SELECT full_name FROM users WHERE username = ?))";
$resultStmt = $conn->prepare($resultSql);
$resultStmt->bind_param("s", $loggedInUsername);
$resultStmt->execute();
$laboratoryResults = $resultStmt->get_result();

// Handle sending lab tests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_to_laboratory'])) {
    $patientId = $_POST['patient_id'];
    $patientName = $_POST['patient_name'];
    $testType = $_POST['test_type'];

    // Insert into laboratory_test table with error check
    $insertSql = "INSERT INTO laboratory_test (patient_id, patient_name, doctor_name, test_type, status) 
                  VALUES (?, ?, ?, ?, 'Pending')";

    $stmt = $conn->prepare($insertSql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error); // Show why prepare failed
    }

    $stmt->bind_param("isss", $patientId, $patientName, $loggedInUsername, $testType);
    if (!$stmt->execute()) {
        die("Execution Error: " . $stmt->error); // Show why execute failed
    }

    header('Location: doctor_dashboard.php'); // Redirect after submission
}

// Handle sending medications to pharmacy
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_to_pharmacy'])) {
    $patientId = $_POST['patient_id'];
    $patientName = $_POST['patient_name'];
    $medications = $_POST['medications'];

    // Insert into medication table
    $insertSql = "INSERT INTO medication (patient_id, patient_name, doctor_name, medications) 
                  VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("isss", $patientId, $patientName, $loggedInUsername, $medications);
    if (!$stmt->execute()) {
        die("Execution Error: " . $stmt->error);
    }

    header('Location: doctor_dashboard.php'); // Redirect after submission
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
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
        <li><a href="#">Pending Patients</a></li>
        <li><a href="#">Laboratory Results</a></li>
        <li><a href="#">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Doctor Dashboard</h1>
    <h2>Assigned Patients</h2>
    <table>
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Age</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($patient = mysqli_fetch_assoc($assignedPatients)) { ?>
                <tr>
                    <td><?php echo $patient['patient_id']; ?></td>
                    <td><?php echo $patient['patient_name']; ?></td>
                    <td><?php echo $patient['age']; ?></td>
                    <td><?php echo $patient['contact']; ?></td>
                    <td><?php echo $patient['address']; ?></td>
                    <td><?php echo $patient['status']; ?></td>
                    <td>
                        <button onclick="openLabTestModal('<?php echo $patient['patient_id']; ?>', '<?php echo $patient['patient_name']; ?>')">Send to Laboratory</button>
                        <button onclick="openMedicationModal('<?php echo $patient['patient_id']; ?>', '<?php echo $patient['patient_name']; ?>')">Send to Pharmacy</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Laboratory Results (Received from Laboratory)</h2>
    <table>
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Doctor Name</th>
                <th>Test Type</th>
                <th>Lab Results</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($resultRow = mysqli_fetch_assoc($laboratoryResults)) { ?>
                <tr>
                    <td><?php echo $resultRow['patient_id']; ?></td>
                    <td><?php echo $resultRow['patient_name']; ?></td>
                    <td><?php echo $resultRow['doctor_name']; ?></td>
                    <td><?php echo $resultRow['test_type']; ?></td>
                    <td><?php echo $resultRow['lab_results']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal for entering lab test details -->
<div id="labTestModal" class="modal">
    <div class="modal-content">
        <span onclick="closeLabTestModal()" style="cursor:pointer; float:right; font-size:20px;">&times;</span>
        <form method="POST">
            <h3>Enter Lab Test for <span id="labModalPatientName"></span></h3>
            <input type="hidden" name="patient_id" id="labModalPatientId" />
            <input type="text" name="patient_name" id="labModalPatientNameInput" required placeholder="Enter Patient Name" />
            <input type="text" name="test_type" required placeholder="Enter Test Type" />
            <button type="submit" name="send_to_laboratory">Send to Laboratory</button>
            <button type="button" onclick="closeLabTestModal()">Cancel</button>
        </form>
    </div>
</div>

<!-- Modal for entering medication details -->
<div id="medicationModal" class="modal">
    <div class="modal-content">
        <span onclick="closeMedicationModal()" style="cursor:pointer; float:right; font-size:20px;">&times;</span>
        <form method="POST">
            <h3>Enter Medication Details for <span id="modalPatientName"></span></h3>
            <input type="hidden" name="patient_id" id="modalPatientId" />
            <input type="text" name="patient_name" id="modalPatientNameInput" required placeholder="Enter Patient Name" />
            <textarea name="medications" required placeholder="Enter Medications"></textarea>
            <button type="submit" name="send_to_pharmacy">Send to Pharmacy</button>
            <button type="button" onclick="closeMedicationModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
function openLabTestModal(patientId, patientName) {
    document.getElementById('labModalPatientId').value = patientId;
    document.getElementById('labModalPatientName').innerText = patientName;
    document.getElementById('labModalPatientNameInput').value = patientName;
    document.getElementById('labTestModal').style.display = 'block';
}

function closeLabTestModal() {
    document.getElementById('labTestModal').style.display = 'none';
}

function openMedicationModal(patientId, patientName) {
    document.getElementById('modalPatientId').value = patientId;
    document.getElementById('modalPatientName').innerText = patientName;
    document.getElementById('modalPatientNameInput').value = patientName;
    document.getElementById('medicationModal').style.display = 'block';
}

function closeMedicationModal() {
    document.getElementById('medicationModal').style.display = 'none';
}
</script>

</body>
</html>

<?php
$conn->close();
?>