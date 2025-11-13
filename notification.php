<?php
session_start();
require_once 'connect.php';

// 1️⃣ Check kung naka-login
$email = $_SESSION['email'] ?? '';
if (empty($email)) {
  echo "<script>alert('Please log in first!'); window.location='index.php';</script>";
  exit;
}

// 2️⃣ Kunin first name at last name
$stmt = $conn->prepare("SELECT fName, lName FROM register WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$fullname = isset($user['fName'], $user['lName'])
  ? htmlspecialchars($user['fName'] . ' ' . $user['lName'])
  : 'Student';

// 3️⃣ Kunin reservation info ng user
$query = $conn->prepare("
  SELECT sched, counter, slot, 
         TIMESTAMPDIFF(MINUTE, sched, NOW()) AS mins_diff, 
         TIMESTAMPDIFF(DAY, NOW(), sched) AS days_left
  FROM register 
  WHERE email = ? AND slot IS NOT NULL
  ORDER BY sched DESC LIMIT 1
");
$query->bind_param("s", $email);
$query->execute();
$result = $query->get_result();

$notifications = [];

if ($row = $result->fetch_assoc()) {
  $days = (int)$row['days_left'];
  $mins_diff = (int)$row['mins_diff'];
  $slot = (int)$row['slot'];
  $counter = $row['counter'];
  $msg = "";
  $timeLabel = "";

  // 🔹 Custom message depende sa schedule
  if ($days > 1) {
    $msg = "⏳ " . $days . " days left before your reservation schedule.";
  } elseif ($days === 1) {
    $msg = "📅 1 day left before your reservation.";
  } elseif ($days === 0 && $mins_diff <= 0) {
    $msg = "🔔 Today is your reservation schedule! Please go to your allotted counter.";
  } elseif ($days < 0) {
    $msg = "✅ Your reservation has been successfully completed.";
  }

  // 🔹 Time label
  if ($mins_diff < 60 && $mins_diff >= 0) {
    $timeLabel = $mins_diff . " mins ago";
  } elseif ($mins_diff < 1440 && $mins_diff >= 0) {
    $hours = floor($mins_diff / 60);
    $timeLabel = $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
  } elseif ($mins_diff >= 1440) {
    $daysAgo = floor($mins_diff / 1440);
    $timeLabel = $daysAgo . " day" . ($daysAgo > 1 ? "s" : "") . " ago";
  } else {
    $timeLabel = "Just now";
  }

  $notifications[] = [
    'message' => $msg,
    'time' => $timeLabel
  ];

  // 🔹 Extra logic: ilang tao pa ang ahead
  $countQuery = $conn->prepare("
    SELECT COUNT(*) AS ahead 
    FROM register 
    WHERE counter = ? AND slot < ? AND slot IS NOT NULL
  ");
  $countQuery->bind_param("si", $counter, $slot);
  $countQuery->execute();
  $countResult = $countQuery->get_result();
  $aheadData = $countResult->fetch_assoc();
  $ahead = (int)$aheadData['ahead'];

  if ($ahead === 0) {
    $notifications[] = [
      'message' => "🚨 It's your turn now! Please proceed to Counter <b>" . htmlspecialchars($counter) . "</b>.",
      'time' => "Just now"
    ];
  } else {
    $notifications[] = [
      'message' => "👥 There are <b>{$ahead}</b> people ahead of you before your turn at Counter <b>" . htmlspecialchars($counter) . "</b>.",
      'time' => "Updated recently"
    ];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notifications - BulSU Queuing System</title>
  <link rel="stylesheet" href="notification.css" />
  <style>
    .profile-dropdown {
      position: absolute;
      top: 100px;
      left: 20px;
      background-color: #eaeaea;
      border-radius: 10px;
      padding: 15px;
      width: 270px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      display: none;
      text-align: center;
      z-index: 100;
    }
    .profile-dropdown.active { display: block; }
    .profile-icon { font-size: 45px; color: #555; margin-bottom: 5px; }
    .profile-dropdown h3 { margin: 5px 0; font-size: 18px; color: #000; }
    .profile-dropdown p { margin: 0; font-size: 14px; color: #666; }

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
    }
    .logout-btn:hover { background-color: #dc3545; color: white; border-color: #dc3545; }

    .notifications-box {
      max-width: 600px;
      margin: 120px auto;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      padding: 20px;
    }

    .notification-item {
      background: #f8f8f8;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 10px;
      border-left: 4px solid #800000;
    }
    .notification-item .message { font-size: 15px; color: #333; }
    .notification-item .time { display: block; font-size: 12px; color: #777; margin-top: 5px; text-align: right; }
    h2 { text-align: center; color: #800000; margin-bottom: 20px; }
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

      <div class="hamburger" id="hamburger">☰</div>

      <div class="profile-dropdown" id="profileDropdown">
        <div class="profile-icon">👤</div>
        <h3><?php echo $fullname; ?></h3>
        <p>Student</p>
        <button class="logout-btn" onclick="window.location='logout.php'">Log out</button>
      </div>

      <nav>
        <ul class="nav-links" id="nav-links">
          <li><a href="home.php">Home</a></li>
          <li><a href="reservation.php">Reserve</a></li>
          <li><a href="report.php">Reports</a></li>
          <li><a href="notification.php" class="active">Notifications</a></li>
          <li><a href="feedbacks.php">Feedbacks</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main>
    <div class="notifications-box">
      <h2>Notifications</h2>
      <?php if (empty($notifications)): ?>
        <p style="text-align:center; color:#777;">No notifications yet.</p>
      <?php else: ?>
        <?php foreach ($notifications as $notif): ?>
          <div class="notification-item">
            <p class="message"><?php echo $notif['message']; ?></p>
            <span class="time"><?php echo htmlspecialchars($notif['time']); ?></span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

  <script>
    const logoBtn = document.getElementById('logoButton');
    const dropdown = document.getElementById('profileDropdown');
    logoBtn.addEventListener('click', () => dropdown.classList.toggle('active'));
    document.addEventListener('click', (e) => {
      if (!logoBtn.contains(e.target) && !dropdown.contains(e.target))
        dropdown.classList.remove('active');
    });
  </script>

  <script src="reservation.js"></script>
</body>
</html>
