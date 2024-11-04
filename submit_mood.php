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
    <title>Submit Mood</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Card for Mood Submission -->
            <div class="card shadow-sm p-4">
                <h2 class="text-center mb-4">Submit Your Mood</h2>
                
                <form action="submit_mood.php" method="POST">
                    <!-- Mood Selection -->
                    <div class="form-group">
                        <label for="mood" class="font-weight-bold">How are you feeling today?</label>
                        <select name="mood" id="mood" class="form-control form-control-lg" required>
                            <option value="" disabled selected>Select your mood</option>
                            <option value="Happy">😊 Happy</option>
                            <option value="Neutral">😐 Neutral</option>
                            <option value="Unhappy">☹️ Unhappy</option>
                        </select>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-smile"></i> Submit Mood
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg ml-3">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Optional Font Awesome Icons for a more polished look -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</body>
</html>
