<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// جلب البيانات الحالية للطبيب
$query = "SELECT name, profile_pic, specialization, phone, location, available_times, pronouns 
          FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $availability = $_POST['availability'];
    $pronouns = $_POST['pronouns'];

    // رفع الصورة لو تم اختيار واحدة جديدة
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
        $profile_pic = $target_file;
    } else {
        $profile_pic = $user['profile_pic'];
    }

    $update_query = "UPDATE users 
                     SET name='$name', specialization='$specialization', phone='$phone', 
                         location='$location', available_times='$availability', 
                         pronouns='$pronouns', profile_pic='$profile_pic' 
                     WHERE id='$user_id'";
    mysqli_query($conn, $update_query);

    header("Location: doctor_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Doctor Profile</title>
    <link rel="stylesheet" href="edit_doctor_profile.css">
</head>
<body>
<div class="profile-container">
    <h2>Edit Profile</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="profile-pic">
            <img src="<?php echo htmlspecialchars($user['profile_pic'] ?: 'images/pfp.png'); ?>" alt="Profile Picture">
        </div>

        <label for="profile_pic">Change Picture:</label>
        <input type="file" name="profile_pic" id="profile_pic" accept="image/*">

        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label for="pronouns">Pronouns:</label>
        <input type="text" name="pronouns" value="<?php echo htmlspecialchars($user['pronouns'] ?? ''); ?>">

        <label for="specialization">Specialization:</label>
        <input type="text" name="specialization" value="<?php echo htmlspecialchars($user['specialization'] ?? ''); ?>">

        <label for="phone">Phone Number:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">

        <label for="location">Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">

        <label for="availability">Available Times:</label>
        <textarea name="availability" rows="3"><?php echo htmlspecialchars($user['availability'] ?? ''); ?></textarea>

        <button type="submit" class="edit-btn">Save Changes</button>
        <a href="doctor_profile.php" class="logout-btn">Cancel</a>
    </form>
</div>
</body>
</html>
