<?php
include_once('config.php');
include_once('BO/Movie.php');

$nowShowing = Movie::GetNowShowing();
$comingSoon = Movie::GetComingSoon();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MondyCinema - Kandy's Premier Movie Theatre</title>
    <link rel="stylesheet" href="assets/style/public_home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="logo">MondyCinema</div>
        <nav>
            <a href="index.php">Home</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="user-profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
                <a href="contact-us.php">Contact Us</a>
                <a href="add-feedback.php">Share your Feedbacks</a>
                <a href="view-feedbacks.php">CustomerFeedbacks</a>
            <?php endif; ?>
        </nav>
    </header>
    
    <section class="hero">
        <div class="hero-content">
            <h1>Experience Cinema Like Never Before</h1>
            <p>Kandy's premier destination for the latest blockbusters and cinematic experiences</p>
        </div>
    </section>

    <main class="container">
        <section class="movie-section">
            <h2>Now Showing</h2>
            <p class="scroll-indicator">← Scroll to see more movies →</p>
            <div class="movie-scroll-container">
                <?php foreach($nowShowing as $movie): ?>
                <div class="movie-card">
                    <img src="<?php echo htmlspecialchars(str_replace('../', '', $movie['poster'])); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" onerror="this.src='assets/posters/default.jpg'">
                    <div class="movie-info">
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <div class="movie-actions">
                            <a href="movie-details.php?id=<?php echo htmlspecialchars($movie['id']); ?>" class="btn">Book Now</a>
                            <a href="<?php echo htmlspecialchars($movie['trailer_link']); ?>" class="btn secondary" target="_blank">Trailer</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="movie-section">
            <h2>Coming Soon</h2>
            <p class="scroll-indicator">← Scroll to see more movies →</p>
            <div class="movie-scroll-container">
                <?php foreach($comingSoon as $movie): ?>
                <div class="movie-card">
                    <img src="<?php echo htmlspecialchars(str_replace('../', '', $movie['poster'])); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" onerror="this.src='assets/posters/default.jpg'">
                    <div class="movie-info">
                        <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <div class="movie-actions">
                            <a href="<?php echo htmlspecialchars($movie['trailer_link']); ?>" class="btn secondary" target="_blank">Trailer</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 MondyCinema. All rights reserved.</p>
    </footer>
</body>
</html>