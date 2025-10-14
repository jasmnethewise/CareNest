<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $appointment_type = $_POST['appointment_type'];
    $day = $_POST['day'];
    $time = $_POST['time'];
    $location = isset($_POST['location']) ? $_POST['location'] : null;
    $contact = isset($_POST['contact']) ? $_POST['contact'] : null;

    $user_id = $_SESSION['id'];

    if (empty($user_id)) {
        die(" User not logged in properly. Session ID missing!");
    }

    
    $booking_date = date('Y-m-d');

    
    $query = "INSERT INTO bookings (doctor_id, user_id, appointment_type, day, time, location, contact_method, booking_date)
              VALUES ('$doctor_id', '$user_id', '$appointment_type', '$day', '$time', '$location', '$contact', '$booking_date')";

    if (mysqli_query($conn, $query)) {
        header("Location: booking_confirmed.php");
        exit();
    } else {
        echo " Error: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
    exit();
}
?>
