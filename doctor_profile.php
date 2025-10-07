<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$query = "SELECT name, profile_pic, specialization, location, phone, available_times FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);


$profile_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : 'images/pfp.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="doctor_profile.css">
</head>
<body>
<div class="profile-container">
    <div class="banner"></div>
    <div class="profile-pic">
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
    </div>

    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($user['specialization'] ?? 'Not added'); ?></p>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($user['location'] ?? 'Not added'); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Not added'); ?></p>
    <p><strong>Available Times:</strong> <?php echo htmlspecialchars($user['available_times'] ?? 'Not added'); ?></p>

    <a href="edit_doctor_profile.php" class="edit-btn">Edit Profile</a>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>
</body>
</html>
