<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

include_once('../BO/Booking.php');
include_once('../config.php');

function getBookedSeats($conn) {
    if (!$conn) {
        return [];
    }
    $bookedSeats = [];
    $sql = "SELECT seats FROM bookings WHERE status IN ('confirmed', 'pending')";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $seats = explode(',', $row['seats']);
            foreach ($seats as $seat) {
                $bookedSeats[] = trim($seat);
            }
        }
    }
    return $bookedSeats;
}

function getAllBookings($conn) {
    $bookings = [];
    $sql = "SELECT b.id, b.customer_name, b.customer_phone, m.title AS movie_title, t.name AS theater_name, s.showdate, s.showtime, b.seats, b.status, b.booking_date FROM bookings b LEFT JOIN shows s ON b.show_id = s.id LEFT JOIN movies m ON s.movie_id = m.id LEFT JOIN theaters t ON s.theater_id = t.id ORDER BY b.booking_date DESC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    return $bookings;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['seats']) && !empty($_POST['seats'])) {
        $seats = $_POST['seats'];
        $seats_string = implode(',', $seats);
        $customer_name = $_POST['customer_name'];
        $customer_phone = $_POST['customer_phone'];
        $movie_name = $_POST['movie_name'];
        $show_id = 0;
        $status = 'confirmed';
        
        $sql_movie = "SELECT id FROM movies WHERE title = ?";
        $stmt_movie = $conn->prepare($sql_movie);
        $stmt_movie->bind_param("s", $movie_name);
        $stmt_movie->execute();
        $result_movie = $stmt_movie->get_result();
        if ($result_movie->num_rows > 0) {
            $movie_data = $result_movie->fetch_assoc();
            $movie_id = $movie_data['id'];
        } else {
            die("Error: Movie not found.");
        }
        $stmt_movie->close();
        
        $sql_show = "SELECT id FROM shows WHERE movie_id = ? ORDER BY id DESC LIMIT 1";
        $stmt_show = $conn->prepare($sql_show);
        $stmt_show->bind_param("i", $movie_id);
        $stmt_show->execute();
        $result_show = $stmt_show->get_result();
        if ($result_show->num_rows > 0) {
            $show_data = $result_show->fetch_assoc();
            $show_id = $show_data['id'];
        } else {
            die("Error: No show found for this movie.");
        }
        $stmt_show->close();
        
        $sql = "INSERT INTO bookings (customer_name, customer_phone, show_id, seats, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiss", $customer_name, $customer_phone, $show_id, $seats_string, $status);
        if ($stmt->execute()) {
            echo "<script>alert('Seats booked successfully!'); window.location.href='bookings.php';</script>";
        } else {
            echo "<script>alert('Error booking seats: " . $conn->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please select at least one seat.');</script>";
    }
}

$bookedSeats = getBookedSeats($conn);
$allBookings = getAllBookings($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Seat Booking</title>
    <link rel="stylesheet" href="../assets/style/admin-book-seats.css">
</head>
<body>
    <div class="main-container">
        <h1>Admin Seat Booking</h1>
        <div class="seat-map-container">
            <div class="screen">
                <p>Screen</p>
            </div>
            <form id="booking-form" action="bookings.php" method="POST">
                <div class="seat-grid">
                    <?php
                    $totalSeats = 60;
                    $seatsPerRow = 10;
                    for ($i = 1; $i <= $totalSeats; $i++) {
                        $seatNumber = (string)$i;
                        $isBooked = in_array($seatNumber, $bookedSeats);
                        $seatClass = $isBooked ? 'seat booked' : 'seat available';
                        $disabled = $isBooked ? 'disabled' : '';
                        echo '<div class="' . $seatClass . '" data-seat-number="' . $seatNumber . '">';
                        echo '<input type="checkbox" name="seats[]" value="' . $seatNumber . '" ' . $disabled . '>';
                        echo '<span class="seat-label">' . $i . '</span>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="legend">
                    <div class="legend-item"><div class="legend-box available"></div>Available</div>
                    <div class="legend-item"><div class="legend-box booked"></div>Booked</div>
                    <div class="legend-item"><div class="legend-box selected"></div>Selected</div>
                </div>
                <div class="customer-details">
                    <label for="customer_name">Customer Name:</label>
                    <input type="text" id="customer_name" name="customer_name" required>
                    <label for="customer_phone">Phone Number:</label>
                    <input type="text" id="customer_phone" name="customer_phone" required>
                    <label for="movie_name">Movie Name:</label>
                    <input type="text" id="movie_name" name="movie_name" required>
                    <button type="submit">Book Seats</button>
                </div>
            </form>
            <div class="table-container">
                <h2>All Bookings</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Movie</th>
                            <th>Theater</th>
                            <th>Seats</th>
                            <th>Show Date</th>
                            <th>Show Time</th>
                            <th>Status</th>
                            <th>Booking Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($allBookings)): ?>
                            <?php foreach ($allBookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['customer_phone']); ?></td>
                                <td><?php echo htmlspecialchars($booking['movie_title']); ?></td>
                                <td><?php echo htmlspecialchars($booking['theater_name']); ?></td>
                                <td><?php echo htmlspecialchars($booking['seats']); ?></td>
                                <td><?php echo htmlspecialchars($booking['showdate']); ?></td>
                                <td><?php echo htmlspecialchars($booking['showtime']); ?></td>
                                <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">No bookings found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        const form = document.getElementById('booking-form');
        const seats = document.querySelectorAll('.seat');

        seats.forEach(seat => {
            const checkbox = seat.querySelector('input[type="checkbox"]');
            seat.addEventListener('click', () => {
                if (!checkbox.disabled) {
                    checkbox.checked = !checkbox.checked;
                    seat.classList.toggle('selected', checkbox.checked);
                }
            });
        });
        
        form.addEventListener('submit', (event) => {
            const selectedSeats = Array.from(document.querySelectorAll('.seat.selected'));
            if (selectedSeats.length === 0) {
                event.preventDefault();
                alert('Please select at least one seat.');
            }
        });
    </script>
</body>
</html>