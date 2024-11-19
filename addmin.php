<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        form {
            max-width: 400px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        label {
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Add Admin User</h2>
    <form method="POST" action="addmin.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Add Admin</button>
    </form>

    <?php
    // Database connection
    $servername = "localhost"; // Update this to your database server
    $db_username = "root";     // Database username
    $db_password = "";         // Database password
    $dbname = "dishcovery"; // Your database name

    // Establish connection
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Sanitize inputs
        $username = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $usertype = 'admin'; // Set the usertype to 'admin'

        // Check if email already exists
        $checkEmail = $conn->query("SELECT id FROM users WHERE email = '$email'");
        if ($checkEmail->num_rows > 0) {
            echo "<p style='color: red;'>Error: Email already exists.</p>";
        } else {
            // Insert new admin into the database
            $sql = "INSERT INTO users (usertype, username, email, password, created_at) 
                    VALUES ('$usertype', '$username', '$email', '$password', NOW())";

            if ($conn->query($sql) === TRUE) {
                echo "<p style='color: green;'>Admin user added successfully.</p>";
            } else {
                echo "<p style='color: red;'>Error: " . $sql . "<br>" . $conn->error . "</p>";
            }
        }
    }

    $conn->close();
    ?>
</body>
</html>
