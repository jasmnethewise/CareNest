<?php
include 'config.php';
session_start();

$doctor_id = $_SESSION['user_id']; 

$query = "SELECT b.*, u.name AS user_name 
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          WHERE b.doctor_id = '$doctor_id'
          ORDER BY b.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Bookings</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: #f5f9ff;
  padding: 20px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}
table, th, td {
  border: 1px solid #ccc;
}
th {
  background: #2ecc71;
  color: white;
  padding: 10px;
}
td {
  padding: 10px;
  text-align: center;
}
</style>
</head>
<body>
<h1>Your Bookings</h1>
<table>
<tr>
  <th>Patient</th>
  <th>Type</th>
  <th>Day</th>
  <th>Time</th>
  <th>Location / Contact</th>
  <th>Date</th>
</tr>

<?php
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$row['user_name']}</td>
            <td>{$row['appointment_type']}</td>
            <td>{$row['day']}</td>
            <td>{$row['time']}</td>
            <td>" . 
              ($row['appointment_type'] == 'inperson' ? $row['location'] : $row['contact_method']) . 
            "</td>
            <td>{$row['created_at']}</td>
          </tr>";
  }
} else {
  echo "<tr><td colspan='6'>No bookings yet.</td></tr>";
}
?>
</table>
</body>
</html>
