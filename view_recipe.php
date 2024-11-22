<?php
// Database connection (same as your existing connection)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dishcovery";
$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
$recipeId = $_GET['id']; // Get recipe ID from URL
// Fetch the full recipe details
$sql = "SELECT * FROM recipeee WHERE id = $recipeId";
$result = $connection->query($sql);
$recipe = $result->fetch_assoc();
// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'])) {
    $rating = (int)$_POST['rating'];
    $insertRatingSql = "INSERT INTO ratings (recipe_id, rating) VALUES ($recipeId, $rating)";
    $connection->query($insertRatingSql);
}
// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = $connection->real_escape_string($_POST['comment']);
    $insertCommentSql = "INSERT INTO comments (recipe_id, comment) VALUES ($recipeId, '$comment')";
    $connection->query($insertCommentSql);
}
// Fetch the average rating for this recipe
$averageRatingSql = "SELECT AVG(rating) AS average_rating FROM ratings WHERE recipe_id = $recipeId";
$averageRatingResult = $connection->query($averageRatingSql);
$averageRating = $averageRatingResult->fetch_assoc()['average_rating'];
// Fetch all comments for this recipe
$commentsSql = "SELECT * FROM comments WHERE recipe_id = $recipeId ORDER BY created_at DESC";
$commentsResult = $connection->query($commentsSql);
// Close the database connection
$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recipe['dish_name']) ?> - Recipe Details</title>
    <link rel="stylesheet" href="view_recipe.css">
    <!-- Add FontAwesome for star icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="recipe-details">
        <h2><?= htmlspecialchars($recipe['dish_name']) ?></h2>
        <img src="<?= htmlspecialchars($recipe['image_path']) ?>" alt="<?= htmlspecialchars($recipe['dish_name']) ?>" style="max-width: 100%; height: auto;">
        <p><strong>Category:</strong> <?= htmlspecialchars($recipe['category']) ?></p>
        <p><strong>Recipe and Procedure:</strong><br><?= nl2br(htmlspecialchars($recipe['recipe'])) ?></p>
        <!-- Display the average rating as stars -->
        <p><strong>Average Rating:</strong> 
            <?php
            if ($averageRating) {
                $fullStars = floor($averageRating);
                $halfStar = ($averageRating - $fullStars) >= 0.5 ? 1 : 0;
                $emptyStars = 5 - $fullStars - $halfStar;
                for ($i = 0; $i < $fullStars; $i++) {
                    echo '<i class="fas fa-star"></i>';
                }
                if ($halfStar) {
                    echo '<i class="fas fa-star-half-alt"></i>';
                }
                for ($i = 0; $i < $emptyStars; $i++) {
                    echo '<i class="far fa-star"></i>';
                }
                echo " (" . round($averageRating, 1) . ")";
            } else {
                echo 'No ratings yet';
            }
            ?>
        </p>
        <!-- Rating form with star selection -->
        <form method="POST">
            <label for="rating">Rate this recipe:</label><br>
            <div class="stars">
                <input type="radio" name="rating" value="1" id="star1"><label for="star1"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" value="2" id="star2"><label for="star2"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" value="3" id="star3"><label for="star3"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" value="4" id="star4"><label for="star4"><i class="fas fa-star"></i></label>
                <input type="radio" name="rating" value="5" id="star5"><label for="star5"><i class="fas fa-star"></i></label>
            </div><br>
            <input type="submit" value="Submit Rating">
        </form>
        <!-- Comment section -->
        <h3>Comments:</h3>
        <?php
        if ($commentsResult->num_rows > 0) {
            while ($comment = $commentsResult->fetch_assoc()) {
                echo "<div class='comment'>";
                echo "<p>" . htmlspecialchars($comment['comment']) . "</p>";
                echo "<small>Posted on " . $comment['created_at'] . "</small>";
                echo "</div><br>";
            }
        } else {
            echo "<p>No comments yet.</p>";
        }
        ?>
        <!-- Add a comment -->
        <form method="POST">
            <label for="comment">Leave a comment:</label><br>
            <textarea name="comment" id="comment" rows="4" cols="50" required></textarea><br>
            <input type="submit" value="Submit Comment">
        </form>
    </div>
    <!-- Back to Dashboard Button in Bottom Right -->
    <a href="userdash.php" class="back-to-dashboard-btn">Back to Dashboard</a>
    <!-- CSS to style stars, comments, and fixed button position -->
    <style>
        .back-to-dashboard-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .recipe-details {
            padding: 20px;
        }
        .stars {
            display: inline-block;
            direction: ltr;
        }
        .stars input {
            display: none;
        }
        .stars label {
            font-size: 2rem;
            color: #ccc;
            cursor: pointer;
        }
        .stars input:checked ~ label,
        .stars label:hover,
        .stars label:hover ~ label {
            color: gold;
        }
        .comment {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .comment small {
            color: #888;
        }
    </style>
</body>
</html>