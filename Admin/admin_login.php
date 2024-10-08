<?php
session_start();

// Database connection parameters
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "foodwaste";

// Create a database connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login_input = $_POST['login_input'];
    $password = $_POST['password'];

    // Prepare and execute query to fetch admin based on username or email
    $query = $conn->prepare("SELECT * FROM admin WHERE username = ? OR email = ?");
    $query->bind_param("ss", $login_input, $login_input);
    $query->execute();
    $result = $query->get_result();

    // Debugging output
    error_log("Query executed: " . $conn->error);

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        
        // Debugging output
        error_log("Admin found: " . json_encode($admin));

        // Verify password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];

            // Debugging output
            error_log("Password verified. Redirecting to homepage...");

            // Redirect to admin homepage
            header('Location: admin_homepage.php');
            exit();
        } else {
            $error = "Invalid username/email or password.";
            error_log("Password verification failed.");
        }
    } else {
        $error = "No admin found with that username or email.";
        error_log("No admin found.");
    }
    
    $query->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f8ff;
            font-family: 'Lato', sans-serif;
        }

        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 400px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .form-control {
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .btn-primary {
            width: 100%;
            background-color: #007bff;
            border: none;
            padding: 10px;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="POST" action="">
            <input type="text" name="login_input" class="form-control" placeholder="Username or Email" required>
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <button type="submit" class="btn btn-primary">Login</button>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
        </form>

        <div class="register-link">
            <p>Don't have an account? <a href="admin_registration.php">Register here</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
