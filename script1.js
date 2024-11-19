Banawa Trixie
// Simulated user data
const users = [];

// Handle login form submission
document.getElementById('loginForm')?.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorDiv = document.getElementById('error');

    // Check if user exists
    const user = users.find(user => user.username === username && user.password === password);

    if (user) {
        errorDiv.textContent = ''; // Clear any previous error
        // Redirect to dashboard
        localStorage.setItem('username', username); // Store username in local storage
        window.location.href = 'dashboard.html';
    } else {
        errorDiv.textContent = 'Invalid username or password.'; // Show error message
    }
});

// Handle password recovery
document.getElementById('recoverForm')?.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const recoverUsername = document.getElementById('recoverUsername').value;
    const recoverMessage = document.getElementById('recoverMessage');

    // Check if the username exists
    const user = users.find(user => user.username === recoverUsername);

    if (user) {
        recoverMessage.textContent = 'Password recovery link sent!'; // Simulate sending recovery link
    } else {
        recoverMessage.textContent = 'User not found.'; // Show error message
    }
});

// Handle password reset
document.getElementById('resetForm')?.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const resetMessage = document.getElementById('resetMessage');

    if (newPassword === confirmPassword) {
        // Simulate password reset (in a real application, you would update the password in the database)
        const username = localStorage.getItem('username');
        const user = users.find(user => user.username === username);
        
        if (user) {
            user.password = newPassword;
            resetMessage.textContent = 'Password has been reset! You can now log in with your new password.';
        } else {
            resetMessage.textContent = 'User not found.'; // Show error message
        }
    } else {
        resetMessage.textContent = 'Passwords do not match.'; // Show error message
    }
});

// Handle sign-up
document.getElementById('signupForm')?.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const newUsername = document.getElementById('newUsername').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const signupMessage = document.getElementById('signupMessage');

    // Check if username already exists
    const existingUser = users.find(user => user.username === newUsername);

    if (existingUser) {
        signupMessage.textContent = 'Username already exists.'; // Show error message
    } else if (newPassword !== confirmPassword) {
        signupMessage.textContent = 'Passwords do not match.'; // Show error message
    } else {
        // Add new user to the list
        users.push({ username: newUsername, password: newPassword });
        signupMessage.textContent = 'Sign up successful! You can now log in.'; // Show success message
    }
});

// Handle dashboard logic
if (document.getElementById('welcomeMessage')) {
    const username = localStorage.getItem('username');
    document.getElementById('welcomeMessage').textContent = Welcome, ${username}!;
}

// Handle logout
document.getElementById('logoutButton')?.addEventListener('click', function() {
    localStorage.removeItem('username'); // Clear stored username
    window.location.href = 'index.html'; // Redirect to login page
});