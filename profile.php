<?php
// Start session to access session variables from login.php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch the username from the session
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Dashboard</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-box">
            <img src="default-profile.png" alt="Profile Picture" class="profile-pic" id="profilePic">
            <div class="username-display">
                <label for="username">Username:</label> 
                <span id="usernameText"><?php echo htmlspecialchars($username); ?></span> 
                <button id="editBtn">Edit</button>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="editModal" class="modal hidden">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h2>Edit Profile</h2>
            <form id="editProfileForm">
                <label for="profilePicInput">Change Profile Picture:</label>
                <input type="file" id="profilePicInput" accept="image/*"><br><br>
                
                <label for="usernameField">Edit Username:</label>
                <input type="text" id="usernameField" value="<?php echo htmlspecialchars($username); ?>"><br><br>
                
                <button type="button" id="saveBtn">Save</button>
                <button type="button" id="cancelBtn">Cancel</button>
            </form>
        </div>
    </div>

    <script src="profile.js"></script>
</body>
</html>
