<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "logindb";
$port = "3399"; // change this if your database name is different

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Failed to connect to database: " . $conn->connect_error);
}
?>