<?php
session_start();
include("BO/Booking.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $showId = $_POST['show_id'];
    $customerName = $_POST['customer_name'];
    $customerPhone = $_POST['customer_phone'];
    $selectedSeats = isset($_POST['seats']) ? $_POST['seats'] : [];

    if (empty($selectedSeats)) {
        echo json_encode(['status' => 'error', 'message' => 'No seats selected.']);
        exit();
    }

    $booking = new Booking();
    $bookingId = $booking->CreateBooking($userId, $showId, $customerName, $customerPhone, $selectedSeats, 'pending');
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