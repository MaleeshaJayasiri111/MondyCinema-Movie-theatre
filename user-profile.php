<?php
session_start();
include_once('config.php');
include_once('BO/User.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = User::GetUserById($user_id);
$success_message = '';

if (isset($_SESSION['update_success'])) {
    $success_message = "Profile updated successfully!";
    unset($_SESSION['update_success']);
}

$bookings = $conn->query("
    SELECT b.*, m.title as movie_title, s.showdate, s.showtime, t.name as theater_name 
    FROM bookings b 
    JOIN shows s ON b.show_id = s.id 
    JOIN movies m ON s.movie_id = m.id 
    JOIN theaters t ON s.theater_id = t.id 
    WHERE b.user_id = $user_id 
    ORDER BY b.booking_date DESC
");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    if (User::UpdateUser($user_id, $name, $email)) {
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['update_success'] = true;
        header("Location: user-profile.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - MondyCinema</title>
    <link rel="stylesheet" href="assets/style/user-profile.css">
    <style>
        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">MondyCinema</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    
    <main class="container">
        <div class="profile-container">
            <h2>User Profile</h2>
            <?php if ($success_message): ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php endif; ?>
            
            <div class="profile-info">
                <form method="post">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                    
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                    
                    <button type="submit">Update Profile</button>
                </form>
            </div>
            
            <div class="booking-history">
                <h3>Booking History</h3>
                
                <?php if ($bookings && $bookings->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Movie</th>
                                <th>Theater</th>
                                <th>Date & Time</th>
                                <th>Seats</th>
                                <th>Status</th>
                                <th>Booking Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($booking = $bookings->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $booking['movie_title']; ?></td>
                                <td><?php echo $booking['theater_name']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($booking['showdate'])); ?> at <?php echo date('g:i A', strtotime($booking['showtime'])); ?></td>
                                <td><?php echo $booking['seats']; ?></td>
                                <td><?php echo ucfirst($booking['status']); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($booking['booking_date'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No bookings found.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="back-button-container">
            <a href="bookseats.php" class="back-button">Back</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 MondyCinema. All rights reserved.</p>
    </footer>
</body>
</html>