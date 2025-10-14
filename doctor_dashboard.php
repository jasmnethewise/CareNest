<?php
session_start();
include 'config.php';


if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['id'];


$date_today = date('Y-m-d');
$result = mysqli_query($conn, "SELECT last_login, streak FROM users WHERE id='$doctor_id'");
$user = mysqli_fetch_assoc($result);

$last_login = $user['last_login'];
$streak = $user['streak'];

if ($last_login !== $date_today) {
    if ($last_login === date('Y-m-d', strtotime('-1 day'))) {
        $streak += 1;
    } else {
        $streak = 1; 
    }
    mysqli_query($conn, "UPDATE users SET last_login='$date_today', streak='$streak' WHERE id='$doctor_id'");
}

$query_users = "
    SELECT COUNT(DISTINCT user_id) AS total_users 
    FROM bookings 
    WHERE doctor_id = '$doctor_id' AND status = 'confirmed'
";
$result_users = mysqli_query($conn, $query_users);
$total_users = mysqli_fetch_assoc($result_users)['total_users'] ?? 0;


$query_confirmed = "
    SELECT COUNT(*) AS confirmed_appointments
    FROM bookings
    WHERE doctor_id = '$doctor_id' AND status = 'confirmed'
";
$result_confirmed = mysqli_query($conn, $query_confirmed);
$confirmed_appointments = mysqli_fetch_assoc($result_confirmed)['confirmed_appointments'] ?? 0;



$query_week = "
    SELECT COUNT(*) AS week_appointments
    FROM bookings
    WHERE doctor_id = '$doctor_id' 
    AND status = 'confirmed'
    AND YEARWEEK(booking_date, 1) = YEARWEEK(CURDATE(), 1)
";
$result_week = mysqli_query($conn, $query_week);
$week_appointments = mysqli_fetch_assoc($result_week)['week_appointments'] ?? 0;


$query_types = "
    SELECT 
        SUM(CASE WHEN appointment_type = 'online' THEN 1 ELSE 0 END) AS online_count,
        SUM(CASE WHEN appointment_type = 'inperson' THEN 1 ELSE 0 END) AS inperson_count
    FROM bookings
    WHERE doctor_id = '$doctor_id' AND status = 'confirmed'
";

$query_pending = "
    SELECT COUNT(*) AS pending_appointments
    FROM bookings
    WHERE doctor_id = '$doctor_id' AND status = 'pending'
";
$result_pending = mysqli_query($conn, $query_pending);
$pending_appointments = mysqli_fetch_assoc($result_pending)['pending_appointments'] ?? 0;

$result_types = mysqli_query($conn, $query_types);
$types = mysqli_fetch_assoc($result_types);
$online_count = $types['online_count'] ?? 0;
$inperson_count = $types['inperson_count'] ?? 0;


$query_appointments = "
    SELECT b.*, u.name AS user_name, u.phone 
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    WHERE b.doctor_id = '$doctor_id' AND b.status = 'confirmed'
    ORDER BY b.created_at DESC
    LIMIT 5
";
$result_appointments = mysqli_query($conn, $query_appointments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="doctor_dashboard.css">
    <style>
        body {
            background: linear-gradient(270deg, rgba(81, 174, 229, 0.3), rgba(100, 200, 255, 0.3), rgba(62, 192, 255, 0.3));
            background-size: 600% 600%;
            animation: gradientMove 15s ease infinite;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
            color: #333;
        }
        .dashboard-container {
            max-width: 900px;
            margin: auto;
        }
        h1 {
            color: #dc3d87;
        }
        .stats {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 20px 0;
        }
        .card {
            flex: 1;
            min-width: 200px;
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin: 0;
            color: #51aee5;
        }
        .card p {
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }
        .appointments {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        .appointment-box {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .appointment-box h4 {
            margin: 0;
            color: #dc3d87;
        }
        .appointment-box p {
            margin: 5px 0;
            font-size: 14px;
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
        }

        .back-btn:hover {
            background: #dc3d87;
            color: white;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <button class="back-btn" onclick="window.location.href='admin_page.php'">← Back</button>
    <div class="dashboard-container">
        <h1>Doctor's Dashboard</h1>

        <div class="stats">
            <div class="card streak">
                <h3>🔥 Streak</h3>
                <p><?php echo $streak; ?> days in a row</p>
            </div>

            <div class="card users">
                <h3>Total Patients</h3>
                <p><?php echo $total_users; ?> users</p>
            </div>

            <div class="card confirmed">
                <h3>Confirmed Appointments</h3>
                <p><?php echo $confirmed_appointments; ?> confirmed total</p>
            </div>

            <div class="card week">
                <h3>This Week</h3>
                <p><?php echo $week_appointments; ?> confirmed this week</p>
            </div>

            <div class="card pending">
                <h3>Pending</h3>
                <p><?php echo $pending_appointments; ?> awaiting confirmation</p>
            </div>

            <div class="card type">
                <h3>Online vs In Person</h3>
                <p><?php echo $online_count; ?> Online | <?php echo $inperson_count; ?> In-person</p>
            </div>
        </div>

        <h2> ◉ Recent Appointments</h2>
        <div class="appointments">
            <?php while ($row = mysqli_fetch_assoc($result_appointments)): ?>
                <div class="appointment-box">
                    <h4><?php echo htmlspecialchars($row['user_name']); ?></h4>
                    <p>📞 <?php echo htmlspecialchars($row['phone']); ?></p>
                    <p>📆 <?php echo htmlspecialchars($row['day']); ?> - 🕒 <?php echo htmlspecialchars($row['time']); ?></p>
                    <p>
                        <?php 
                            echo $row['appointment_type'] == 'inperson' 
                                ? '🏥 In-person' 
                                : '💻 Online';
                        ?>
                    </p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
