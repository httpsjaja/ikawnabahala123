// Elements
const editBtn = document.getElementById('editBtn');
const editModal = document.getElementById('editModal');
const closeModal = document.getElementById('closeModal');
const saveBtn = document.getElementById('saveBtn');
const cancelBtn = document.getElementById('cancelBtn');
const usernameText = document.getElementById('usernameText');
const usernameField = document.getElementById('usernameField');
const profilePicInput = document.getElementById('profilePicInput');
const profilePic = document.getElementById('profilePic');

// Open Modal
editBtn.addEventListener('click', () => {
    editModal.style.display = 'block';
});

// Close Modal
closeModal.addEventListener('click', () => {
    editModal.style.display = 'none';
});

cancelBtn.addEventListener('click', () => {
    editModal.style.display = 'none';
});

// Save Changes
saveBtn.addEventListener('click', () => {
    // Update Username
    const newUsername = usernameField.value.trim();
    if (newUsername) {
        usernameText.textContent = newUsername;
    }

    // Update Profile Picture Preview
    const file = profilePicInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = () => {
            profilePic.src = reader.result;
        };
        reader.readAsDataURL(file);
    }

    // Close Modal
    editModal.style.display = 'none';
});
