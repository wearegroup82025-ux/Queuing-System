<?php
session_start();
require_once 'connect1.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
  header("Location: studentLogin1.php");
  exit;
}

// Get registrar info
$user_email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT fName, lName FROM register WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($fName, $lName);
$stmt->fetch();
$stmt->close();

// 🔧 Fetch only Counter 2 student queue/reservation data
$query = "SELECT ID, fName, lName, sched, counter, slot, status 
          FROM register 
          WHERE counter = '2'
          ORDER BY slot ASC";
$result = $conn->query($query);

// 🔧 Count total records for Counter 2
$total = $conn->query("SELECT COUNT(*) AS total FROM register WHERE counter = '2'");
$totalCount = $total->fetch_assoc()['total'];

// 🔧 Count per status for Counter 2
$countApproved = $conn->query("SELECT COUNT(*) AS total FROM register WHERE counter = '2' AND status='Approved'")->fetch_assoc()['total'];
$countPending = $conn->query("SELECT COUNT(*) AS total FROM register WHERE counter = '2' AND (status='Pending' OR status='' OR status IS NULL)")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Registrar Panel | BulSU Queueing System</title>
  <link rel="stylesheet" href="registrar.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    .approve-btn {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 5px;
      cursor: pointer;
    }
    .approve-btn:hover {
      background-color: #45a049;
    }
    .approved-status {
      color: green;
      font-weight: bold;
    }
    .pending-status {
      color: orange;
      font-weight: bold;
    }

    /* Floating Logout Popup */
    .logout-popup {
      position: absolute;
      top: 120px;
      left: 40px;
      background-color: #f3f3f3;
      border-radius: 12px;
      padding: 15px;
      width: 220px;
      text-align: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      display: none;
      z-index: 999;
      animation: fadeIn 0.2s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }
    .logout-popup .icon {
      font-size: 50px;
      color: #5e35b1;
    }
    .logout-popup p {
      margin: 5px 0;
      font-weight: bold;
      color: #444;
    }
    .logout-popup button {
      background: white;
      border: 1px solid #444;
      border-radius: 8px;
      padding: 6px 14px;
      cursor: pointer;
      font-size: 15px;
    }
    .logout-popup button:hover {
      background-color: #e6e6e6;
    }

    .logo {
      width: 120px;
      height: auto;
      cursor: pointer;
    }

    /* Reports Section */
    .chart-container {
      width: 60%;
      margin: 20px auto;
    }
    #refreshReports {
      background: #007bff;
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 6px;
      cursor: pointer;
    }
    #refreshReports:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="topbar">
    <div class="logo-container" style="display: ">
      <img id="logoButton" src="img/bsu logo.png" alt="BulSU Logo" class="logo"/>
      <h1>BulSU Queueing System</h1>
    </div>
  </header>

  <!-- FLOATING LOGOUT POPUP (outside layout) -->
  <div class="logout-popup" id="logoutPopup">
    <div class="icon">👤</div>
    <p>Registrar</p>
    <form action="logout.php" method="POST">
      <button type="submit">Log out</button>
    </form>
  </div>

  <!-- MAIN LAYOUT -->
  <div class="main-container">
    <!-- SIDEBAR -->
    <aside class="sidebar">
      <h2>Registrar Panel</h2>
      <ul>
        <li class="sidebar-item active" data-target="dashboard">Dashboard</li>
        <li class="sidebar-item" data-target="queueList">Queue List</li>
        <li class="sidebar-item" data-target="reports">Reports</li>
      </ul>
    </aside>

    <!-- CONTENT AREA -->
    <main class="content">
      <!-- DASHBOARD -->
      <section id="dashboard" class="section active">
        <h2>Registrar Dashboard</h2>
        <div class="card-container">
          <div class="card">
            <h3>Total Students (Counter 2)</h3>
            <p id="totalProcessed"><?php echo $totalCount; ?></p>
          </div>
        </div>
      </section>

      <!-- QUEUE LIST -->
      <section id="queueList" class="section">
        <div class="page-header">
          <h2>Queue List (Counter 2)</h2>
          <div>
            <button id="btnRefresh" class="action-btn" onclick="location.reload()">Refresh</button>
          </div>
        </div>
        <table id="queueTable">
  <thead>
    <tr>
      <th>Slot</th>
      <th>Student Name</th>
      <th>Schedule</th>
      <th>Counter</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $status = !empty($row['status']) ? $row['status'] : 'Pending';
        $statusClass = ($status === 'Approved') ? 'approved-status' : 'pending-status';
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['slot']) . "</td>";
        echo "<td>" . htmlspecialchars($row['fName'] . ' ' . $row['lName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['sched']) . "</td>";
        echo "<td>" . htmlspecialchars($row['counter']) . "</td>";
        echo "<td class='$statusClass'>" . htmlspecialchars($status) . "</td>";
        echo "<td>";
        
        if ($status !== 'Approved') {
          echo "<form method='POST' action='approve.php' style='display:inline;' onsubmit='return confirm(\"Approve this reservation?\");'>
                  <input type='hidden' name='id' value='" . htmlspecialchars($row['ID']) . "'>
                  <button type='submit' class='approve-btn'>Approve</button>
                </form>";
        } else {
          echo "<button disabled class='approve-btn' style='background-color: gray;'>Approved</button>";
        }
        
        echo "</td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='6' style='text-align:center;'>No records found for Counter 2.</td></tr>";
    }
    ?>
  </tbody>
</table>

      </section>

      <!-- REPORTS -->
      <section id="reports" class="section">
        <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
          <h2>Reports (Counter 2)</h2>
          <button id="refreshReports" onclick="location.reload()">Refresh Reports</button>
        </div>
        <p>Summary of queue records for Counter 2 as of <?php echo date("M d, Y"); ?></p>

        <table>
          <thead>
            <tr>
              <th>Status</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>Approved</td><td><?php echo $countApproved; ?></td></tr>
            <tr><td>Pending</td><td><?php echo $countPending; ?></td></tr>
            <tr style="font-weight:bold; background:#f2f2f2;"><td>Total Students</td><td><?php echo $totalCount; ?></td></tr>
          </tbody>
        </table>

        <div class="chart-container">
          <canvas id="reportChart"></canvas>
        </div>
      </section>
    </main>
  </div>
  
  <script>
    // Toggle Logout Popup
    const logoButton = document.getElementById('logoButton');
    const logoutPopup = document.getElementById('logoutPopup');
    logoButton.addEventListener('click', () => {
      logoutPopup.style.display = logoutPopup.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', (e) => {
      if (!logoutPopup.contains(e.target) && !logoButton.contains(e.target)) {
        logoutPopup.style.display = 'none';
      }
    });

    // Chart.js for Reports
    const ctx = document.getElementById('reportChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Approved', 'Pending', 'Total'],
        datasets: [{
          label: 'Number of Students',
          data: [<?php echo $countApproved; ?>, <?php echo $countPending; ?>, <?php echo $totalCount; ?>],
          backgroundColor: ['#4CAF50', '#FFA500', '#007bff']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          title: { display: true, text: 'Queue Summary (Counter 2)' }
        },
        scales: { y: { beginAtZero: true } }
      }
    });
  </script>
  
  <script src="registrer.js"></script>
</body>
</html>
