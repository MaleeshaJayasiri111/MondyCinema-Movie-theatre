<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include_once('../config.php');

function getStats()
{
    global $conn;
    $stats = [
        'movies' => 0,
        'bookings' => 0,
        'theaters' => 0,
        'pending_bookings' => 0
    ];

    if ($conn === null) {
        return $stats;
    }

    $result = $conn->query("SELECT COUNT(*) as count FROM movies");
    if ($result) {
        $stats['movies'] = $result->fetch_assoc()['count'];
    }

    $result = $conn->query("SELECT COUNT(*) as count FROM bookings");
    if ($result) {
        $stats['bookings'] = $result->fetch_assoc()['count'];
    }

    $result = $conn->query("SELECT COUNT(*) as count FROM theaters");
    if ($result) {
        $stats['theaters'] = $result->fetch_assoc()['count'];
    }

    $result = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
    if ($result) {
        $stats['pending_bookings'] = $result->fetch_assoc()['count'];
    }

    return $stats;
}

$stats = getStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style/admin-dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h1 class="app-name">MondyCinema</h1>
            <ul class="nav-menu">
                <li><a href="dashboard.php?page=home" class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : ''; ?>">Dashboard Home</a></li>
                <li><a href="movie-manage.php" class="nav-link">Movie Management</a></li>
                <li><a href="dashboard.php?page=bookings" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'bookings') ? 'active' : ''; ?>">Seat Bookings</a></li>
                <li><a href="dashboard.php?page=customers" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'customers') ? 'active' : ''; ?>">Our Customers</a></li>
                <li><a href="dashboard.php?page=feedbacks" class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'feedbacks') ? 'active' : ''; ?>">View Feedbacks</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="header">
                <h2 class="dashboard-title">
                    <?php
                    $page = $_GET['page'] ?? 'home';
                    if ($page == 'home') echo 'Dashboard Home';
                    if ($page == 'bookings') echo 'Seat Bookings';
                    if ($page == 'customers') echo 'Our Customers';
                    if ($page == 'feedbacks') echo 'View Feedbacks';
                    ?>
                </h2>
                <div class="user-profile">
                    <a href="logout.php" class="logout-link">Logout</a>
                </div>
            </header>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="message success"><?php echo $_SESSION['success_message']; ?></div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="message error"><?php echo $_SESSION['error_message']; ?></div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            <?php
            $page = $_GET['page'] ?? 'home';
            if ($page == 'home') {
                echo '<div class="card-grid">';
                echo '<div class="card"><h3>Total Movies</h3><p>' . htmlspecialchars($stats['movies']) . '</p></div>';
                echo '<div class="card"><h3>Total Bookings</h3><p>' . htmlspecialchars($stats['bookings']) . '</p></div>';
                echo '<div class="card"><h3>Theaters</h3><p>' . htmlspecialchars($stats['theaters']) . '</p></div>';
                echo '<a href="manage-bookings.php" class="card pending-bookings-card"><h3>Pending Bookings</h3><p class="pending-count">' . htmlspecialchars($stats['pending_bookings']) . '</p></a>';
                echo '</div>';
            } elseif ($page == 'bookings') {
                include('bookings.php');
            } elseif ($page == 'customers') {
                include('view_customers.php');
            } elseif ($page == 'feedbacks') {
                include('feedbacks.php');
            }
            ?>
        </main>
    </div>
</body>
</html>
