<?php
session_start();
include_once('config.php');
include_once('BO/Movie.php');
include_once('BO/Showtime.php');
$message = '';
$now_showing_movies = Movie::GetNowShowing();
$seat_count = isset($_GET['seats']) ? intval($_GET['seats']) : 0;
$selectedSeats = isset($_GET['selected_seats']) ? $_GET['selected_seats'] : '';
$showtime_id = isset($_GET['showtime_id']) ? $_GET['showtime_id'] : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="assets/style/payment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="header">
        <a href="bookseats.php" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Seat Booking
        </a>
        <h1 class="header-title">Payment Page</h1>
    </header>
    <main class="container">
        <div class="payment-card">
            <h2 class="card-title">Movie & Ticket Details</h2>
            <div class="form-group">
                <label for="movie-select">Select Movie:</label>
                <select id="movie-select" required>
                    <option value="">Select a Movie</option>
                    <?php
                        foreach ($now_showing_movies as $movie) {
                            $price = str_replace('LKR ', '', $movie['ticket_price']);
                            echo '<option data-price="' . htmlspecialchars($price) . '" data-movie-id="' . htmlspecialchars($movie['id']) . '" data-movie-title="' . htmlspecialchars($movie['title']) . '">' . htmlspecialchars($movie['title']) . '</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="seat-count">Number of Seats:</label>
                <input type="text" id="seat-count" value="<?php echo htmlspecialchars($seat_count); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="ticket-price">Ticket Price:</label>
                <input type="text" id="ticket-price" value="0.00" readonly>
            </div>
            <div class="total-price-display">
                <p>Total Price: <span id="total-price">Rs. 0.00</span></p>
            </div>
        </div>
        <div class="payment-card">
            <h2 class="card-title">Payment Details</h2>
            <div class="card-icons">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-amex"></i>
                <i class="fas fa-credit-card"></i>
            </div>
            <form id="payment-form">
                <div class="form-group">
                    <label for="card-name">Card Holder Name:</label>
                    <input type="text" id="card-name" placeholder="Name" required>
                </div>
                <div class="form-group">
                    <label for="card-number">Card Number:</label>
                    <input type="text" id="card-number" placeholder="0000 0000 0000 0000" maxlength="19" required>
                </div>
                <div class="form-group-inline">
                    <div class="form-group">
                        <label for="expiry-date">Expiry Date:</label>
                        <input type="text" id="expiry-date" placeholder="MM/YY" maxlength="5" required>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV:</label>
                        <input type="text" id="cvv" placeholder="123" maxlength="3" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="amount">Amount:</label>
                    <input type="text" id="amount" readonly required>
                </div>
                <div id="validation-message" style="color: red; text-align: center; margin-bottom: 10px; display: none;"></div>
                <button type="submit" class="proceed-btn">Proceed with Payment</button>
            </form>
        </div>
    </main>
    <div id="payment-success-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <p>Your payment was successful!</p>
            <i class="fas fa-check-circle success-icon"></i>
            <button id="ok-button" class="btn">OK</button>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const movieSelect = document.getElementById('movie-select');
            const seatCount = document.getElementById('seat-count').value;
            const ticketPriceInput = document.getElementById('ticket-price');
            const totalPriceSpan = document.getElementById('total-price');
            const amountInput = document.getElementById('amount');
            const paymentForm = document.getElementById('payment-form');
            const successModal = document.getElementById('payment-success-modal');
            const okButton = document.getElementById('ok-button');
            const selectedSeats = '<?php echo $selectedSeats; ?>';
            const showtimeId = '<?php echo $showtime_id; ?>';
            const userId = '<?php echo $user_id; ?>';
            
            function calculateTotalPrice() {
                const selectedOption = movieSelect.options[movieSelect.selectedIndex];
                const ticketPrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
                const total = ticketPrice * parseInt(seatCount);
                ticketPriceInput.value = total.toFixed(2);
                totalPriceSpan.textContent = `Rs. ${total.toFixed(2)}`;
                amountInput.value = total.toFixed(2);
            }
            movieSelect.addEventListener('change', calculateTotalPrice);
            calculateTotalPrice();
            paymentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                successModal.style.display = 'flex';
                const cardHolderName = document.getElementById('card-name').value;
                const selectedOption = movieSelect.options[movieSelect.selectedIndex];
                const movieId = selectedOption.getAttribute('data-movie-id');
                const formData = new FormData();
                formData.append('customer_name', cardHolderName);
                formData.append('user_id', userId);
                formData.append('show_id', showtimeId);
                formData.append('seats', selectedSeats);
                formData.append('status', 'pending');
                fetch('admin/admin-booking-action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                    } else {
                        console.error('Failed to save booking:', data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
            okButton.addEventListener('click', () => {
                const redirectUrl = `bookseats.php?pending_seats=${selectedSeats}`;
                window.location.href = redirectUrl;
            });
            window.onclick = function(event) {
                if (event.target == successModal) {
                    successModal.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>