<?php
class Theater {
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

    public static function GetAllTheaters() {
        $conn = self::getConnection();
        $sql = "SELECT * FROM theaters";
        $result = $conn->query($sql);
        $theaters = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $theaters[] = $row;
            }
        }
        $conn->close();
        return $theaters;
    }

    public static function GetTheaterById($id) {
        $conn = self::getConnection();
        $stmt = $conn->prepare("SELECT * FROM theaters WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $theater = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $theater;
    }
}
?>