<?php
class Movie {
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

    public static function AddMovie($title, $description, $poster, $trailer_link, $status, $ticket_price) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("INSERT INTO movies (title, description, poster, trailer_link, status, ticket_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $title, $description, $poster, $trailer_link, $status, $ticket_price);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public static function UpdateMovie($id, $title, $description, $poster, $trailer_link, $status, $ticket_price) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("UPDATE movies SET title = ?, description = ?, poster = ?, trailer_link = ?, status = ?, ticket_price = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $title, $description, $poster, $trailer_link, $status, $ticket_price, $id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }

    public static function DeleteMovie($id) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    }
    
    public static function GetMovieById($id) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $movie = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $movie;
    }
    
    public static function GetAllMovies() {
        $conn = self::getConnection();
        $result = $conn->query("SELECT * FROM movies ORDER BY created_at DESC");
        $movies = array();
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        $conn->close();
        return $movies;
    }
    
    public static function GetNowShowing() {
        $conn = self::getConnection();
        $result = $conn->query("SELECT * FROM movies WHERE status = 'now_showing' ORDER BY created_at DESC");
        $movies = array();
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        $conn->close();
        return $movies;
    }

    public static function GetComingSoon() {
        $conn = self::getConnection();
        $result = $conn->query("SELECT * FROM movies WHERE status = 'coming_soon' ORDER BY created_at DESC");
        $movies = array();
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
        $conn->close();
        return $movies;
    }
    
    public static function GetLastInsertedId() {
        $conn = self::getConnection();
        $last_id = $conn->insert_id;
        $conn->close();
        return $last_id;
    }
}
?>
