<?php
class Showtime {
    public static function getConnection() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "movie_db";
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    public static function AddShowtime($movie_id, $theater_id, $showdate, $showtime) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("INSERT INTO shows (movie_id, theater_id, showdate, showtime) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $movie_id, $theater_id, $showdate, $showtime);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public static function GetShowtimesByMovie($movie_id) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("SELECT s.*, t.name as theater_name FROM shows s JOIN theaters t ON s.theater_id = t.id WHERE s.movie_id = ? ORDER BY s.showdate, s.showtime");
        $stmt->bind_param("i", $movie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $showtimes = array();
        while ($row = $result->fetch_assoc()) {
            $showtimes[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $showtimes;
    }

    public static function DeleteShowtime($showtime_id) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("DELETE FROM shows WHERE id = ?");
        $stmt->bind_param("i", $showtime_id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }
    
    public static function DeleteShowtimesByMovie($movieId) {
        $conn = self::getConnection();

        $deleteBookingsStmt = $conn->prepare("DELETE FROM bookings WHERE show_id IN (SELECT id FROM shows WHERE movie_id = ?)");
        $deleteBookingsStmt->bind_param("i", $movieId);
        $deleteBookingsStmt->execute();
        $deleteBookingsStmt->close();

        $deleteShowtimesStmt = $conn->prepare("DELETE FROM shows WHERE movie_id = ?");
        $deleteShowtimesStmt->bind_param("i", $movieId);
        $success = $deleteShowtimesStmt->execute();
        $deleteShowtimesStmt->close();
        $conn->close();
        return $success;
    }

    public static function GetShowtimesForPublic($movie_id) {
        $conn = self::getConnection();
        $current_date = date('Y-m-d');
        $stmt = $conn->prepare("SELECT s.*, t.name as theater_name FROM shows s JOIN theaters t ON s.theater_id = t.id WHERE s.movie_id = ? AND s.showdate >= ? ORDER BY s.showdate, s.showtime");
        $stmt->bind_param("is", $movie_id, $current_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $showtimes = array();
        while ($row = $result->fetch_assoc()) {
            $showtimes[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $showtimes;
    }
    
    public static function GetShowtimeById($showtime_id) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("SELECT s.*, t.name as theater_name FROM shows s JOIN theaters t ON s.theater_id = t.id WHERE s.id = ?");
        $stmt->bind_param("i", $showtime_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $showtime = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $showtime;
    }
    
    public static function GetAllTheaters() {
        $conn = self::getConnection();
        $result = $conn->query("SELECT * FROM theaters ORDER BY name");
        $theaters = array();
        while ($row = $result->fetch_assoc()) {
            $theaters[] = $row;
        }
        $conn->close();
        return $theaters;
    }
    
    public static function GetAvailableTheaters($showdate, $showtime) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("SELECT t.* FROM theaters t WHERE t.id NOT IN (SELECT s.theater_id FROM shows s WHERE s.showdate = ? AND s.showtime = ?)");
        $stmt->bind_param("ss", $showdate, $showtime);
        $stmt->execute();
        $result = $stmt->get_result();
        $theaters = array();
        while ($row = $result->fetch_assoc()) {
            $theaters[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $theaters;
    }
}
?>