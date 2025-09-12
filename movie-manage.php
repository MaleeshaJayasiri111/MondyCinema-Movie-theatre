<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

include_once('../config.php');
include_once('../BO/Movie.php');
include_once('../BO/Showtime.php');

$message = '';
$editMovie = null;
$showtimes = [];

if (isset($_GET['edit'])) {
    $editMovie = Movie::GetMovieById($_GET['edit']);
    if ($editMovie) {
        $showtimes = Showtime::GetShowtimesByMovie($editMovie['id']);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_movie']) || isset($_POST['update_movie'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $trailer_link = $_POST['trailer_link'];
        $status = $_POST['status'];
        $ticket_price = $_POST['ticket_price'];
        $isUpdate = isset($_POST['update_movie']);

        if (empty($title) || empty($description) || empty($trailer_link) || empty($status) || empty($ticket_price)) {
            $message = "All movie details fields are required!";
        } else {
            $poster = $isUpdate ? $editMovie['poster'] : '';

            if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
                $target_dir = "../assets/posters/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $original_name = $_FILES["poster"]["name"];
                $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'jfif']; 
                if (in_array($file_extension, $allowed_extensions)) {
                    $sanitized_name = preg_replace("/[^a-zA-Z0-9]/", "_", $title);
                    $new_filename = $sanitized_name . '_' . time() . '.' . $file_extension;
                    $poster_path = $target_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES["poster"]["tmp_name"], $poster_path)) {
                        $poster = '../assets/posters/' . $new_filename;
                    }
                }
            } else if (!$isUpdate) {
                $poster = '../assets/posters/default.jpg';
            }

            $success = false;
            $movieId = null;
            if ($isUpdate) {
                $id = $_POST['id'];
                $success = Movie::UpdateMovie($id, $title, $description, $poster, $trailer_link, $status, $ticket_price);
                $movieId = $id;
            } else {
                $success = Movie::AddMovie($title, $description, $poster, $trailer_link, $status, $ticket_price);
                if ($success) {
                    $movieId = Movie::GetLastInsertedId();
                }
            }

            if ($success) {
                if (isset($_POST['theater_id']) && !empty($_POST['theater_id'][0])) {
                    $theater_ids = $_POST['theater_id'];
                    $showdates = $_POST['showdate'];
                    $showtimes_arr = $_POST['showtime'];
                    
                    Showtime::DeleteShowtimesByMovie($movieId);

                    if (is_array($theater_ids) && is_array($showdates) && is_array($showtimes_arr)) {
                        foreach ($theater_ids as $key => $theater_id) {
                            if (!empty($theater_id) && !empty($showdates[$key]) && !empty($showtimes_arr[$key])) {
                                Showtime::AddShowtime($movieId, $theater_id, $showdates[$key], $showtimes_arr[$key]);
                            }
                        }
                    }
                }
                $message = $isUpdate ? "Movie updated successfully!" : "Movie added successfully!";
            } else {
                $message = $isUpdate ? "Error updating movie!" : "Error adding movie!";
            }
        }
    }
    
    if (isset($_POST['delete_movie'])) {
        $id = $_POST['id'];
        
        Showtime::DeleteShowtimesByMovie($id);
        
        if (Movie::DeleteMovie($id)) {
            $message = "Movie deleted successfully!";
        } else {
            $message = "Error deleting movie!";
        }
    }
}

$movies = Movie::GetAllMovies();
$theaters = Showtime::GetAllTheaters();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Movies - MondyCinema</title>
    <link rel="stylesheet" href="../assets/style/admin-movie-manage.css">
