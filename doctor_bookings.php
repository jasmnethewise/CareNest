<?php
session_start();
require_once 'config.php';


if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['id'];


$query = "SELECT 
            b.id AS booking_id,
            u.name AS patient_name,
            u.phone AS patient_phone,
            b.appointment_type,
            b.day,
            b.time,
            b.location,
            b.contact_method
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          WHERE b.doctor_id = '$doctor_id'
          ORDER BY b.day, b.time";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f6fa;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #51aee5;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 1150px;
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
    <h1 style="color: #dc3d87;">Welcome, <?php echo $_SESSION['name']; ?></h1>
    <h3 style="color: #dc3d87;">Your Appointments</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Type</th>
            <th>Day</th>
            <th>Time</th>
            <th>Location</th>
            <th>Contact Method</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['booking_id']; ?></td>
            <td><?php echo $row['patient_name']; ?></td>
            <td><?php echo htmlspecialchars($row['patient_phone']); ?></td>
            <td><?php echo $row['appointment_type']; ?></td>
            <td><?php echo $row['day']; ?></td>
            <td><?php echo $row['time']; ?></td>
            <td><?php echo $row['location']; ?></td>
            <td>
                <?php 
                    if (strtolower($row['appointment_type']) !== 'inperson') {
                        echo htmlspecialchars($row['contact_method']);
                    } 
                ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
