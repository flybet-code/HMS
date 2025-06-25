<?php
session_start();
// Uncomment for session management
// if (!isset($_SESSION['username']) || $_SESSION['role'] != 'cashier') {
//     header('Location: index.php');
//     exit();
// }

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch pending payments from the laboratory_test table
$laboratoryPaymentsSql = "SELECT patient_id, patient_name, doctor_name, test_type, price FROM laboratory_test WHERE status = 'Pending'";
$laboratoryPaymentsResult = $conn->query($laboratoryPaymentsSql);

// Fetch pending payments from the medication table
$pharmacyPaymentsSql = "SELECT patient_id, patient_name, doctor_name, medications, price FROM medication WHERE status = 'Pending'";
$pharmacyPaymentsResult = $conn->query($pharmacyPaymentsSql);

if (!$laboratoryPaymentsResult || !$pharmacyPaymentsResult) {
    die("Error fetching data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard</title>
    <link rel="stylesheet" href="cashier_dashboard.css"> <!-- Link to the CSS file -->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; display: flex; height: 100vh; background-color: #f9f9f9; }
        .sidebar { width: 250px; background-color: #3949ab; color: white; position: fixed; height: 100%; padding-top: 30px; }
        .sidebar ul { list-style: none; }
        .sidebar ul li { padding: 15px 20px; }
        .sidebar ul li a { text-decoration: none; color: white; font-size: 16px; display: block; font-weight: bold; }
        .sidebar ul li a:hover { background-color: #283593; border-radius: 5px; padding-left: 10px; }
        .main-content { margin-left: 250px; padding: 40px; width: calc(100% - 250px); }
        .header { background-color: #3949ab; color: white; padding: 20px; text-align: center; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        button { padding: 10px 15px; background-color: #3949ab; color: white; border: none; cursor: pointer; border-radius: 5px; }
        button:hover { background-color: #283593; }
        .section { background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .pending-payment { margin-bottom: 30px; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0, 0, 0, 0.5); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 400px; border-radius: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <ul>
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Pending Payments</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<!-- Main content -->
<div class="main-content">
    <div class="header">
        <h1>Cashier Dashboard</h1>
        <p>Manage payments and approve transactions.</p>
    </div>

    <!-- Pending Payments Section (from Lab) -->
    <div class="section pending-payment">
        <h2>Pending Payments (from Lab)</h2>
        <table id="pending-payments-table">
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Test Type</th>
                    <th>Price (Birr)</th>
                    <th>Add Price</th>
                    <th>Approve Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $laboratoryPaymentsResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['patient_id']; ?></td>
                        <td><?php echo $row['patient_name']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td><?php echo $row['test_type']; ?></td>
                        <td><?php echo isset($row['price']) ? $row['price'] : 'Not Set'; ?></td>
                        <td><button onclick="openAddPriceModal('<?php echo $row['patient_id']; ?>', '<?php echo $row['patient_name']; ?>', 'lab')">Add Price</button></td>
                        <td><button onclick="approvePayment('<?php echo $row['patient_id']; ?>')">Approve</button></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Pending Payments Section (from Pharmacy) -->
    <div class="section pending-payment">
        <h2>Pending Payments (from Pharmacy)</h2>
        <table id="pending-payments-pharmacy-table">
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Medication</th>
                    <th>Price (Birr)</th>
                    <th>Add Price</th>
                    <th>Approve Payment</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $pharmacyPaymentsResult->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['patient_id']; ?></td>
                        <td><?php echo $row['patient_name']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td><?php echo $row['medications']; ?></td>
                        <td><?php echo isset($row['price']) ? $row['price'] : 'Not Set'; ?></td>
                        <td><button onclick="openAddPriceModal('<?php echo $row['patient_id']; ?>', '<?php echo $row['patient_name']; ?>', 'pharmacy')">Add Price</button></td>
                        <td><button onclick="approvePayment('<?php echo $row['patient_id']; ?>')">Approve</button></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Adding Price -->
<div id="addPriceModal" class="modal">
    <div class="modal-content">
        <span onclick="closeModal()" style="cursor:pointer; float:right; font-size:20px;">&times;</span>
        <h3>Add Price</h3>
        <form id="addPriceForm">
            <input type="hidden" id="modalPatientId" name="patient_id">
            <input type="hidden" id="modalType" name="type">
            <div class="form-group">
                <label for="price">Enter Price (Birr):</label>
                <input type="number" id="price" name="price" required>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<script>
// Function to open the price addition modal
function openAddPriceModal(patientId, patientName, type) {
    document.getElementById('modalPatientId').value = patientId;
    document.getElementById('modalType').value = type;
    document.getElementById('addPriceModal').style.display = 'block';
}

// Function to close the modal
function closeModal() {
    document.getElementById('addPriceModal').style.display = 'none';
}

// Function to handle form submission for adding price
document.getElementById('addPriceForm').onsubmit = function(event) {
    event.preventDefault(); // Prevent default form submission

    const patientId = document.getElementById('modalPatientId').value;
    const price = document.getElementById('price').value;
    const type = document.getElementById('modalType').value;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "add_price.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function () {
        if (xhr.status === 200) {
            alert(`Price for Patient ID ${patientId} has been added.`);
            // Update the price on the dashboard without reloading
            updatePriceOnDashboard(patientId, price, type);
            closeModal();
        } else {
            alert("Error adding price. Please try again.");
        }
    };
    xhr.send(`patient_id=${patientId}&price=${price}&type=${type}`);
};

// Function to update the price on the dashboard
function updatePriceOnDashboard(patientId, price, type) {
    const tableRows = type === 'lab' ? 
        document.querySelectorAll('#pending-payments-table tbody tr') :
        document.querySelectorAll('#pending-payments-pharmacy-table tbody tr');

    tableRows.forEach(row => {
        const patientCell = row.cells[0]; // Patient ID cell
        if (patientCell.textContent === patientId) {
            row.cells[4].textContent = price; // Update the Price cell
        }
    });
}

// Function to approve payment and update status in the database
function approvePayment(patientId) {
    const action = confirm(`Approve payment for Patient ID ${patientId}?`);

    if (action) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "approve_payment.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status === 200) {
                alert(`Payment for Patient ID ${patientId} has been approved.`);
                location.reload(); // Reload the page to reflect changes
            } else {
                alert("Error approving payment. Please try again.");
            }
        };
        xhr.send(`patient_id=${patientId}`);
    }
}
</script>

</body>
</html>

<?php
$conn->close();
?>