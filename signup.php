
<?php
$host = "localhost"; // Database host (usually localhost)
$dbname = "dishcovery"; // Database name
$username = "root"; // Database username (root by default)
$password = ""; // Database password (empty by default)

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

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
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Simple validation
    if ($password !== $confirmPassword) {
        echo "Passwords do not match!";
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    // Execute the statement
    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
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
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Link to Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="signup-container">
        <h2>DISH-COVERY</h2>
        <form id="signupForm" method="POST" action="">
            <label for="confirmPassword">Sign-up</label>
            <input type="text" id="Username" name="Username" required placeholder="Username">
            <input type="text" id="Email" name="Email" required placeholder="Email">
            
            <!-- Password field with eye icon -->
            <div class="password-container">
                <input type="password" id="Password" name="Password" required placeholder="Password">
                <span class="toggle-password" onclick="togglePassword('Password', 'eye-icon1')">
                    <i id="eye-icon1" class="far fa-eye-slash"></i>
                </span>
            </div>
            
            <!-- Confirm Password field with eye icon -->
            <div class="password-container">
                <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirm Password">
                <span class="toggle-password" onclick="togglePassword('confirmPassword', 'eye-icon2')">
                    <i id="eye-icon2" class="far fa-eye-slash"></i>
                </span>
            </div>

            <!-- Agree to Terms and Policies checkbox -->
            <div class="terms-container">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to the <a href="terms.php" target="_blank">Terms and Policies</a></label>
            </div>

            <div class="g-recaptcha" data-sitekey="6Lf7EmMqAAAAAHMqBjFN_tnKRfRihEqnWA8Hmitz"></div>
            <button type="submit">Submit</button>
        </form>
        <div class="links">
            <a href="index.php">Go to Home</a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, iconId) {
            var passwordField = document.getElementById(fieldId);
            var eyeIcon = document.getElementById(iconId);
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            }
        }
    </script>

    <style>
        /* Styling the password container for better alignment */
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
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

        /* Styling for the terms container */
        .terms-container {
            margin-bottom: 15px;
        }
    </style>
</body>
</html>