</head>
<body>
    <header>
        <h1>MondyCinema Admin</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="movie-manage.php">Movies</a>
            <a href="bookings.php">Bookings</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    
    <main class="container">
        <h2>Manage Movies</h2>
        
        <?php if ($message): ?>
            <p class="<?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        
        <div class="form-section">
            <h3><?php echo $editMovie ? 'Edit Movie' : 'Add New Movie'; ?></h3>
            <form method="post" enctype="multipart/form-data" id="movieForm">
                <?php if ($editMovie): ?>
                    <input type="hidden" name="id" value="<?php echo $editMovie['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="title">Movie Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo $editMovie ? $editMovie['title'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required><?php echo $editMovie ? $editMovie['description'] : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="poster">Movie Poster:</label>
                    <input type="file" id="poster" name="poster" accept="image/*" <?php echo !$editMovie ? 'required' : ''; ?>>
                    <?php if ($editMovie && $editMovie['poster']): ?>
                        <p>Current poster:</p>
                        <img src="<?php echo $editMovie['poster']; ?>" alt="Current poster" class="movie-poster-edit">
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="trailer_link">Trailer Link:</label>
                    <input type="url" id="trailer_link" name="trailer_link" value="<?php echo $editMovie ? $editMovie['trailer_link'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="ticket_price">Ticket Price:</label>
                    <input type="text" id="ticket_price" name="ticket_price" list="ticket_prices" value="<?php echo $editMovie ? $editMovie['ticket_price'] : ''; ?>">
                    <datalist id="ticket_prices">
                        <option value="2500">
                        <option value="3000">
                        <option value="3500">
                        <option value="4000">
                    </datalist>
                </div>
                
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="">Select Movie Status</option>
                        <option value="now_showing" <?php echo $editMovie && $editMovie['status'] == 'now_showing' ? 'selected' : ''; ?>>Now Showing</option>
                        <option value="coming_soon" <?php echo $editMovie && $editMovie['status'] == 'coming_soon' ? 'selected' : ''; ?>>Coming Soon</option>
                    </select>
                </div>
                
                <div id="showtimes-container">
                    <h4>Showtimes</h4>
                    <div id="showtime-fields-container">
                        <?php if ($editMovie && !empty($showtimes)): ?>
                            <?php foreach ($showtimes as $showtime): ?>
                                <div class="showtime-row">
                                    <div class="form-group">
                                        <label for="theater_id">Theater:</label>
                                        <select name="theater_id[]" required>
                                            <?php foreach ($theaters as $theater): ?>
                                                <option value="<?php echo $theater['id']; ?>" <?php echo $theater['id'] == $showtime['theater_id'] ? 'selected' : ''; ?>><?php echo $theater['name']; ?> (Capacity: <?php echo $theater['capacity']; ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="showdate">Date:</label>
                                        <input type="date" name="showdate[]" value="<?php echo htmlspecialchars($showtime['showdate']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="showtime">Time:</label>
                                        <input type="time" name="showtime[]" value="<?php echo htmlspecialchars($showtime['showtime']); ?>" required>
                                    </div>
                                    <button type="button" class="remove-showtime-btn">Remove</button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="showtime-row">
                                <div class="form-group">
                                    <label for="theater_id">Theater:</label>
                                    <select name="theater_id[]" required>
                                        <option value="">Select a Theater</option>
                                        <?php foreach ($theaters as $theater): ?>
                                            <option value="<?php echo $theater['id']; ?>"><?php echo $theater['name']; ?> (Capacity: <?php echo $theater['capacity']; ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="showdate">Date:</label>
                                    <input type="date" name="showdate[]" required>
                                </div>
                                <div class="form-group">
                                    <label for="showtime">Time:</label>
                                    <input type="time" name="showtime[]" required>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="add-showtime-btn" class="btn secondary">Add Another Showtime</button>
                </div>
                
                <div class="button-container">
                    <?php if ($editMovie): ?>
                        <button type="submit" name="update_movie" class="btn">Update Movie</button>
                        <a href="movie-manage.php" class="btn secondary">Cancel</a>
                    <?php else: ?>
                        <button type="submit" name="add_movie" class="btn">Add Movie</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="movies-list">
            <h3>All Movies</h3>
            <table>
                <thead>
                    <tr>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td><img src="<?php echo $movie['poster']; ?>" alt="<?php echo $movie['title']; ?>" class="movie-poster-thumb"></td>
                        <td><?php echo $movie['title']; ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $movie['status'])); ?></td>
                        <td class="action-buttons">
                            <a href="movie-manage.php?edit=<?php echo $movie['id']; ?>" class="btn">Edit</a>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this movie?');">
                                <input type="hidden" name="id" value="<?php echo $movie['id']; ?>">
                                <button type="submit" name="delete_movie" class="btn secondary">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addShowtimeBtn = document.getElementById('add-showtime-btn');
            const showtimeFieldsContainer = document.getElementById('showtime-fields-container');
            const theaters = <?php echo json_encode($theaters); ?>;
            const theaterOptions = theaters.map(theater => `<option value="${theater.id}">${theater.name} (Capacity: ${theater.capacity})</option>`).join('');

            function createShowtimeRow() {
                const newRow = document.createElement('div');
                newRow.classList.add('showtime-row');
                newRow.innerHTML = `
                    <div class="form-group">
                        <label>Theater:</label>
                        <select name="theater_id[]" required>
                            <option value="">Select a Theater</option>
                            ${theaterOptions}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date:</label>
                        <input type="date" name="showdate[]" required>
                    </div>
                    <div class="form-group">
                        <label>Time:</label>
                        <input type="time" name="showtime[]" required>
                    </div>
                    <button type="button" class="remove-showtime-btn">Remove</button>
                `;
                showtimeFieldsContainer.appendChild(newRow);
            }

            addShowtimeBtn.addEventListener('click', createShowtimeRow);

            showtimeFieldsContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-showtime-btn')) {
                    e.target.closest('.showtime-row').remove();
                }
            });
        });
    </script>
</body>
</html>