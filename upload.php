<?php
// upload.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dishcovery";
$connection = new mysqli($servername, $username, $password, $dbname);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form data
    $photoTitle = $_POST['photoTitle'];
    $photoDescription = $_POST['photoDescription'];  // Save to the 'recipe' column in the database
    $category = $_POST['category'];

    // Handle file upload
    if (isset($_FILES['media']) && $_FILES['media']['error'] == 0) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['media']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Create upload directory if it doesn’t exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES['media']['tmp_name'], $targetFilePath)) {
            // Insert recipe data into the database with 'pending' status
            $sql = "INSERT INTO recipeee (dish_name, recipe, category, image_path, status) VALUES (?, ?, ?, ?, 'pending')";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("ssss", $photoTitle, $photoDescription, $category, $targetFilePath);

            if ($stmt->execute()) {
                // Display success message without redirecting
                echo "<script>alert('Recipe submitted successfully for approval.'); window.location.href = 'userdash.php';</script>";
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "File upload failed.";
        }
    } else {
        echo "Please select a file to upload.";
    }
}

$connection->close();
?>
