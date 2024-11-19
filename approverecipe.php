<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dishcovery";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the recipe ID from the form
$id = $_POST['id'];

// Update the status to 'approved'
$sql = "UPDATE recipeee SET status = 'approved' WHERE id = ?";
$stmt = $conn->prepare($sql); // Use $conn instead of $connection

if ($stmt) {
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect back to admin dashboard after approval
        header("Location: admin.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
