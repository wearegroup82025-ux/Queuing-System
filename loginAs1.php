<?php
session_start();

// Kung naka-login na, diretso sa home
if (isset($_SESSION['email'])) {
  header("Location: admin.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BulSU Queueing System</title>
  <link rel="stylesheet" href="loginAs1.css">
</head>
<body>

  <div class="login-container">
    <img src="img/bsu logo.png" alt="BulSU Logo" style="width:120px; height:auto;">
    <h2>Login as</h2>

    <div class="btn-row">
      <button class="btn" id="studentBtn">Student</button>
      <button class="btn" id="adminBtn">Admin</button>
    </div>

    <button class="btn" id="registrarBtn">Registrar</button>
  </div>

  <script>
    // 👉 Pag-click ng Student button → punta sa studentLogin.php
    document.getElementById("studentBtn").addEventListener("click", function() {
      window.location.href = "studentLogin.php";
    });

    // 👉 Pag-click ng Admin button → (placeholder)
    document.getElementById("adminBtn").addEventListener("click", function() {
      window.location.href = "studentLogin2.php";
    });

    // 👉 Pag-click ng Registrar button → (placeholder)
    document.getElementById("registrarBtn").addEventListener("click", function() {
      window.location.href = "studentLogin1.php";
    });
  </script>

</body>
</html>
