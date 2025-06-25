<?php
// Start session to handle user login status
session_start();

// Check if the user is logged in and has the 'admin' role
if (!isset($_SESSION['logged']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page if not logged in or if not an admin
    header("Location: login.php");
    exit;
}

// Include database connection (update path as needed)
include('db_connection.php');

// Fetch admin name or other session variables if needed
$admin_name = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <style>
    /* Same CSS as your provided version */
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <ul>
    <li><a href="#">Dashboard</a></li>
    <li><a href="#">User & Staff Management</a></li>
    <li><a href="#">Billing & Finance</a></li>
    <li><a href="#">Medical Records</a></li>
    <li><a href="#">Inventory & Pharmacy</a></li>
    <li><a href="#">Feedback & Communication</a></li>
    <li><a href="#">Security & Data Protection</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</div>

<!-- Main content -->
<div class="main-content">
  <div class="header">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($admin_name); ?>! Manage hospital operations efficiently.</p>
  </div>

  <!-- User & Staff Management Section -->
  <div class="section">
    <h2>User & Staff Management</h2>
    <button onclick="addStaff()">Add Staff</button>
    <button onclick="editStaff()">Edit Staff</button>
    <button onclick="removeStaff()">Remove Staff</button>
    <button onclick="manageRoles()">Manage Roles & Permissions</button>
    <button onclick="handlePatientRegistrations()">Handle Patient Registrations</button>
  </div>

  <!-- Billing & Finance Section -->
  <div class="section">
    <h2>Billing & Finance Management</h2>
    <button onclick="trackRevenue()">Track Revenue</button>
    <button onclick="handleBilling()">Handle Billing</button>
    <button onclick="processClaims()">Process Insurance Claims</button>
    <button onclick="managePayments()">Monitor Payments</button>
    <button onclick="processRefunds()">Process Refunds</button>
  </div>

  <!-- Medical Records & Reports Section -->
  <div class="section">
    <h2>Medical Records & Reports</h2>
    <button onclick="maintainHistory()">Maintain Patient History</button>
    <button onclick="generateReports()">Generate Reports</button>
    <button onclick="ensureCompliance()">Ensure Compliance</button>
  </div>

  <!-- Inventory & Pharmacy Section -->
  <div class="section">
    <h2>Inventory & Pharmacy Management</h2>
    <button onclick="trackStock()">Track Stock Levels</button>
    <button onclick="orderStock()">Order Stock</button>
    <button onclick="removeExpired()">Remove Expired Medicines</button>
    <button onclick="manageVendors()">Manage Vendors</button>
  </div>

  <!-- Feedback & Communication Section -->
  <div class="section">
    <h2>Feedback & Communication</h2>
    <button onclick="handleComplaints()">Handle Complaints</button>
    <button onclick="sendAnnouncements()">Send Announcements</button>
  </div>

  <!-- Security & Data Protection Section -->
  <div class="section">
    <h2>Security & Data Protection</h2>
    <button onclick="ensureSecurity()">Ensure Secure Login</button>
    <button onclick="monitorLogs()">Monitor System Logs</button>
    <button onclick="backupData()">Backup & Recovery</button>
  </div>
</div>

<script>
  // Example function placeholders for each admin management task
  function addStaff() {
    alert("Function to add staff member.");
  }

  function editStaff() {
    alert("Function to edit staff details.");
  }

  function removeStaff() {
    alert("Function to remove staff member.");
  }

  function manageRoles() {
    alert("Function to manage roles and permissions.");
  }

  function handlePatientRegistrations() {
    alert("Function to handle patient registrations.");
  }

  function trackRevenue() {
    alert("Function to track hospital revenue and expenses.");
  }

  function handleBilling() {
    alert("Function to handle patient billing.");
  }

  function processClaims() {
    alert("Function to process insurance claims.");
  }

  function managePayments() {
    alert("Function to monitor outstanding payments.");
  }

  function processRefunds() {
    alert("Function to process refunds.");
  }

  function maintainHistory() {
    alert("Function to maintain medical history records.");
  }

  function generateReports() {
    alert("Function to generate medical reports.");
  }

  function ensureCompliance() {
    alert("Function to ensure compliance with regulations.");
  }

  function trackStock() {
    alert("Function to track stock levels in pharmacy.");
  }

  function orderStock() {
    alert("Function to order new stock for pharmacy.");
  }

  function removeExpired() {
    alert("Function to remove expired medicines.");
  }

  function manageVendors() {
    alert("Function to manage pharmacy vendors.");
  }

  function handleComplaints() {
    alert("Function to handle patient complaints.");
  }

  function sendAnnouncements() {
    alert("Function to send hospital-wide announcements.");
  }

  function ensureSecurity() {
    alert("Function to ensure secure login and authentication.");
  }

  function monitorLogs() {
    alert("Function to monitor system logs for unauthorized access.");
  }

  function backupData() {
    alert("Function to implement data backup and recovery.");
  }
</script>

</body>
</html>
