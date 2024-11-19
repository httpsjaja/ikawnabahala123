<?php
session_start();

$host = 'localhost';
$dbname = 'dishcovery';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $e->getMessage()]));
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Session user_id is not set"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $day = $_POST['day'];
    $description = $_POST['description'];
    $breakfast = $_POST['breakfast'];
    $lunch = $_POST['lunch'];
    $snack = $_POST['snack'];
    $dinner = $_POST['dinner'];

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $day)) {
        echo json_encode(["status" => "error", "message" => "Invalid date format. Please use YYYY-MM-DD."]);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO meal_plans (user_id, day, description, breakfast, lunch, snack, dinner) VALUES (:user_id, :day, :description, :breakfast, :lunch, :snack, :dinner)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':day', $day);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':breakfast', $breakfast);
    $stmt->bindParam(':lunch', $lunch);
    $stmt->bindParam(':snack', $snack);
    $stmt->bindParam(':dinner', $dinner);

    try {
        if ($stmt->execute()) {
            $insertedId = $pdo->lastInsertId();
            $newMealPlan = [
                "id" => $insertedId,
                "user_id" => $user_id,
                "day" => $day,
                "description" => $description,
                "breakfast" => $breakfast,
                "lunch" => $lunch,
                "snack" => $snack,
                "dinner" => $dinner
            ];
            echo json_encode(["status" => "success", "message" => "Meal plan submitted successfully!", "mealPlan" => $newMealPlan]);
        } else {
            echo json_encode(["status" => "error", "message" => "Could not submit meal plan.", "error_info" => $stmt->errorInfo()]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE user_id = :user_id ORDER BY day");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $mealPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($mealPlans);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meal Planner</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="mealplan.css">
</head>
<body>
  <div class="container mt-5">
    <h1 class="text-center mb-4">Meal Planning</h1>

    <!-- Meal Plan Form -->
    <div class="card p-4">
      <form id="mealPlanForm">
        <div class="mb-3">
          <label for="dayInput" class="form-label">Day:</label>
          <input type="date" class="form-control" id="dayInput" required>
        </div>
        <div class="mb-3">
          <label for="descriptionInput" class="form-label">Description:</label>
          <input type="text" class="form-control" id="descriptionInput" placeholder="Enter meal description" required>
        </div>
        <div class="mb-3">
          <label for="breakfastInput" class="form-label">Breakfast:</label>
          <input type="text" class="form-control" id="breakfastInput" placeholder="Enter breakfast plan" required>
        </div>
        <div class="mb-3">
          <label for="lunchInput" class="form-label">Lunch:</label>
          <input type="text" class="form-control" id="lunchInput" placeholder="Enter lunch plan" required>
        </div>
        <div class="mb-3">
          <label for="snackInput" class="form-label">Snack:</label>
          <input type="text" class="form-control" id="snackInput" placeholder="Enter snack plan" required>
        </div>
        <div class="mb-3">
          <label for="dinnerInput" class="form-label">Dinner:</label>
          <input type="text" class="form-control" id="dinnerInput" placeholder="Enter dinner plan" required>
        </div>
        <button type="button" class="btn btn-primary" onclick="submitMealPlan()">Submit Meal Plan</button>
      </form>
    </div>

    <!-- Displayed Meal Plans -->
    <div class="mt-5">
      <h3 class="mb-3">Saved Meal Plans</h3>
      <div id="mealPlansContainer"></div>
    </div>
  </div>

  <script>
    // Fetch meal plans from the backend when the page loads
    window.onload = function() {
      fetchMealPlans();
    }

    function submitMealPlan() {
      const day = document.getElementById('dayInput').value;
      const description = document.getElementById('descriptionInput').value;
      const breakfast = document.getElementById('breakfastInput').value;
      const lunch = document.getElementById('lunchInput').value;
      const snack = document.getElementById('snackInput').value;
      const dinner = document.getElementById('dinnerInput').value;

      const mealPlan = { day, description, breakfast, lunch, snack, dinner };

      // Send the meal plan to the server via POST
      fetch('mealplan.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(mealPlan),
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          alert(data.message);
          clearForm();
          fetchMealPlans();  // Refresh the displayed meal plans
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }

    function clearForm() {
      document.getElementById('mealPlanForm').reset();
    }

    function fetchMealPlans() {
      fetch('mealplan.php')
        .then(response => response.json())
        .then(data => {
          if (Array.isArray(data)) {
            displayMealPlans(data);
          } else {
            console.error('Error: ', data.message);
          }
        })
        .catch(error => {
          console.error('Error fetching meal plans:', error);
        });
    }

    function displayMealPlans(mealPlans) {
      const container = document.getElementById('mealPlansContainer');
      container.innerHTML = ''; // Clear the container

      mealPlans.forEach((plan, index) => {
        const mealPlanElement = document.createElement('div');
        mealPlanElement.className = 'card mb-2';
        mealPlanElement.innerHTML = `
          <div class="card-body">
            <h5 class="card-title">${plan.day}: ${plan.description}</h5>
            <p class="card-text"><strong>Breakfast:</strong> ${plan.breakfast}</p>
            <p class="card-text"><strong>Lunch:</strong> ${plan.lunch}</p>
            <p class="card-text"><strong>Snack:</strong> ${plan.snack}</p>
            <p class="card-text"><strong>Dinner:</strong> ${plan.dinner}</p>
          </div>
        `;
        container.appendChild(mealPlanElement);
      });
    }
  </script>

  <script src="mealplan.js"></script>
</body>
</html>

