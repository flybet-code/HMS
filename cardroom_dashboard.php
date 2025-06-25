<?php
/*
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cardroom') {
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

$message = "";

// Handle registration of a new patient
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_patient'])) {
        $patient_id = $_POST['patient_id'];
        $patient_name = $_POST['patient_name'];
        $age = $_POST['age'];
        $contact = $_POST['contact'];
        $address = $_POST['address'];
        $home_number = $_POST['home_number'];
        $patient_type = $_POST['patient_type'];

        // Check if patient ID already exists
        $check_sql = "SELECT * FROM patient_records WHERE patient_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $patient_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $_SESSION['message'] = "Error: Patient ID already exists. Please use a unique ID.";
        } else {
            $sql = "INSERT INTO patient_records (patient_id, patient_name, age, contact, address, home_number, patient_type)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssissss", $patient_id, $patient_name, $age, $contact, $address, $home_number, $patient_type);

            if ($stmt->execute()) {
                $_SESSION['message'] = "Patient registered successfully!";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
            }
        }
        header('Location: cardroom_dashboard.php'); // Redirect to reload the page
        exit();
    } elseif (isset($_POST['assign_doctor'])) {
        $patient_id = $_POST['patient_id'];
        $doctor_assigned = $_POST['doctor_assigned'];

        // Update the patient record with the assigned doctor
        $sql = "UPDATE patient_records SET doctor_assigned = ? WHERE patient_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $doctor_assigned, $patient_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Doctor assigned successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
        header('Location: cardroom_dashboard.php'); // Redirect to reload the page
        exit();
    } elseif (isset($_POST['edit_patient'])) {
        $patient_id = $_POST['patient_id'];
        $patient_name = $_POST['patient_name'];
        $age = $_POST['age'];
        $contact = $_POST['contact'];
        $address = $_POST['address'];
        $home_number = $_POST['home_number'];
        $patient_type = $_POST['patient_type'];

        // Update patient information
        $sql = "UPDATE patient_records SET patient_name = ?, age = ?, contact = ?, address = ?, home_number = ?, patient_type = ? WHERE patient_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siissss", $patient_name, $age, $contact, $address, $home_number, $patient_type, $patient_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Patient information updated successfully!";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
        header('Location: cardroom_dashboard.php'); // Redirect to reload the page
        exit();
    }
}

// Check if there's a message to display
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying it
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cardroom Dashboard</title>
  <style>
    * {
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }
    body {
        background: #f8f9fa;
        padding: 20px;
    }
    button {
        padding: 12px 18px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    button:hover {
        background-color: #0056b3;
        transform: translateY(-3px);
    }
    .content {
        max-width: 800px;
        margin: 0 auto;
    }
    h1 {
        color: #333;
        text-align: center;
        font-size: 2rem;
    }
    .message {
        background: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: 5px;
        display: inline-block;
        margin-top: 10px;
    }
    .form-container {
        display: none;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
        animation: fadeIn 0.5s ease;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        display: none;
    }
    table th, table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    table th {
        background-color: #007bff;
        color: white;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1000; 
        left: 0; 
        top: 0; 
        width: 100%; 
        height: 100%; 
        overflow: auto; 
        background-color: rgba(0, 0, 0, 0.5); 
    }
    .modal-content {
        background-color: #fefefe; 
        margin: 15% auto; 
        padding: 20px; 
        border: 1px solid #888; 
        width: 80%; 
        max-width: 500px; 
        border-radius: 10px;
        animation: slideIn 0.5s ease;
    }
    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
  </style>
</head>
<body>
  <div class="content">
    <h1>Welcome to Cardroom Dashboard</h1>
    <p class="message"><?php echo $message; ?></p>

    <div style="text-align: center;">
        <button onclick="toggleForm('register-form')">Register New Patient</button>
        <button onclick="togglePatientList()">List All Registered Patients</button>
        <button onclick="toggleForm('assign-form')">Send Request (Assign Doctor)</button>
    </div>

    <!-- Register Patient Form -->
    <div id="register-form" class="form-container">
        <h3>Register New Patient</h3>
        <form method="POST">
            <label>Patient ID:</label>
            <input type="text" name="patient_id" required><br><br>
            <label>Patient Name:</label>
            <input type="text" name="patient_name" required><br><br>
            <label>Age:</label>
            <input type="number" name="age" required><br><br>
            <label>Contact:</label>
            <input type="text" name="contact" required><br><br>
            <label>Address:</label>
            <input type="text" name="address" required><br><br>
            <label>Home Number:</label>
            <input type="text" name="home_number" required><br><br>
            <label>Patient Type:</label>
            <select name="patient_type" required>
                <option value="Individual">Individual</option>
                <option value="Worker">Worker</option>
            </select><br><br>
            <button type="submit" name="register_patient">Register</button>
        </form>
    </div>

    <!-- Assign Doctor Form -->
    <div id="assign-form" class="form-container">
        <h3>Assign Doctor to Patient</h3>
        <form method="POST">
            <label>Patient ID:</label>
            <input type="text" name="patient_id" required><br><br>
            <label>Doctor Assigned:</label>
            <select name="doctor_assigned" required>
                <?php
                $result = $conn->query("SELECT username, full_name FROM users WHERE role = 'doctor'");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['full_name'] . "'>" . $row['full_name'] . "</option>";
                }
                ?>
            </select><br><br>
            <button type="submit" name="assign_doctor">Assign Doctor</button>
        </form>
    </div>

    <!-- List All Patients -->
    <table id="patient-table">
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Home Number</th>
                <th>Patient Type</th>
                <th>Doctor Assigned</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM patient_records");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['patient_id']}</td>
                        <td>{$row['patient_name']}</td>
                        <td>{$row['age']}</td>
                        <td>{$row['contact']}</td>
                        <td>{$row['address']}</td>
                        <td>{$row['home_number']}</td>
                        <td>{$row['patient_type']}</td>
                        <td>{$row['doctor_assigned']}</td>
                        <td><button onclick='openEditModal(\"{$row['patient_id']}\", \"{$row['patient_name']}\", {$row['age']}, \"{$row['contact']}\", \"{$row['address']}\", \"{$row['home_number']}\", \"{$row['patient_type']}\")'>Edit</button></td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
  </div>

  <!-- Edit Patient Modal -->
  <div id="editPatientModal" class="modal">
      <div class="modal-content">
          <span onclick="closeEditModal()" style="cursor:pointer; float:right; font-size:20px;">&times;</span>
          <form method="POST">
              <h3>Edit Patient Details</h3>
              <input type="hidden" name="patient_id" id="editPatientId" />
              <label>Patient Name:</label>
              <input type="text" name="patient_name" id="editPatientName" required><br><br>
              <label>Age:</label>
              <input type="number" name="age" id="editPatientAge" required><br><br>
              <label>Contact:</label>
              <input type="text" name="contact" id="editPatientContact" required><br><br>
              <label>Address:</label>
              <input type="text" name="address" id="editPatientAddress" required><br><br>
              <label>Home Number:</label>
              <input type="text" name="home_number" id="editPatientHomeNumber" required><br><br>
              <label>Patient Type:</label>
              <select name="patient_type" id="editPatientType" required>
                  <option value="Individual">Individual</option>
                  <option value="Worker">Worker</option>
              </select><br><br>
              <button type="submit" name="edit_patient">Update Patient</button>
              <button type="button" onclick="closeEditModal()">Cancel</button>
          </form>
      </div>
  </div>

  <script>
    function toggleForm(formId) {
        const form = document.getElementById(formId);
        form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
    }

    function togglePatientList() {
        const table = document.getElementById('patient-table');
        table.style.display = (table.style.display === "none" || table.style.display === "") ? "table" : "none";
    }

    function openEditModal(patientId, patientName, age, contact, address, homeNumber, patientType) {
        document.getElementById('editPatientId').value = patientId;
        document.getElementById('editPatientName').value = patientName;
        document.getElementById('editPatientAge').value = age;
        document.getElementById('editPatientContact').value = contact;
        document.getElementById('editPatientAddress').value = address;
        document.getElementById('editPatientHomeNumber').value = homeNumber;
        document.getElementById('editPatientType').value = patientType;

        document.getElementById('editPatientModal').style.display = 'block';
    }

    function closeEditModal() {
        document.getElementById('editPatientModal').style.display = 'none';
    }
  </script>
</body>
</html>

<?php
$conn->close();
?>