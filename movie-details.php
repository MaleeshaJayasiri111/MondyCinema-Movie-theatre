<?php
include_once('config.php');
include_once('BO/Movie.php');
include_once('BO/Show.php');

if (!isset($_GET['id'])) {
    header("Location: movies.php");
    exit();
}

$movie = Movie::GetMovieById($_GET['id']);
if (!$movie) {
    header("Location: movies.php");
    exit();
}

$shows = Show::GetShowsByMovie($movie['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - MondyCinema</title>
    <link rel="stylesheet" href="assets/style/movie-details.css">
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
            <?php endif; ?>
        </nav>
    </header>
    
    <main class="container">
        <div class="movie-details">
            <div class="poster">
                <img src="<?php echo htmlspecialchars(str_replace('../', '', $movie['poster'])); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>" onerror="this.src='assets/posters/default.jpg'">
            </div>
            <div class="details">
                <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
                <p><?php echo htmlspecialchars($movie['description']); ?></p>
                
                <?php if ($movie['trailer_link']): ?>
                <div class="trailer">
                    <h2>Trailer</h2>
                    <a href="<?php echo htmlspecialchars($movie['trailer_link']); ?>" class="btn" target="_blank">Watch Trailer</a>
                </div>
                <?php endif; ?>
                
                <div class="showtimes">
                    <h2>Showtimes</h2>
                    <?php if (!empty($shows)): ?>
                        <?php foreach ($shows as $show): ?>
                        <div class="showtime-item">
                            <h3><?php echo htmlspecialchars($show['theater_name']); ?></h3>
                            <p><?php echo htmlspecialchars(date('l, F j', strtotime($show['showdate']))); ?> at <?php echo htmlspecialchars(date('g:i A', strtotime($show['showtime']))); ?></p>
                            <a href="register.php" class="btn">Book Tickets</a>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No showtimes available for this movie.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 MondyCinema. All rights reserved.</p>
    </footer>
</body>
</html>