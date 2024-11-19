<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'dishcovery');
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Check if POST variables are set and valid
if (isset($_POST['id'], $_POST['status'])) {
    $id = (int)$_POST['id'];  // Ensure ID is an integer
    $status = trim($_POST['status']); // Clean up the status input

    // Restrict status values to avoid unwanted data
    $valid_statuses = ['pending', 'approved', 'rejected'];
    if (!in_array($status, $valid_statuses)) {
        echo json_encode(["success" => false, "message" => "Invalid status value."]);
        exit();
    }

    // Prepare the SQL update query
    $sql = "UPDATE recipe SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Recipe status updated successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error updating status: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Failed to prepare the statement: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input data."]);
}

$conn->close();
?>
