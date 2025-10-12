<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['user_id'])) {
    
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

    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

    
    $query = "INSERT INTO bookings (doctor_id, user_id, appointment_type, day, time, location, contact_method)
              VALUES ('$doctor_id', '$user_id', '$appointment_type', '$day', '$time', '$location', '$contact')";

    if (mysqli_query($conn, $query)) {
        
        header("Location: booking_confirmed.php");
        exit();
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
    exit();
}
?>