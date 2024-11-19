<?php
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
    header("Location: admin.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $userId = intval($_GET['delete_user']);
    $deleteUserQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $connection->prepare($deleteUserQuery);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: admin.php#users");
    exit;
}

// Fetch users data
$fetchUsersQuery = "SELECT id, username, email, created_at FROM users";
$usersResult = $connection->query($fetchUsersQuery);

$signupDataQuery = "SELECT DATE(created_at) AS signup_date, COUNT(*) AS total_signups FROM users GROUP BY signup_date ORDER BY signup_date ASC";
// Weekly User Signups
$weeklySignupsQuery = "SELECT DATE(created_at) AS signup_date, COUNT(*) AS total_signups 
                       FROM users 
                       WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                       GROUP BY signup_date 
                       ORDER BY signup_date ASC";

$weeklySignupsResult = $connection->query($weeklySignupsQuery);

// Recipe Category Distribution
$categoryDistributionQuery = "SELECT category, COUNT(*) AS total_recipes 
                              FROM recipeee 
                              GROUP BY category";

$categoryDistributionResult = $connection->query($categoryDistributionQuery);

// Fetch data for dashboard cards
$totalUsersQuery = "SELECT COUNT(*) AS total_users FROM users";
$newSignupsQuery = "SELECT COUNT(*) AS new_signups FROM users WHERE DATE(created_at) = CURDATE()";
$pendingRecipesQuery = "SELECT COUNT(*) AS pending_recipes FROM recipeee WHERE status = 'pending'";
$totalApprovedRecipesQuery = "SELECT COUNT(*) AS total_recipes FROM recipeee WHERE status = 'approved'";

$totalUsers = $connection->query($totalUsersQuery)->fetch_assoc()['total_users'];
$newSignups = $connection->query($newSignupsQuery)->fetch_assoc()['new_signups'];
$pendingRecipes = $connection->query($pendingRecipesQuery)->fetch_assoc()['pending_recipes'];
$totalRecipes = $connection->query($totalApprovedRecipesQuery)->fetch_assoc()['total_recipes'];

// Fetch pending recipes
$sql = "SELECT * FROM recipeee WHERE status = 'pending'";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    // Load the Google Charts library
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawCharts);

    function drawCharts() {
        // Chart for Recipe Status
        drawPieChart();

        // Chart for User Growth
        drawLineChart();
    }
    function drawCharts() {
    drawPieChart();
    drawLineChart();
    drawBarChart();
}

function drawBarChart() {
    const data = google.visualization.arrayToDataTable([
        ['Category', 'Recipes'],
        <?php
        while ($row = $categoryDistributionResult->fetch_assoc()) {
            echo "['" . $row['category'] . "', " . $row['total_recipes'] . "],";
        }
        ?>
    ]);

    const options = {
        title: 'Recipes by Category',
        chartArea: { width: '50%' },
        hAxis: {
            title: 'Number of Recipes',
            minValue: 0,
        },
        vAxis: {
            title: 'Category',
        },
    };

    const chart = new google.visualization.BarChart(document.getElementById('recipeCategoryChart'));
    chart.draw(data, options);
}

function drawLineChart() {
    const data = google.visualization.arrayToDataTable([
        ['Date', 'Signups'],
        <?php
        while ($row = $weeklySignupsResult->fetch_assoc()) {
            echo "['" . $row['signup_date'] . "', " . $row['total_signups'] . "],";
        }
        ?>
    ]);

    const options = {
        title: 'User Signups (Last 7 Days)',
        curveType: 'function',
        legend: { position: 'bottom' },
    };

    const chart = new google.visualization.LineChart(document.getElementById('weeklySignupsChart'));
    chart.draw(data, options);
}

    function drawPieChart() {
        const data = google.visualization.arrayToDataTable([
            ['Status', 'Count'],
            ['Pending', <?php echo $pendingRecipes; ?>],
            ['Approved', <?php echo $totalRecipes; ?>]
        ]);

        const options = {
            title: 'Recipe Status Distribution',
            is3D: true
        };

        const chart = new google.visualization.PieChart(document.getElementById('recipeStatusChart'));
        chart.draw(data, options);
    }

    function drawLineChart() {
        const data = google.visualization.arrayToDataTable([
            ['Date', 'New Signups'],
            <?php
            // Example: Fetching user signups data
            $signupDataQuery = "SELECT DATE(created_at) AS signup_date, COUNT(*) AS total_signups FROM users GROUP BY signup_date ORDER BY signup_date ASC";
            $signupDataResult = $connection->query($signupDataQuery);

            while ($row = $signupDataResult->fetch_assoc()) {
                echo "['" . $row['signup_date'] . "', " . $row['total_signups'] . "],";
            }
            ?>
        ]);

        const options = {
            title: 'New Signups Over Time',
            curveType: 'function',
            legend: { position: 'bottom' }
        };

        const chart = new google.visualization.LineChart(document.getElementById('userGrowthChart'));
        chart.draw(data, options);
    }
</script>

</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2>Admin</h2>
            <ul>
                <li><a href="admin.php">Dashboard</a></li>
                <li><a href="adminuserdash.php">Userdash</a></li>
                <li><a href="user_management.php">Users</a></li>
                <li><a href="archive_management.php">Archive</a></li>
                <li><a href="addmin.php">Add Admin</a></li>
                <li><a href="pendingrecipe.php">Pending recipe</a></li>
                <li><a href="login.php">Logout</a></li>
            
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="content">
            <!-- Header -->
            <header class="header">
                <h1>Dish-covery</h1>
                <a href="login.php" class="logout-btn btn btn-danger">Logout</a>
            </header>

            <!-- Overview Cards -->
            <section class="cards">
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
                <div class="card">
                    <h3>New Signups</h3>
                    <p><?php echo $newSignups; ?></p>
                </div>
                <div class="card">
                    <h3>Pending Recipes</h3>
                    <p><?php echo $pendingRecipes; ?></p>
                </div>
                <div class="card">
                    <h3>Total Recipes Posted</h3>
                    <p><?php echo $totalRecipes; ?></p>
                </div>
            </section>
            <section class="charts">
    <h2>Charts</h2>
    <div id="recipeStatusChart" style="width: 100%; height: 400px;"></div>
    <div id="userGrowthChart" style="width: 100%; height: 400px;"></div>
</section>
<section class="charts">
    <h2>Analytics</h2>
    <div class="chart-container">
        <div id="recipeStatusChart"></div>
    </div>
    <div class="chart-container">
        <div id="weeklySignupsChart"></div>
    </div>
    <div class="chart-container">
        <div id="recipeCategoryChart"></div>
    </div>
</section>

            <!-- Recipe Management Table -->
            <section class="tables">
                <h2>Pending Recipes</h2>
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
            </section>
        </main>
    </div>
    <style>
        <?php include 'admin.css'; ?>
    </style>
</body>
</html>

<?php
$connection->close();
?>