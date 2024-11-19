<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dishcovery";
$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Pagination setup
$recipesPerPage = 6; // Number of recipes per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $recipesPerPage;

// Get total number of recipes
$totalRecipesQuery = "SELECT COUNT(*) as total FROM recipeee WHERE status = 'approved'";
$totalResult = $connection->query($totalRecipesQuery);
$totalRecipes = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecipes / $recipesPerPage);

// Fetch recipes for the current page
$sql = "SELECT * FROM recipeee WHERE status = 'approved' LIMIT $start, $recipesPerPage";
$result = $connection->query($sql);
$recipes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
}
$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="userdash.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="userdash.js" defer></script>
</head>
<body>
    <aside class="sidebar">
    <h2>Dish-covery</h2>
    <div class="profile-logout-container">
    <button class="profile-container" onclick="window.location.href='profile.php'">
        <img src="path/to/profile-pic.jpg" alt="Profile" class="profile-pic">
    </button>
    <div class="logout-dropdown">
    <!-- Dropdown Icon -->
    <button class="dropdown-btn">▼</button>
    <!-- Dropdown Menu -->
    <div class="dropdown-content">
        <button class="logout-btn" onclick="logout()">Logout</button>
    </div>
</div>



</div>

        <main>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Type to search..." oninput="filterRecipes()">
                <button onclick="filterRecipes()">Search</button>
            </div>
        </main>
        <div class="btnn-container">
            <button class="btnn btnn-color-2" onclick="window.location.href='nutritiontrack.php'">Nutrition Tracker</button>
            <button class="btnn btnn-color-3" onclick="window.location.href='mealplan.php'">Meal Planning</button>
        </div>
        <div class="btn1-container">
            <button class="btn1 btn1-color-4" onclick="showModal()">Upload Recipe</button>
        </div>
    </aside>

    <div class="container mt-5">
        <h2>Uploaded Recipes</h2>
        <div class="row" id="recipeContainer">
            <?php if (!empty($recipes)): ?>
                <?php foreach ($recipes as $recipe): ?>
                    <div class="col-md-4 mb-4 recipe-card">
                        <div class="card">
                            <img src="<?= htmlspecialchars($recipe['image_path']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($recipe['dish_name']) ?>" 
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($recipe['dish_name']) ?></h5>
                                <p class="card-text">
                                    <small class="text-muted">Category: <?= htmlspecialchars($recipe['category']) ?></small>
                                </p>
                                <a href="view_recipe.php?id=<?= $recipe['id'] ?>" class="btn btn-primary" target="_blank">View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No approved recipes found.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination Links -->
        <nav aria-label="Recipe Pagination">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <!-- Upload Recipe Modal -->
    <div class="modal" id="uploadModal" tabindex="-1" role="dialog" style="display:none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Recipe</h5>
                    <button type="button" class="close" onclick="hideModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="upload.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="photoTitle">Dish name:</label>
                            <input type="text" id="photoTitle" name="photoTitle" required>
                        </div>
                        <div class="form-group">
                            <label for="photoDescription">Recipe and Procedure:</label>
                            <textarea id="photoDescription" name="photoDescription" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="category">Category:</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="appetizer">Appetizer</option>
                                <option value="main_course">Main Course</option>
                                <option value="dessert">Dessert</option>
                                <option value="snack">Snack</option>
                                <option value="salads">Salads</option>
                                <option value="side_dishes">Side Dishes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="media">Select Photo:</label>
                            <input type="file" name="media" id="media" class="form-control" accept="image/*" onchange="previewMedia(event)">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showModal() {
            document.getElementById('uploadModal').style.display = 'block';
        }

        function hideModal() {
            document.getElementById('uploadModal').style.display = 'none';
        }

        function logout() {
            window.location.href = 'login.php';
        }

        function filterRecipes() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const recipeCards = document.querySelectorAll('.recipe-card');
            recipeCards.forEach(card => {
                const dishName = card.querySelector('.card-title').innerText.toLowerCase();
                const recipeText = card.querySelector('.card-text').innerText.toLowerCase();
                if (dishName.includes(searchInput) || recipeText.includes(searchInput)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        /* Modal container styling */
.modal {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Overlay effect */
}

.modal-dialog {
    width: 90%;
    max-width: 600px;
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.modal-title {
    font-size: 1.25em;
    color: #1e328f; /* Match the button color */
}

.close {
    font-size: 1.5em;
    background: none;
    border: none;
    cursor: pointer;
}

.modal-body {
    padding: 0;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="text"],
textarea,
select,
input[type="file"] {
    width: 90%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 15px;
}

button[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #1e328f;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
}
/* Search bar container styling */
.search-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.search-bar input[type="text"] {
    width: 600px; /* Adjust this width as needed */
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px 0 0 5px;
}

.search-bar button {
    padding: 8px 12px;
    background-color: #1e328f;
    color: white;
    border: none;
    border-radius: 0 5px 5px 0;
    cursor: pointer;
    font-size: 1em;
}


    </style>
</body>
</html>
