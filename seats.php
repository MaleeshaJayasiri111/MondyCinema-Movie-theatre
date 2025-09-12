<?php
include_once('config.php');
include_once('BO/Show.php');

if (!isset($_GET['show_id'])) {
    header("Location: movies.php");
    exit();
}

$show_id = $_GET['show_id'];
$show = $con->query("
    SELECT s.*, m.title as movie_title, t.name as theater_name, t.capacity 
    FROM shows s 
    JOIN movies m ON s.movie_id = m.id 
    JOIN theaters t ON s.theater_id = t.id 
    WHERE s.id = $show_id
")->fetch_assoc();

if (!$show) {
    header("Location: movies.php");
    exit();
}

$booked_seats = array();
$bookings_result = $con->query("SELECT seats FROM bookings WHERE show_id = $show_id AND status != 'cancelled'");
while ($booking = $bookings_result->fetch_assoc()) {
    $seats = explode(',', $booking['seats']);
    $booked_seats = array_merge($booked_seats, $seats);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['seats'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_to'] = "seats.php?show_id=$show_id";
        header("Location: login.php");
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $seats = implode(',', $_POST['seats']);
    
    $stmt = $con->prepare("INSERT INTO bookings (user_id, show_id, seats) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $show_id, $seats);
    $stmt->execute();
    $stmt->close();
    
    header("Location: user-profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - MondyCinema</title>
    <link rel="stylesheet" href="assets/style/public.css">
</head>
<body>
    <header>
        <div class="logo">MondyCinema</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="movies.php">Movies</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="user-profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    
    <main class="container">
        <h1>Select Seats for <?php echo $show['movie_title']; ?></h1>
        <p>Theater: <?php echo $show['theater_name']; ?> | Date: <?php echo date('l, F j', strtotime($show['showdate'])); ?> | Time: <?php echo date('g:i A', strtotime($show['showtime'])); ?></p>
        
        <form method="post">
            <div class="seat-map">
                <?php
                $rows = ceil($show['capacity'] / 10);
                for ($i = 1; $i <= $show['capacity']; $i++):
                    $is_booked = in_array($i, $booked_seats);
                ?>
                <div class="seat <?php echo $is_booked ? 'booked' : 'available'; ?>">
                    <?php if (!$is_booked): ?>
                        <input type="checkbox" name="seats[]" value="<?php echo $i; ?>" id="seat-<?php echo $i; ?>">
                        <label for="seat-<?php echo $i; ?>"><?php echo $i; ?></label>
                    <?php else: ?>
                        <span><?php echo $i; ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($i % 10 == 0) echo '<br>'; ?>
                <?php endfor; ?>
            </div>
            
            <div class="screen">Screen</div>
            
            <button type="submit" class="btn">Book Selected Seats</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 MondyCinema. All rights reserved.</p>
    </footer>
</body>
</html>