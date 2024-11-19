google.charts.load('current', { packages: ['corechart'] });
google.charts.setOnLoadCallback(drawCharts);

function drawCharts() {
    drawRecipeStatusChart();
    drawUserGrowthChart();
    drawRecipeCategoryChart();
}

// Recipe Status Chart
function drawRecipeStatusChart() {
    const data = google.visualization.arrayToDataTable([
        ['Status', 'Number of Recipes'],
        ['Approved', <?php echo $totalRecipes; ?>],
        ['Pending', <?php echo $pendingRecipes; ?>],
    ]);

    const options = { title: 'Recipe Status Distribution', pieHole: 0.4 };
    const chart = new google.visualization.PieChart(document.getElementById('recipeStatusChart'));
    chart.draw(data, options);
}

// User Growth Chart
function drawUserGrowthChart() {
    const data = google.visualization.arrayToDataTable([
        ['Date', 'Signups'],
        <?php
        while ($row = $weeklySignupsResult->fetch_assoc()) {
            echo "['" . $row['signup_date'] . "', " . $row['total_signups'] . "],";
        }
        ?>
    ]);

    const options = { title: 'User Growth in the Last Week', hAxis: { title: 'Date' }, vAxis: { title: 'Signups' } };
    const chart = new google.visualization.ColumnChart(document.getElementById('userGrowthChart'));
    chart.draw(data, options);
}

// Recipe Category Chart
function drawRecipeCategoryChart() {
    const data = google.visualization.arrayToDataTable([
        ['Category', 'Recipes'],
        <?php
        while ($row = $categoryDistributionResult->fetch_assoc()) {
            echo "['" . $row['category'] . "', " . $row['total_recipes'] . "],";
        }
        ?>
    ]);

    const options = { title: 'Recipes by Category', is3D: true };
    const chart = new google.visualization.PieChart(document.getElementById('recipeCategoryChart'));
    chart.draw(data, options);
}
