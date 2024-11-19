// Function to search through sidebar links
function search() {
    // Get the input value from the search bar
    const searchInput = document.getElementById('searchInput').value.toLowerCase();

    // Get all the sidebar links
    const links = document.querySelectorAll('.sidebar1 ul li a');

    // Loop through each link
    links.forEach(link => {
        // Get the text content of the link
        const text = link.textContent.toLowerCase();
        
        // Check if the link text includes the search input
        if (text.includes(searchInput)) {
            link.parentElement.style.display = ''; // Show the link
        } else {
            link.parentElement.style.display = 'none'; // Hide the link
        }
    });

}

// Toggle the dropdown menu when the icon is clicked
document.querySelector('.dropdown-btn').addEventListener('click', function() {
    var dropdownContent = document.querySelector('.logout-dropdown');
    dropdownContent.classList.toggle('show');
});
