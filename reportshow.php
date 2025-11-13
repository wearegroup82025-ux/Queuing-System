<?php
include 'connect.php';
session_start();

// Optional: kung gusto mong gamitin yung logged-in user
// $email = $_SESSION['email'] ?? '';

// Kunin ang pinakabagong reservation record
$sql = "SELECT * FROM register ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $sched = $row['sched'];
    $counter = $row['counter'];
    $slot = $row['slot'];
} else {
    $sched = "No schedule yet";
    $counter = "No counter yet";
    $slot = "No slot yet";
}
?>