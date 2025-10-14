<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = $_SESSION['id'];

// ✅ لو الدكتور ضغط على Confirm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    $booking_id = intval($_POST['booking_id']);
    $update_query = "UPDATE bookings SET status = 'confirmed' WHERE id = '$booking_id' AND doctor_id = '$doctor_id'";
    mysqli_query($conn, $update_query);
}

// 📋 جلب الحجوزات
$query = "SELECT 
            b.id AS booking_id,
            u.name AS patient_name,
            u.phone AS patient_phone,
            b.appointment_type,
            b.day,
            b.time,
            b.location,
            b.contact_method,
            b.status
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
        .status {
            font-weight: bold;
            text-transform: capitalize;
        }
        .status.pending {
            color: #f39c12;
        }
        .status.confirmed {
            color: #27ae60;
        }
        .confirm-btn {
            background: #27ae60;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        .confirm-btn:hover {
            background: #219150;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 1050px;
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
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['booking_id']; ?></td>
            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
            <td><?php echo htmlspecialchars($row['patient_phone']); ?></td>
            <td><?php echo htmlspecialchars($row['appointment_type']); ?></td>
            <td><?php echo htmlspecialchars($row['day']); ?></td>
            <td><?php echo htmlspecialchars($row['time']); ?></td>
            <td><?php echo htmlspecialchars($row['location']); ?></td>
            <td>
                <?php 
                    if (strtolower($row['appointment_type']) !== 'inperson') {
                        echo htmlspecialchars($row['contact_method']);
                    }
                ?>
            </td>
            <td class="status <?php echo $row['status']; ?>">
                <?php echo $row['status']; ?>
            </td>
            <td>
                <?php if ($row['status'] === 'pending') { ?>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                        <button type="submit" name="confirm_booking" class="confirm-btn">Confirm</button>
                    </form>
                <?php } else { echo "✔️"; } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
