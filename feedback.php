<?php
// Get the score from the URL
$score = isset($_GET['score']) ? (float)$_GET['score'] : 0;

// Set the feedback message based on the score
if ($score < 2) {
    $feedback_message = "Your mood score is below 2. You may need serious mental attention. Please consider seeking help from a professional.";
} elseif ($score >= 2 && $score <= 5) {
    $feedback_message = "Your mood score is between 2 and 5. You need some mild work, but keep going. Focus on improving yourself gradually.";
} else {
    $feedback_message = "Your mood score is above 5.01. You're doing well! Keep going and maintaining a positive outlook!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="text-center mb-4">Feedback</h1>

    <div class="card p-4 mb-4">
        <p class="text-center"><?php echo $feedback_message; ?></p>
    </div>

    <div class="text-center">
        <a href="dashboard.php" class="btn btn-primary btn-lg">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
