<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "mirrora"; // Yahan apne database ka naam likhein

$conn = mysqli_connect($host, $username, $password, $database);

// Connection check karne ke liye
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>