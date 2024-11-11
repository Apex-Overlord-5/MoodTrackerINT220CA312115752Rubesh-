<?php
// Start the session at the beginning of the file
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

// Initialize variables for date and mood filters
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;
$mood_filter = $_GET['mood_filter'] ?? null;

// Pagination setup
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Export to CSV if requested
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="mood_data.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Mood', 'Submitted At']);

    $export_sql = "SELECT mood, created_at FROM moods";
    $conditions = [];
    if ($start_date && $end_date) {
        $conditions[] = "DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
    }
    if ($mood_filter) {
        $conditions[] = "mood = '$mood_filter'";
    }
    if ($conditions) {
        $export_sql .= " WHERE " . implode(" AND ", $conditions);
    }
    $export_sql .= " ORDER BY created_at DESC";

    $export_result = $conn->query($export_sql);
    while ($row = $export_result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}

// Main query to fetch mood records
$sql = "SELECT mood, created_at FROM moods";
$conditions = [];
if ($start_date && $end_date) {
    $conditions[] = "DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
}
if ($mood_filter) {
    $conditions[] = "mood = '$mood_filter'";
}
if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY created_at DESC LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);

// Count total records for pagination
$total_sql = "SELECT COUNT(*) FROM moods";
if ($conditions) {
    $total_sql .= " WHERE " . implode(" AND ", $conditions);
}
$total_result = $conn->query($total_sql);
$total_records = $total_result->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Your custom styles here */
    </style>
</head>
<body>
<div class="container my-5">
    <h1 class="text-center mb-4">Mood Dashboard</h1>

    <!-- Date and Mood Filter Form -->
    <div class="card p-4 mb-4">
        <form class="form-inline justify-content-center" action="dashboard.php" method="GET">
            <label for="start_date" class="mr-2">Start Date:</label>
            <input type="date" name="start_date" class="form-control mr-3" required>
            <label for="end_date" class="mr-2">End Date:</label>
            <input type="date" name="end_date" class="form-control mr-3" required>
            <label for="mood_filter" class="mr-2">Mood:</label>
            <select name="mood_filter" class="form-control mr-3">
                <option value="">All</option>
                <option value="Happy" <?php echo ($mood_filter == 'Happy') ? 'selected' : ''; ?>>Happy</option>
                <option value="Neutral" <?php echo ($mood_filter == 'Neutral') ? 'selected' : ''; ?>>Neutral</option>
                <option value="Unhappy" <?php echo ($mood_filter == 'Unhappy') ? 'selected' : ''; ?>>Unhappy</option>
            </select>
            <button type="submit" class="btn btn-primary btn-custom">Filter</button>
        </form>
    </div>

    <!-- Export Button -->
    <div class="text-center mb-4">
        <form action="dashboard.php" method="GET" style="display:inline;">
            <input type="hidden" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
            <input type="hidden" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
            <input type="hidden" name="export" value="csv">
            <button type="submit" class="btn btn-success btn-custom">Download as CSV</button>
        </form>
    </div>

    <!-- Mood Records Table with Pagination -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center">
            <thead class="thead-dark">
                <tr>
                    <th>Mood</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . htmlspecialchars($row['mood']) . "</td><td>" . $row['created_at'] . "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No moods recorded for the selected date range.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Controls -->
    <nav aria-label="Page navigation" class="d-flex justify-content-center">
        <ul class="pagination">
            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
                $active = ($i == $page) ? 'active' : '';
                echo "<li class='page-item $active'><a class='page-link' href='dashboard.php?page=$i&start_date=$start_date&end_date=$end_date&mood_filter=$mood_filter'>$i</a></li>";
            }
            ?>
        </ul>
    </nav>

    <?php
    // Mood Summary and Average Score
    $happy_count = $neutral_count = $unhappy_count = 0;
    $total_score = $total_moods = 0;

    $count_sql = "SELECT mood, COUNT(*) as count FROM moods";
    if ($conditions) {
        $count_sql .= " WHERE " . implode(" AND ", $conditions);
    }
    $count_sql .= " GROUP BY mood";
    $count_result = $conn->query($count_sql);

    if ($count_result && $count_result->num_rows > 0) {
        while ($row = $count_result->fetch_assoc()) {
            switch ($row['mood']) {
                case 'Happy':
                    $happy_count = $row['count'];
                    $total_score += $row['count'] * 3;
                    break;
                case 'Neutral':
                    $neutral_count = $row['count'];
                    $total_score += $row['count'] * 2;
                    break;
                case 'Unhappy':
                    $unhappy_count = $row['count'];
                    $total_score += $row['count'] * 1;
                    break;
            }
            $total_moods += $row['count'];
        }
    }
    $average_score = $total_moods ? round($total_score / $total_moods, 2) : 0;
    ?>
    <!-- Mood Submission Section -->
<div class="text-center mb-4">
    <a href="submit_mood.php" class="btn btn-primary btn-lg">Submit Your Mood</a>
</div>

<!-- Logout Button in Dashboard.php -->
<div style="display: flex; justify-content: center; padding: 20px;">
    <form action="logout.php" method="post">
        <button type="submit" class="btn btn-danger">Logout</button>
    </form>
</div>





<!-- Display Mood Summary and Average Score -->
<div class="card p-4 mb-4">
    <h2 class="text-center">Mood Summary</h2>
    <div class="d-flex justify-content-between px-4">
        <p><strong>Happy:</strong> <?php echo $happy_count; ?></p>
        <p><strong>Neutral:</strong> <?php echo $neutral_count; ?></p>
        <p><strong>Unhappy:</strong> <?php echo $unhappy_count; ?></p>
    </div>
    <p class="text-center"><strong>Average Mood Score:</strong> <?php echo $average_score; ?></p>
</div>

<!-- Feedback Button -->
<?php if ($average_score < 2): ?>
    <div class="text-center mb-4">
        <a href="feedback.php?score=<?php echo $average_score; ?>" class="btn btn-warning btn-lg">Get Feedback</a>
    </div>
<?php endif; ?>



<?php
// Close database connection
$conn->close();
?>
