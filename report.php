<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Please log in first.'); window.location='index.php';</script>";
    exit;
}

$email = trim($_SESSION['email']);

// 🔹 Kunin ang user record (para sa reservation at pangalan)
$stmt = $conn->prepare("SELECT * FROM register WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

// 🔹 Kunin full name (firstname + lastname)
if ($user && !empty($user['fName']) && !empty($user['lName'])) {
    $fullName = htmlspecialchars($user['fName'] . ' ' . $user['lName']);
} else {
    $fullName = "Student";
}

// 🔹 Handle reservation submit
if (isset($_POST['register'])) {
    $date = trim($_POST['sched']);
    $counterNumber = trim($_POST['counter']);
    $slotNumber = trim($_POST['slot']);

    if (empty($date) || empty($counterNumber) || empty($slotNumber)) {
        echo "<script>alert('Please complete all fields.');</script>";
        exit;
    }

    // Check kung may existing reservation
    if (!empty($user['slot'])) {
        echo "<script>alert('You already have an existing reservation!'); window.location='report.php';</script>";
        exit;
    }

    // Check kung puno na ang slot (max 50)
    $checkSlot = $conn->prepare("SELECT COUNT(*) AS total FROM register WHERE slot = ?");
    $checkSlot->bind_param("s", $slotNumber);
    $checkSlot->execute();
    $slotResult = $checkSlot->get_result()->fetch_assoc();

    if ($slotResult['total'] >= 50) {
        echo "<script>alert('Sorry, this slot is already full! Please choose another one.'); window.location='reservation.php';</script>";
        exit;
    }

    // UPDATE lang existing record
    $update = $conn->prepare("UPDATE register SET sched = ?, counter = ?, slot = ? WHERE email = ?");
    $update->bind_param("ssss", $date, $counterNumber, $slotNumber, $email);

    if ($update->execute()) {
        echo "<script>alert('Reservation successful!'); window.location='report.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating reservation: " . $conn->error . "');</script>";
    }
}

// Display current reservation
$date = $user['sched'] ?? 'No schedule yet';
$counterNumber = $user['counter'] ?? 'No counter yet';
$slotNumber = $user['slot'] ?? 'No slot yet';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Schedule - BulSU Queuing System</title>
  <link rel="stylesheet" href="report.css" />
  <style>
    /* Dropdown Profile */
    .profile-dropdown {
      position: absolute;
      top: 100px;
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

    .profile-dropdown.active { display: block; }

    .profile-icon {
      font-size: 45px;
      color: #555;
      margin-bottom: 5px;
    }

    .profile-dropdown button {
      background: #7b1113;
      border: none;
      cursor: pointer;
      position: relative;
      color: white;
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
  </style>
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="logo1">
        <button id="logoButton">
          <img src="img/bsu logo.png" alt="BulSU Logo" style="width: 120px; height: auto;">
        </button>
      </div>

      <!-- Hamburger Button -->
      <div class="hamburger" id="hamburger">☰</div>

      <!-- ✅ Updated Profile Dropdown -->
      <div class="profile-dropdown" id="profileDropdown">
        <div class="profile-icon">👤</div>
        <h3><?php echo $fullName; ?></h3>
        <p>Student</p>
        <button class="logout-btn" onclick="window.location='logout.php'">Log out</button>
      </div>

      <!-- Navigation -->
      <nav>
        <ul class="nav-links" id="nav-links">
          <li><a href="home.php">Home</a></li>
          <li><a href="reservation.php">Reserve</a></li>
          <li><a href="report.php" class="active">Reports</a></li>
          <li><a href="notification.php">Notifications</a></li>
          <li><a href="feedbacks.php">Feedbacks</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- 🔹 Schedule Section -->
  <main>
    <section class="schedule-box">
      <h2>Your Schedule</h2>
      <div class="schedule-grid">
        <div class="card">
          <h3>Counter</h3>
          <p><?php echo htmlspecialchars($counterNumber); ?></p>
        </div>
        <div class="card">
          <h3>Date</h3>
          <p><?php echo htmlspecialchars($date); ?></p>
        </div>
        <div class="card">
          <h3>Slot</h3>
          <p><?php echo htmlspecialchars($slotNumber); ?></p>
        </div>
      </div>
    </section>
  </main>

  <script>
    // Toggle dropdown visibility
    const logoBtn = document.getElementById('logoButton');
    const dropdown = document.getElementById('profileDropdown');
    logoBtn.addEventListener('click', () => dropdown.classList.toggle('active'));
    document.addEventListener('click', (e) => {
      if (!logoBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('active');
      }
    });
  </script>

  <script src="reservation.js"></script>
</body>
</html>
