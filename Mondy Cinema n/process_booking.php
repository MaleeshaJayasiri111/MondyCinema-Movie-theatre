<?php
session_start();
include("config.php");
include("BO/Booking.php");
include("BO/Show.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $showId = isset($_POST['show_id']) ? intval($_POST['show_id']) : null;
    $customerName = $_POST['customer_name'];
    $customerPhone = $_POST['customer_phone'];
    $selectedSeats = isset($_POST['seats']) ? $_POST['seats'] : [];

    if (empty($selectedSeats)) {
        echo json_encode(['status' => 'error', 'message' => 'No seats selected.']);
        exit();
    }

    if (!$showId) {
        echo json_encode(['status' => 'error', 'message' => 'Show ID is missing. Please select a valid show.']);
        exit;
    }

    $booking = new Booking();
    $status = 'pending';
    $bookingId = $booking->CreateBooking($userId, $showId, $customerName, $customerPhone, $selectedSeats, $status);
    $booking->closeConnection();

    if ($bookingId) {
        echo json_encode(['status' => 'success', 'bookingId' => $bookingId]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Booking creation failed: ' . $booking->getError()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
