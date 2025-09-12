<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include_once('../config.php');
include_once('../BO/Booking.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];
    $response = Booking::UpdateBookingStatus($booking_id, $status);
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$bookings = Booking::GetAllBookings('pending');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - MondyCinema</title>
    <link rel="stylesheet" href="../assets/style/manage-bookings.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <h1 class="app-name">Mondy Cinema</h1>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link">Dashboard Home</a></li>
                <li><a href="movie-manage.php" class="nav-link">Movie Management</a></li>
                <li><a href="bookings.php" class="nav-link active">Seat Bookings</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <header class="header">
                <h2 class="dashboard-title">Manage Bookings</h2>
                <div class="user-profile">
                    <a href="logout.php" class="logout-link">Logout</a>
                </div>
            </header>
            <div class="bookings-list">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer Name</th>
                            <th>Customer Phone</th>
                            <th>Movie</th>
                            <th>Theater</th>
                            <th>Date & Time</th>
                            <th>Seats</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['customer_phone']); ?></td>
                            <td><?php echo htmlspecialchars($booking['movie_title']); ?></td>
                            <td><?php echo htmlspecialchars($booking['theater_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['showdate'] . ' ' . $booking['showtime']); ?></td>
                            <td>
                                <?php 
                                    if (is_array($booking['seats'])) {
                                        echo htmlspecialchars(implode(', ', $booking['seats']));
                                    } else {
                                        echo htmlspecialchars($booking['seats']);
                                    }
                                ?>
                            </td>
                            <td class="status-<?php echo htmlspecialchars($booking['status']); ?>"><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></td>
                            <td class="action-buttons">
                                <?php if ($booking['status'] === 'pending'): ?>
                                <button class="btn confirm-btn" data-booking-id="<?php echo htmlspecialchars($booking['id']); ?>">Confirm</button>
                                <button class="btn secondary reject-btn" data-booking-id="<?php echo htmlspecialchars($booking['id']); ?>">Reject</button>
                                <?php else: ?>
                                    <span class="status-action-na">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center;">No pending bookings found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookingsTable = document.querySelector('.bookings-table');
            bookingsTable.addEventListener('click', function(e) {
                const targetBtn = e.target;
                const bookingId = targetBtn.dataset.bookingId;
                let status = '';
                if (targetBtn.classList.contains('confirm-btn')) {
                    status = 'confirmed';
                } else if (targetBtn.classList.contains('reject-btn')) {
                    status = 'cancelled';
                }
                if (bookingId && status) {
                    const formData = new FormData();
                    formData.append('booking_id', bookingId);
                    formData.append('status', status);
                    fetch('manage-bookings.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
                }
            });
        });
    </script>
</body>
</html>