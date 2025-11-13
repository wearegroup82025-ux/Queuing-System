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

// 🔹 Kapag nagsubmit ng reservation
if (isset($_POST['submit'])) {
    $sched = $_POST['sched'];
    $counter = $_POST['counter'];
    $slot = $_POST['slot'];

    // Check kung may existing record
    $check = $conn->prepare("SELECT * FROM register WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // 🔸 May existing account → update reservation info
        $update = $conn->prepare("UPDATE register SET sched=?, counter=?, slot=?, status='Pending' WHERE email=?");
        $update->bind_param("ssis", $sched, $counter, $slot, $email);
        if ($update->execute()) {
            echo "<script>alert('Reservation updated successfully!'); window.location='reservation.php';</script>";
        } else {
            echo "<script>alert('Error updating reservation.');</script>";
        }
        $update->close();
    } else {
        // 🔸 Wala pa → insert new reservation record
        $insert = $conn->prepare("INSERT INTO register (email, sched, counter, slot, status) VALUES (?, ?, ?, ?, 'Pending')");
        $insert->bind_param("sssi", $email, $sched, $counter, $slot);
        if ($insert->execute()) {
            echo "<script>alert('Reservation added successfully!'); window.location='reservation.php';</script>";
        } else {
            echo "<script>alert('Error adding reservation.');</script>";
        }
        $insert->close();
    }

    $check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservation - BulSU Queuing System</title>
  <link rel="stylesheet" href="reservation.css">
  <style>
    /* 🔹 Profile Dropdown Popup */
    .profile-dropdown {
      position: absolute;
      top: 110px;
      left: 40px;
      background-color: #f1f1f1;
      border-radius: 12px;
      padding: 18px;
      width: 250px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      display: none;
      text-align: center;
      z-index: 100;
    }
    .profile-dropdown.active { display: block; }

    .profile-icon {
      font-size: 45px;
      color: #5e35b1;
      margin-bottom: 5px;
    }

    .profile-dropdown h3 {
      margin: 6px 0;
      font-size: 18px;
      color: #222;
    }
    .profile-dropdown p {
      font-size: 14px;
      color: #666;
      margin: 0;
    }

    .profile-dropdown button {
      background: #7b1113;
      border: none;
      cursor: pointer;
      position: relative;
      color: white;
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
      font-size: 15px;
    }
    .logout-btn:hover {
      background-color: #dc3545;
      color: white;
      border-color: #dc3545;
    }
  </style>
</head>

<body>
  <!-- 🔹 Header Section -->
  <header class="header">
    <div class="container">
      <!-- Logo Button -->
      <div class="logo1">
        <button id="logoButton" style="background:none; border:none; cursor:pointer;">
          <img src="img/bsu logo.png" alt="BulSU Logo" style="width: 120px; height: auto;">
        </button>
      </div>

      <!-- Hamburger Menu -->
      <div class="hamburger" id="hamburger">☰</div>

      <!-- 🔹 Profile Popup -->
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
          <li><a href="reservation.php" class="active">Reserve</a></li>
          <li><a href="report.php">Reports</a></li>
          <li><a href="notification.php">Notifications</a></li>
          <li><a href="feedbacks.php">Feedbacks</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- 🔹 Reservation Form -->
  <main>
    <div class="reservation-box">
      <h2>Reservation</h2>
      <form action="" method="POST">
        <input type="date" name="sched" required>

        <input list="Counternumber" name="counter" type="text" placeholder="Counter" required>
        <datalist id="Counternumber">
          <option value="1">
          <option value="2">
          <option value="3">
          <option value="4">
        </datalist>

        <input type="number" name="slot" placeholder="Slots" required>

        <div class="buttons">
          <button type="reset" class="cancel" onclick="window.location.href='home.php'">Cancel</button>
          <button type="submit" class="ok" name="submit">OK</button>
        </div>
      </form>
    </div>
  </main>

  <script>
    // 🔹 Toggle dropdown visibility
    const logoBtn = document.getElementById('logoButton');
    const dropdown = document.getElementById('profileDropdown');

    logoBtn.addEventListener('click', () => {
      dropdown.classList.toggle('active');
    });

    // 🔹 Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
      if (!logoBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('active');
      }
    });
  </script>

    <script src="reservation.js"></script>

</body>
</html>
