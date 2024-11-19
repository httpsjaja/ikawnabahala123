<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="recover-container">
        <h2>Recover Password</h2>
        <h3>Enter the email address Registered to your account we'll send you a link to reset your password</h3>
        <form id="recoverForm">
            <label for="recoverUsername">Email Address</label>
            <input type="text" id="recoverUsername" required placeholder="Email">
            <button type="submit">Submit</button>
            <div id="recoverMessage" class="message"></div>
        </form>
        <div class="links">
        <a href="adminlogin.php" class="signup-link">Remember Password? <span class="red-text">Login</span> here</a>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>