<?php
session_start(); // Start the session

$host = "localhost"; // Database host
$dbname = "dishcovery"; // Database name
$dbusername = "root"; // Database username
$dbpassword = ""; // Database password

// Create a connection
$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set UTF-8 encoding to handle special characters
if (!$conn->set_charset("utf8mb4")) {
    die("Error loading character set utf8mb4: " . $conn->error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, password, usertype FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);

    // Execute the statement
    $stmt->execute();
    
    // Store the result
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows == 1) {
        // Bind the result
        $stmt->bind_result($userId, $hashedPassword, $userType);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $userId; // Store user ID in session
            $_SESSION['username'] = $username; // Store username in session
            $_SESSION['usertype'] = $userType; // Store user type in session
            
            // Redirect based on user type
            if ($userType === 'admin') {
                header("Location: admin.php"); // Redirect to admin dashboard
            } else {
                header("Location: userdash.php"); // Redirect to user dashboard
            }
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with that username!";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DISH-COVERY - Login</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Link to Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <h2>DISH-COVERY</h2>
        <form method="POST">
            <label for="username">LOGIN</label>
            <input type="text" id="username" name="username" required placeholder="Username">
            
            <div class="password-container">
                <input type="password" id="password" name="password" required placeholder="Password">
                <!-- Eye icon for toggling visibility -->
                <span class="toggle-password" onclick="togglePassword()">
                    <i id="eye-icon" class="far fa-eye-slash"></i>
                </span>
            </div>

            <div class="g-recaptcha" data-sitekey="6Lf7EmMqAAAAAHMqBjFN_tnKRfRihEqnWA8Hmitz"></div>
            <button type="submit">Login</button>
        </form>

        <div class="links">
            <a href="recover.php" class="signup-link">Recover Password</a>
            <br>
            <a href="signup.php" class="signup-link">Don't have an account? <span class="blue-text">Sign up </span> here</a>
        </div>
        <div class="link">
            <a href="index.php">Go to Home</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var eyeIcon = document.getElementById("eye-icon");
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye"); // Change to open eye
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash"); // Change to closed eye
            }
        }
    </script>

    <style>
        /* Styling the password container for better alignment */
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-container input[type="password"],
        .password-container input[type="text"] {
            padding-right: 30px; /* Space for the eye icon */
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
        }

        /* Eye icon size */
        .toggle-password i {
            font-size: 1.2em;
        }
    </style>
</body>
</html>


