<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

$query = "SELECT name, profile_pic, pronouns FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// صورة افتراضية لو مفيش صورة محفوظة
$profile_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : 'images/pfp.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
<div class="profile-container">
    <div class="banner"></div>

    <div class="profile-pic">
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
    </div>

    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
    <p class="pronouns"><?php echo htmlspecialchars($user['pronouns'] ?? ''); ?></p>

    <a href="edit_profile.php" class="edit-btn">Edit Profile</a>

    <!-- زرار اللوج آوت تحت زرار الإيدت -->
    <a href="logout.php" class="logout-btn">Logout</a>
</div>
</body>
</html>
