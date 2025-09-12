<?php
class Show {
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

    public static function GetShowsByMovie($movie_id) {
        $conn = self::getConnection();
        $sql = "SELECT s.*, t.name as theater_name FROM shows s JOIN theaters t ON s.theater_id = t.id WHERE s.movie_id = $movie_id ORDER BY s.showdate, s.showtime";
        $result = $conn->query($sql);
        $shows = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $shows[] = $row;
            }
        }
        $conn->close();
        return $shows;
    }

    public static function AddShow($movie_id, $theater_id, $showdate, $showtime) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("INSERT INTO shows (movie_id, theater_id, showdate, showtime) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $movie_id, $theater_id, $showdate, $showtime);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public static function DeleteShow($id) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("DELETE FROM shows WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }
}
?>