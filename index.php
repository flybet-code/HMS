<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = md5($_POST['password']);

    // Query to check if user exists and fetch role
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, fetch role and redirect
        $row = $result->fetch_assoc();
        $role = $row['role']; // Assume 'role' column exists in your table
        $_SESSION['username'] = $user;
        $_SESSION['role'] = $role;

        switch ($role) {
            case 'admin':
                header('Location: admin_dashboard.php');
                break;
            case 'doctor':
                header('Location: doctor_dashboard.php');
                break;
            case 'cardroom':
                header('Location: cardroom_dashboard.php');
                break;
            case 'pharmacy':
                header('Location: pharmacy_dashboard.php');
                break;
            case 'laboratory':
                 header('Location: laboratory_dashboard.php');
                break;
            case 'cashier':
                header('Location: cashier_dashboard.php');
                break;
                default:
                echo "Invalid role!";
                session_destroy();
                break;
        }
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hospital Management - Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    /* General Styles */
    body {
      font-family: 'Poppins', sans-serif;
      background: #f8f9fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .auth-container {
      background: #ffffff;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      width: 380px;
      padding: 40px;
      text-align: center;
      transition: transform 0.3s ease;
    }
    
    .auth-container:hover {
      transform: scale(1.02);
    }

    .auth-container h2 {
      margin-bottom: 20px;
      color: #333;
      font-size: 26px;
      font-weight: 600;
    }

    .input-group {
      position: relative;
      margin-bottom: 20px;
    }

    .input-group input {
      width: 100%;
      padding: 12px;
      border: 2px solid #ddd;
      border-radius: 25px;
      font-size: 16px;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      outline: none;
    }

    .input-group input:focus {
      border-color: #007bff;
      box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
    }

    .auth-button {
      width: 100%;
      padding: 12px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease, transform 0.2s ease;
    }

    .auth-button:hover {
      background: #0056b3;
      transform: translateY(-2px);
    }

    .forgot-password {
      display: block;
      margin-top: 12px;
      color: #007bff;
      text-decoration: none;
      font-size: 14px;
      transition: color 0.3s ease;
    }

    .forgot-password:hover {
      color: #0056b3;
      text-decoration: underline;
    }

    .error-message {
      color: red;
      font-size: 14px;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="auth-container">
    <h2>Hospital Management System</h2>
    <form action="index.php" method="POST">
      <div class="input-group">
        <input type="text" name="username" placeholder="Username" required>
      </div>
      <div class="input-group">
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <button type="submit" class="auth-button">Login</button>
    </form>
    <a href="#" class="forgot-password">Forgot password?</a>

    <?php if (isset($error)): ?>
      <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>
  </div>
</body>
</html>
