<?php
session_start();

// Kung naka-login na, diretso sa home
if (isset($_SESSION['email'])) {
  header("Location: home.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BulSU Queueing System</title>
  <link rel="stylesheet" href="loginAs.css">
</head>
<body>

  <div class="login-container">
    <img src="img/bsu logo.png" alt="BulSU Logo" style="width:120px; height:auto;">
    <h2>Login as</h2>

    <!-- Student Button -->
    <button class="btn" id="studentBtn">Student</button>

    <!-- Registrar Button -->
    <button class="btn" id="registrarBtn">Registrar</button>
  </div>

  <script>
    // 👉 Pag-click ng Student button → punta sa studentLogin.php
    document.getElementById("studentBtn").addEventListener("click", function() {
      window.location.href = "studentLogin.php";
    });

    // 👉 Pag-click ng Registrar button → punta sa registrar.php
    document.getElementById("registrarBtn").addEventListener("click", function() {
      window.location.href = "registrar.php";
    });
  </script>

</body>
</html>
