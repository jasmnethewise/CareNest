<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

$streak_query = mysqli_query($conn, "SELECT streak, name FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($streak_query);
$streak = $user_data['streak'];
$user_name = $user_data['name'];


$bookings_query = mysqli_query($conn, "
    SELECT 
        b.day, 
        b.time, 
        b.appointment_type, 
        b.status, 
        u.name AS doctor_name
    FROM bookings b
    JOIN users u ON b.doctor_id = u.id
    WHERE b.user_id = '$user_id'
    ORDER BY b.created_at DESC
    LIMIT 5
");


$bmi_query = mysqli_query($conn, "
    SELECT bmi, date_recorded 
    FROM bmi_records 
    WHERE user_id = '$user_id'
    ORDER BY date_recorded DESC
    LIMIT 3
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: linear-gradient(270deg, rgba(81, 174, 229, 0.3), rgba(100, 200, 255, 0.3), rgba(62, 192, 255, 0.3));
      background-size: 600% 600%;
      animation: gradientMove 15s ease infinite;
      margin: 0;
      padding: 0;
    }
    .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #ffffff;
            color: #dc3d87;
            border: 2px solid #dc3d87;
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 999;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            width: 100px;
        }

        .back-btn:hover {
            background: #dc3d87;
            color: white;
            transform: scale(1.05);
        }
    .dashboard-container {
      max-width: 900px;
      margin: 50px auto;
      background: #fff;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    h1 {
      color: #dc3d87;
      margin-bottom: 20px;
      text-align: center;
    }
    .streak-box {
      background: #ffe9c4;
      border: 2px solid #ffb84d;
      border-radius: 12px;
      padding: 15px;
      text-align: center;
      font-weight: bold;
      color: #ff8c00;
      margin-bottom: 30px;
    }
    .section {
      margin-bottom: 40px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
    }
    th, td {
      text-align: center;
      padding: 12px;
      border-bottom: 1px solid #eee;
    }
    th {
      background: #51aee5;
      color: white;
    }
    tr:hover {
      background: #f1f1f1;
    }
    .status {
      padding: 5px 10px;
      border-radius: 6px;
      color: white;
      font-size: 0.9em;
    }
    .status.pending { background: #ffb74d; }
    .status.confirmed { background: #4caf50; }
    .status.canceled { background: #e53935; }
  </style>
</head>
<body>

<button class="back-btn" onclick="window.location.href='user_page.php'">← Back</button>

<div class="dashboard-container">
  <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?></h1>

  <div class="streak-box">
    🔥 You're on a <strong><?php echo $streak; ?></strong>-day streak!
  </div>

  <div class="section">
    <h2 style="color: #dc3d87;">• Recent Appointments</h2>
    <table>
      <tr>
        <th>Doctor</th>
        <th>Day</th>
        <th>Time</th>
        <th>Type</th>
        <th>Status</th>
      </tr>
      <?php if (mysqli_num_rows($bookings_query) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($bookings_query)): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
            <td><?php echo htmlspecialchars($row['day']); ?></td>
            <td><?php echo htmlspecialchars($row['time']); ?></td>
            <td><?php echo ucfirst($row['appointment_type']); ?></td>
            <td><span class="status <?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="5">No appointments found.</td></tr>
      <?php endif; ?>
    </table>
  </div>

  <div class="section">
    <h2 style="color: #dc3d87;"> • Recent BMI Records</h2>
    <table>
      <tr>
        <th>Date</th>
        <th>BMI</th>
      </tr>
      <?php if (mysqli_num_rows($bmi_query) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($bmi_query)): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['date_recorded']); ?></td>
            <td><?php echo htmlspecialchars($row['bmi']); ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="2">No BMI records found.</td></tr>
      <?php endif; ?>
    </table>
  </div>

</div>

</body>
</html>
