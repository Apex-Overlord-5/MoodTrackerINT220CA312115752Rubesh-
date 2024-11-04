<?php
// Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'mood_tracker';

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mood = $_POST['mood'];
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO moods (mood, created_at) VALUES (?, ?)");
    $stmt->bind_param("ss", $mood, $created_at);
    $stmt->execute();
    $stmt->close();

    // Redirect to the dashboard after submitting mood
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood Tracker</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            font-family: Arial, sans-serif;
            color: #333;
        }

        /* Center Card */
        .card {
            border: none;
            border-radius: 15px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        /* Button Styling */
        .btn-custom {
            font-size: 1.2em;
            padding: 10px 20px;
            border-radius: 50px;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Dynamic Colors Based on Mood */
        .btn-happy:hover {
            background-color: #f0c419;
            color: #fff;
        }

        .btn-neutral:hover {
            background-color: #6c757d;
            color: #fff;
        }

        .btn-unhappy:hover {
            background-color: #e74c3c;
            color: #fff;
        }

        /* Dropdown styling */
        select {
            font-size: 1.1em;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: border-color 0.3s;
        }

        select:focus {
            border-color: #333;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }

        /* Text and Icon Styles */
        .mood-icon {
            font-size: 1.5em;
            margin-right: 8px;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Mood Submission Card -->
            <div class="card p-4 text-center">
                <h2 class="mb-4">How Are You Feeling Today?</h2>
                <form action="index.php" method="POST">
                    <div class="form-group">
                        <label for="mood" class="sr-only">Select Mood</label>
                        <select name="mood" id="mood" class="form-control form-control-lg" required>
                            <option value="" disabled selected>Select Mood</option>
                            <option value="Happy">😊 Happy</option>
                            <option value="Neutral">😐 Neutral</option>
                            <option value="Unhappy">☹️ Unhappy</option>
                        </select>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-custom btn-happy btn-lg">
                            <i class="fas fa-smile"></i> Submit Mood
                        </button>
                        <a href="dashboard.php" class="btn btn-custom btn-secondary btn-lg ml-3">
                            <i class="fas fa-chart-line"></i> View Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
