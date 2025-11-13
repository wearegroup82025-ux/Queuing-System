<?php
session_start();
require_once 'connect.php';

// ✅ Redirect if not logged in
if (!isset($_SESSION['email'])) {
  echo "<script>alert('Please log in first!'); window.location='studentLogin.php';</script>";
  exit;
}

$user_email = $_SESSION['email'];

// ✅ Get user's name
$stmt = $conn->prepare("SELECT fName, lName FROM register WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $fullName = htmlspecialchars($row['fName'] . ' ' . $row['lName']);
} else {
  $fullName = "Student";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - BulSU</title>
  <link rel="stylesheet" href="login.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      color: #333;
    }

    /* Navbar */
    .navbar {
      background-color: #7b1113;
      color: white;
      padding: 15px 25px;
      display: flex;
      align-items: center;
      position: relative;
    }

    .navbar h1 {
      font-size: 20px;
      margin: 0;
    }

    .navbar button {
      background: #7b1113;
      border: none;
      cursor: pointer;
      position: relative;
      color: white;
    }

    .navbar img {
      width: 120px;
      height: auto;
      transition: transform 0.2s ease;
    }

    .navbar button:hover img {
      transform: scale(1.05);
    }

    .nav-left {
      margin-left: 15px;
    }

    /* Dropdown Profile */
    .profile-dropdown {
      position: absolute;
      top: 100%;
      left: 20px;
      background-color: #eaeaea;
      border-radius: 10px;
      padding: 15px;
      width: 270px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      display: none;
      text-align: center;
      z-index: 100;
    }

    .profile-dropdown.active {
      display: block;
    }

    .profile-icon {
      font-size: 45px;
      color: #555;
      margin-bottom: 5px;
    }

    .profile-dropdown h3 {
      margin: 5px 0;
      font-size: 18px;
      color: #000;
    }

    .profile-dropdown p {
      margin: 0;
      font-size: 14px;
      color: #666;
    }

    .logout-btn {
      margin-top: 12px;
      border: 1px solid #000;
      border-radius: 8px;
      padding: 8px 15px;
      background: white;
      cursor: pointer;
      transition: 0.3s;
      width: 100%;
    }

    .logout-btn:hover {
      background-color: #dc3545;
      color: white;
      border-color: #dc3545;
    }

    /* Main Section */
    .container {
      text-align: center;
      padding: 50px 20px;
    }

    .container h2 {
      font-size: 28px;
      margin-bottom: 20px;
      color: #333;
    }

    .reserve-btn {
      background-color: #7b1113;
      color: white;
      border: none;
      padding: 15px 40px;
      border-radius: 8px;
      font-size: 18px;
      cursor: pointer;
      transition: 0.3s;
      margin-bottom: 40px;
    }

    .reserve-btn:hover {
      background-color: #a31f1f;
    }

    .card-container {
      margin-top: 40px;
      display: flex;
      justify-content: center;
      gap: 50px;
      flex-wrap: wrap;
      margin-bottom: 25px;
    }

    .card {
      background-color: white;
      border-radius: 12px;
      padding: 30px;
      width: 250px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: 0.3s;
      font-weight: bold;
      color: #333;
    }

    .card:hover {
      transform: translateY(-5px);
      background-color: #ffcc00;
    }

    a {
      text-decoration: none;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <header class="navbar">
    <button id="logoButton">
      <img src="img/bsu logo.png" alt="BulSU Logo">
    </button>
    <div class="nav-left">
      <h1>BulSU Queueing System</h1>
    </div>

    <!-- ✅ Fixed dropdown -->
    <div class="profile-dropdown" id="profileDropdown">
      <div class="profile-icon">👤</div>
      <h3><?php echo $fullName; ?></h3>
      <p>Student</p>
      <button class="logout-btn" id="logoutBtn">Log out</button>
    </div>
  </header>

  <!-- Main Section -->
  <main class="container">
    <h2>Reserve here:</h2>
    <a href="reservation.php"><button class="reserve-btn">Click here</button></a>

    <div class="card-container">
      <a href="report.php"><div class="card">Reports</div></a>
      <a href="notification.php"><div class="card">Notifications</div></a>
      <a href="feedbacks.php"><div class="card">Feedbacks</div></a>
    </div>
  </main>

  <script>
    // Toggle dropdown visibility
    const logoBtn = document.getElementById('logoButton');
    const dropdown = document.getElementById('profileDropdown');
    const logoutBtn = document.getElementById('logoutBtn');

    logoBtn.addEventListener('click', () => {
      dropdown.classList.toggle('active');
    });

    // Close dropdown if clicked outside
    document.addEventListener('click', (e) => {
      if (!logoBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('active');
      }
    });

    // ✅ Working logout
    logoutBtn.addEventListener('click', () => {
      window.location.href = "logout.php"; 
    });
  
  </script>

</body>
</html>
