<?php

class Booking {
    private $conn;
    private $error;

    public static function getConnection() {
        $configPath = dirname(__DIR__) . "/config.php";
        if (file_exists($configPath)) {
            include($configPath);
        } else {
            $configPath = __DIR__ . "/../config.php";
            if (file_exists($configPath)) {
                include($configPath);
            } else {
                return null;
            }
        }
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            return null;
        }
        return $conn;
    }

    public function __construct() {
        $this->conn = self::getConnection();
        if (!$this->conn) {
            $this->error = "Database connection failed.";
        }
    }

    public function CreateBooking($userId, $showId, $name, $phone, $seats, $status) {
        if (!$this->conn) {
            return false;
        }
        
        $seatsString = implode(',', $seats);
        
        $sql = "INSERT INTO bookings (user_id, show_id, customer_name, customer_phone, seats, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            $this->error = "SQL prepare failed: " . $this->conn->error;
            return false;
        }

        $userIdParam = $userId !== null ? $userId : 0;
        $stmt->bind_param("iissss", $userIdParam, $showId, $name, $phone, $seatsString, $status);

        if ($stmt->execute()) {
            $bookingId = $stmt->insert_id;
            $stmt->close();
            return $bookingId;
        } else {
            $this->error = "Booking creation failed: " . $stmt->error;
            $stmt->close();
            return false;
        }
    }

    public static function GetBookedSeats($showId) {
        $bookedSeats = [];
        $conn = self::getConnection();
        if (!$conn) {
            return $bookedSeats;
        }
        $sql = "SELECT seats FROM bookings WHERE show_id = ? AND status IN ('confirmed', 'pending')";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $showId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $seats = explode(',', $row['seats']);
                    $bookedSeats = array_merge($bookedSeats, $seats);
                }
            }
            $stmt->close();
        }
        $conn->close();
        return $bookedSeats;
    }

    public function getError() {
        return $this->error;
    }
    
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    
    public static function GetAllBookings($status = null) {
        $bookings = [];
        $conn = self::getConnection();
        if (!$conn) {
            return $bookings;
        }

        $sql = "SELECT b.id, b.user_id, b.customer_name, b.customer_phone, b.seats, b.status, m.title AS movie_title, t.name AS theater_name, s.showdate, s.showtime FROM bookings b LEFT JOIN shows s ON b.show_id = s.id LEFT JOIN movies m ON s.movie_id = m.id LEFT JOIN theaters t ON s.theater_id = t.id";

        if ($status) {
            $sql .= " WHERE b.status = ?";
        }

        $sql .= " ORDER BY b.booking_date DESC";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $conn->close();
            return $bookings;
        }

        if ($status) {
            $stmt->bind_param("s", $status);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
        }

        $stmt->close();
        $conn->close();
        return $bookings;
    }

    public static function UpdateBookingStatus($bookingId, $status) {
        $conn = self::getConnection();
        if (!$conn) {
            return ['success' => false, 'message' => 'Database connection failed.'];
        }

        $sql = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $conn->close();
            return ['success' => false, 'message' => 'SQL prepare failed: ' . $conn->error];
        }

        $stmt->bind_param("si", $status, $bookingId);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            return ['success' => true, 'message' => 'Booking status updated successfully.'];
        } else {
            $errorMessage = $stmt->error;
            $stmt->close();
            $conn->close();
            return ['success' => false, 'message' => 'Failed to update booking status: ' . $errorMessage];
        }
    }
}
?>