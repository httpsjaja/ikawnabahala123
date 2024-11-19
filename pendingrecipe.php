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

// Handle recipe approval or rejection
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'] === 'approve' ? 'approved' : 'rejected';

    $fetchRecipeSql = "SELECT * FROM recipeee WHERE id = ?";
    $stmt = $connection->prepare($fetchRecipeSql);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $recipe = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($recipe) {
            $archiveSql = "INSERT INTO recipe_archive (id, dish_name, recipe, category, image_path, status, archived_at) 
                           VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $archiveStmt = $connection->prepare($archiveSql);
            if ($archiveStmt) {
                $archiveStmt->bind_param(
                    "isssss",
                    $recipe['id'],
                    $recipe['dish_name'],
                    $recipe['recipe'],
                    $recipe['category'],
                    $recipe['image_path'],
                    $action
                );
                $archiveStmt->execute();
                $archiveStmt->close();

                $deleteSql = "DELETE FROM recipeee WHERE id = ?";
                $deleteStmt = $connection->prepare($deleteSql);
                if ($deleteStmt) {
                    $deleteStmt->bind_param("i", $id);
                    $deleteStmt->execute();
                    $deleteStmt->close();
                }
            }
        }
    }
    header("Location: pending_recipes.php");
    exit;
}

// Fetch pending recipes
$sql = "SELECT * FROM recipeee WHERE status = 'pending'";
$result = $connection->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Recipes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Pending Recipes</h1>
        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($recipe = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="card">';
                    if (!empty($recipe['image_path'])) {
                        echo '<img src="' . htmlspecialchars($recipe['image_path']) . '" class="card-img-top" alt="' . htmlspecialchars($recipe['dish_name']) . '">';
                    }
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($recipe['dish_name']) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($recipe['recipe']) . '</p>';
                    echo '<p class="card-text"><small class="text-muted">Category: ' . htmlspecialchars($recipe['category']) . '</small></p>';
                    echo '<a href="?action=approve&id=' . $recipe['id'] . '" class="btn btn-success mx-2">Approve</a>';
                    echo '<a href="?action=reject&id=' . $recipe['id'] . '" class="btn btn-danger">Reject</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No pending recipes found.</p>';
            }
            ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
$connection->close();
?>
