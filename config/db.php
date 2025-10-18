<?php
$conn = new mysqli("localhost", "root", "", "rentacar_db");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}
?>