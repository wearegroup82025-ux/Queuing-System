<?php
session_start();
require_once 'connect.php';

// Kunin ang email ng user galing session
$user_email = $_SESSION['email'] ?? null;

// Kunin ang first at last name ng user mula sa database
$user_fullname = 'Student';
if ($user_email) {
  $stmt = $conn->prepare("SELECT fName, lName FROM register WHERE email = ?");
  $stmt->bind_param("s", $user_email);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($row = $result->fetch_assoc()) {
    $user_fullname = htmlspecialchars($row['fName'] . ' ' . $row['lName']);
  }
}

// Handle feedback submission (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $star = intval($_POST['rating'] ?? 0);
  $feedback = trim($_POST['comment'] ?? '');

  if ($star > 0 && $user_email) {

    // ✅ Check kung may existing record na ang user
    $check = $conn->prepare("SELECT id FROM register WHERE email = ?");
    $check->bind_param("s", $user_email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
      // ✅ May existing record → UPDATE
      $stmt = $conn->prepare("UPDATE register SET star = ?, feedback = ? WHERE email = ?");
      if ($stmt === false) {
        echo "SQL Error: " . htmlspecialchars($conn->error);
        exit;
      }
      $stmt->bind_param("iss", $star, $feedback, $user_email);
    } else {
      // ✅ Wala pang record → INSERT (optional)
      $stmt = $conn->prepare("INSERT INTO register (email, star, feedback) VALUES (?, ?, ?)");
      if ($stmt === false) {
        echo "SQL Error: " . htmlspecialchars($conn->error);
        exit;
      }
      $stmt->bind_param("sis", $user_email, $star, $feedback);
    }

    if ($stmt->execute()) {
      echo "success";
      exit;
    } else {
      echo "DB Error: " . htmlspecialchars($stmt->error);
      exit;
    }

  } else {
    echo "error";
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedbacks - BulSU Queuing System</title>
  <link rel="stylesheet" href="feedbacks.css">
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

    /* ⭐ Feedback Section */
    .feedback-box {
      max-width: 500px;
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      padding: 10px 55px 10px 40px;
      text-align: center;
    }
    .stars {
      font-size: 40px;
      color: #ccc;
      cursor: pointer;
    }
    .star.active { color: gold; }
    textarea {
      width: 100%;
      height: 120px;
      margin-top: 15px;
      padding: 10px;
      border-radius: 10px;
      border: 1px solid #ccc;
      resize: none;
    }
    .submit-btn {
      margin-top: 15px;
      background: #800000;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }
    .submit-btn:hover { background: #a00000; }
  </style>
</head>
<body>
<header class="header">
  <div class="container">
    <div class="logo1">
      <button id="logoButton"><img src="img/bsu logo.png" alt="BulSU Logo" style="width: 120px; height: auto;"></button>
    </div>

    <div class="hamburger" id="hamburger">☰</div>

    <!-- 🔹 FIXED: show full name from database -->
    <div class="profile-dropdown" id="profileDropdown">
      <div class="profile-icon">👤</div>
      <h3><?php echo $user_fullname; ?></h3>
      <p>Student</p>
      <button class="logout-btn" onclick="window.location='logout.php'">Log out</button>
    </div>

    <nav>
      <ul class="nav-links" id="nav-links">
        <li><a href="home.php">Home</a></li>
        <li><a href="reservation.php">Reserve</a></li>
        <li><a href="report.php">Reports</a></li>
        <li><a href="notification.php">Notifications</a></li>
        <li><a href="feedbacks.php" class="active">Feedbacks</a></li>
      </ul>
    </nav>
  </div>
</header>

<main>
  <div class="feedback-box">
    <h2>Feedbacks</h2>
    <p class="instructions">Rate the Application 1 to 5 stars</p>

    <div class="stars">
      <span class="star" data-value="1">&#9733;</span>
      <span class="star" data-value="2">&#9733;</span>
      <span class="star" data-value="3">&#9733;</span>
      <span class="star" data-value="4">&#9733;</span>
      <span class="star" data-value="5">&#9733;</span>
    </div>

    <textarea id="comment" placeholder="What can you say about our application?"></textarea>
    <button class="submit-btn" id="submitFeedback">Submit</button>
  </div>
</main>

<script>
  const stars = document.querySelectorAll('.star');
  let selectedRating = 0;

  stars.forEach(star => {
    star.addEventListener('click', () => {
      selectedRating = parseInt(star.getAttribute('data-value'));
      stars.forEach(s => s.classList.remove('active'));
      for (let i = 0; i < selectedRating; i++) {
        stars[i].classList.add('active');
      }
    });
  });

  document.getElementById('submitFeedback').addEventListener('click', () => {
    const comment = document.getElementById('comment').value.trim();
    if (selectedRating === 0) {
      alert('Please select a star rating.');
      return;
    }

    const formData = new FormData();
    formData.append('rating', selectedRating);
    formData.append('comment', comment);

    fetch('feedbacks.php', { method: 'POST', body: formData })
      .then(res => res.text())
      .then(response => {
        if (response === 'success') {
          alert('Thank you for your feedback!');
          window.location.reload();
        } else {
          alert('Error submitting feedback.');
        }
      });
  });

  // Dropdown logic
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
