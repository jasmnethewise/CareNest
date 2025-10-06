<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir);

    $filename = basename($_FILES["profile_pic"]["name"]);
    $target_file = $target_dir . time() . "_" . $filename;

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $update = "UPDATE users SET profile_pic='$target_file' WHERE id='$user_id'";
        mysqli_query($conn, $update);
        header("Location: profile.php");
        exit();
    }
}
?>
