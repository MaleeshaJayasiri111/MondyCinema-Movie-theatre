<?php
session_start();
include("config.php");
include("BO/Booking.php");
$show_id = 19;
if(isset($_GET['show_id'])) {
    $show_id = intval($_GET['show_id']);
}
$currentUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$bookedSeats = Booking::GetBookedSeats($show_id);
$myPendingSeats = [];
if ($currentUserId) {
    $conn = Booking::getConnection();
    $sql = "SELECT seats FROM bookings WHERE show_id = ? AND user_id = ? AND status = 'pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $show_id, $currentUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $myPendingSeats = array_merge($myPendingSeats, explode(',', $row['seats']));
        }
    }
    $stmt->close();
    $conn->close();
}
$pending_seats_from_payment = isset($_GET['pending_seats']) ? explode(',', $_GET['pending_seats']) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Booking</title>
    <link rel="stylesheet" href="assets/style/user-seat-booking.css">
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
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
    
    <div class="main-container">
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="user-actions">
                <a href="user-profile.php" class="profile-button">My Profile</a>
            </div>
        <?php endif; ?>
        <h1>Book Your Seat</h1>
        <div class="seat-map-container">
            <div class="screen">
                <p>Screen</p>
            </div>
            <form id="booking-form" action="process_booking.php" method="POST">
                <div class="seat-grid">
                    <?php
                    $totalSeats = 60;
                    for ($i = 1; $i <= $totalSeats; $i++) {
                        $seatClass = 'seat available';
                        $disabled = '';
                        $isMyPending = in_array($i, $myPendingSeats);
                        $isBooked = in_array($i, $bookedSeats);
                        if ($isMyPending) {
                            $seatClass = 'seat pending';
                            $disabled = 'disabled';
                        } else if ($isBooked) {
                            $seatClass = 'seat booked';
                            $disabled = 'disabled';
                        }
                        echo '<div class="' . $seatClass . '" data-seat-number="' . $i . '">';
                        echo '<input type="checkbox" name="seats[]" value="' . $i . '" ' . $disabled . '>';
                        echo '<span class="seat-label">' . $i . '</span>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="legend">
                    <div class="legend-item"><div class="legend-box available"></div>Available</div>
                    <div class="legend-item"><div class="legend-box booked"></div>Booked</div>
                    <div class="legend-item"><div class="legend-box selected"></div>Selected</div>
                    <div class="legend-item"><div class="legend-box pending"></div>Pending</div>
                </div>
                <div class="customer-details">
                    <label for="customer_name">Your Name:</label>
                    <input type="text" id="customer_name" name="customer_name" required>
                    <label for="customer_phone">Phone Number:</label>
                    <input type="text" id="customer_phone" name="customer_phone" required>
                    <input type="hidden" id="show_id" name="show_id" value="<?php echo $show_id; ?>">
                    <button type="submit">Book Seats</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const form = document.getElementById('booking-form');
        const seats = document.querySelectorAll('.seat');
        const customerNameInput = document.getElementById('customer_name');
        const customerPhoneInput = document.getElementById('customer_phone');
        const showIdInput = document.getElementById('show_id');
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
            event.preventDefault();
            const selectedSeats = Array.from(document.querySelectorAll('.seat.selected')).map(seat => seat.dataset.seatNumber);
            const customerName = customerNameInput.value;
            const customerPhone = customerPhoneInput.value;
            const showId = showIdInput.value;
            if (selectedSeats.length === 0) {
                alert('Please select at least one seat.');
            } else if (!customerName || !customerPhone) {
                alert('Please fill in all customer details.');
            } else {
                const formData = new FormData();
                formData.append('show_id', showId);
                formData.append('customer_name', customerName);
                formData.append('customer_phone', customerPhone);
                selectedSeats.forEach(seat => {
                    formData.append('seats[]', seat);
                });
                
                fetch('process_booking.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.href = `payments.php?seats=${selectedSeats.length}`;
                    } else {
                        alert('Booking failed: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        });
    </script>
</body>
</html>