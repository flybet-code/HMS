<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch medication data from the database
$sql = "SELECT * FROM medication";
$result = $conn->query($sql);

// Fetch notifications (dummy data for example)
$notifications = [
    ['type' => 'urgent', 'message' => 'Stock for Paracetamol is running low!'],
    ['type' => 'new', 'message' => 'New prescription received for Ibuprofen.'],
    ['type' => 'info', 'message' => 'Update your billing information.']
];

// Handle form submission for adding new medicine
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_prescription'])) {
    $medicine_name = $_POST['medicine-name'];
    $medicine_type = $_POST['medicine-type'];
    $quantity = $_POST['medicine-quantity'];
    $price = $_POST['medicine-price'];
    $selling_date = $_POST['selling-date'];
    $expire_date = $_POST['expire-date'];

    // Insert the data into the medicines table
    $query = "INSERT INTO medicines (name, type, quantity, price, selling_date, expire_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssisss", $medicine_name, $medicine_type, $quantity, $price, $selling_date, $expire_date);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Medicine added successfully.');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pharmacy Dashboard</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Arial', sans-serif;
      background-color: #f7f8fc;
      display: flex;
      height: 100vh;
    }

    .sidebar {
      width: 250px;
      background-color: #3949ab;
      color: white;
      position: fixed;
      height: 100%;
      padding-top: 30px;
    }

    .sidebar ul {
      list-style: none;
    }

    .sidebar ul li {
      padding: 15px 20px;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: white;
      font-size: 16px;
      display: block;
      font-weight: bold;
    }

    .sidebar ul li a:hover {
      background-color: #2c6b2f;
      border-radius: 5px;
      padding-left: 10px;
    }

    .main-content {
      margin-left: 250px;
      padding: 40px;
      width: calc(100% - 250px);
    }

    .header {
      background-color: #3949ab;
      color: white;
      padding: 20px;
      text-align: center;
      border-radius: 8px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    button {
      padding: 8px 12px;
      background-color: #3949ab;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background-color: #2c6b2f;
    }

    .section {
      display: none;
      margin-top: 30px;
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    label {
      font-weight: bold;
    }

    input, select {
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      width: 100%;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <ul>
      <li><a href="#">Dashboard</a></li>
      <li><a href="#" onclick="toggleSection('inventorySection')">Medicine Inventory</a></li>
      <li><a href="#" onclick="toggleSection('prescriptionSection')">Add Prescription</a></li>
      <li><a href="#">Logout</a></li>
    </ul>
  </div>

  <!-- Main content -->
  <div class="main-content">
    <div class="header">
      <h1>Pharmacy Dashboard</h1>
      <p>Manage incoming medications and prescriptions</p>
    </div>

    <!-- Notifications Section -->
    <div class="notifications">
      <h2>Notifications</h2>
      <?php foreach ($notifications as $notification): ?>
        <?php if ($notification['type'] === 'urgent' || $notification['type'] === 'new'): ?>
          <div class="notification">
            <strong><?php echo ucfirst($notification['type']); ?>:</strong> <?php echo $notification['message']; ?>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <!-- Medicine Inventory Section -->
    <div class="section" id="inventorySection">
      <h2>Medicine Inventory</h2>
      <table>
        <thead>
          <tr>
            <th>Patient ID</th>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Medications</th>
            <th>Status</th>
            <th>Stock</th>
            <th>Sell Medicine</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) { ?>
                <tr>
                  <td><?php echo $row['patient_id']; ?></td>
                  <td><?php echo $row['patient_name']; ?></td>
                  <td><?php echo $row['doctor_name']; ?></td>
                  <td><?php echo $row['medications']; ?></td>
                  <td><?php echo $row['status']; ?></td>
                  <td><?php echo $row['stock']; ?></td>
                  <td>
                    <button onclick="alert('Medication for <?php echo $row['patient_name']; ?> has been processed.');">Sell</button>
                  </td>
                </tr>
          <?php } } else { ?>
            <tr>
              <td colspan="7">No incoming medications.</td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- Add Prescription Section -->
    <div class="section" id="prescriptionSection">
      <h2>Add Prescription</h2>
      <form method="POST" action="pharmacy_dashboard.php">
        <label for="medicine-name">Medicine Name</label>
        <input type="text" id="medicine-name" name="medicine-name" required>

        <label for="medicine-type">Type</label>
        <input type="text" id="medicine-type" name="medicine-type" required>

        <label for="medicine-quantity">Quantity</label>
        <input type="number" id="medicine-quantity" name="medicine-quantity" required>

        <label for="medicine-price">Price</label>
        <input type="number" id="medicine-price" name="medicine-price" required>

        <label for="selling-date">Selling Date</label>
        <input type="date" id="selling-date" name="selling-date" required>

        <label for="expire-date">Expire Date</label>
        <input type="date" id="expire-date" name="expire-date" required>

        <button type="submit" name="add_prescription">Add Medicine</button>
      </form>
    </div>

  </div>

  <script>
    // Toggle visibility of the sections
    function toggleSection(sectionId) {
      const section = document.getElementById(sectionId);
      section.style.display = section.style.display === 'none' ? 'block' : 'none';
    }
  </script>

</body>
</html>

<?php
$conn->close();
?>