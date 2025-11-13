<?php
session_start();

// If a counter button is clicked
if (isset($_POST['counter'])) {
  $_SESSION['counter'] = $_POST['counter']; // Save counter number (1 or 2)
  header("Location: registrar.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Counter | BulSU Queueing System</title>
  <link rel="stylesheet" href="selectCounter.css">
</head>
<body>
  <div class="counter-container">
    <img src="img/bsu logo.png" alt="BulSU Logo" class="logo">
    <h2>Select Counter</h2>
    <form method="POST">
      <button type="submit" name="counter" value="1" class="counter-btn">Counter 1</button>
      <button type="submit" name="counter" value="2" class="counter-btn">Counter 2</button>
    </form>
  </div>
</body>
</html>
