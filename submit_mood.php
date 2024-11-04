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
    <!-- Optional Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Dynamic CSS */
        body {
            background-color: #f4f7fa;
            font-family: Arial, sans-serif;
        }
        
        .card {
            border-radius: 15px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .btn-secondary {
            transition: transform 0.2s ease;
        }

        .btn-secondary:hover {
            transform: scale(1.05);
        }

        .form-control-lg {
            font-size: 1.25rem;
        }

        /* Smooth transition for the entire form */
        form {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.6s ease forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
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
</body>
</html>
