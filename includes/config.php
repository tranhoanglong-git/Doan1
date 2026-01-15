<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "music_web";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Ket noi that bai: " . $conn->connect_error);
}
?>
