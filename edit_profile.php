<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $pronouns = mysqli_real_escape_string($conn, $_POST['pronouns']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES['profile_pic']['name']);
        $target_file = $target_dir . time() . "_" . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_type, $allowed)) {
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file);
            $query = "UPDATE users SET name='$name', pronouns='$pronouns', phone='$phone', profile_pic='$target_file' WHERE id='$user_id'";
        } else {
            echo "<p style='color:red'>Only JPG, JPEG, PNG & GIF files are allowed.</p>";
            $query = "UPDATE users SET name='$name', pronouns='$pronouns', phone='$phone' WHERE id='$user_id'";
        }
    } else {
       
        $query = "UPDATE users SET name='$name', pronouns='$pronouns', phone='$phone' WHERE id='$user_id'";
    }

    mysqli_query($conn, $query);
    header("Location: profile.php");
    exit();
}

$result = mysqli_query($conn, "SELECT name, pronouns, profile_pic, phone FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
$profile_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : 'images/default_profile.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="edit_profile.css">
</head>
<body>
<div class="edit-container">
    <h2>Edit Profile</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="profile-pic">
            <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
        </div>

        <label for="profile_pic">Change Profile Picture</label>
        <input type="file" name="profile_pic" accept="image/*">

        <label for="name">Username</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label for="pronouns">Pronouns</label>
        <input type="text" name="pronouns" value="<?php echo htmlspecialchars($user['pronouns']); ?>" placeholder="e.g. he/him">

        <label for="phone">Phone Number</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="e.g. 01012345678">

        <button type="submit">Save Changes</button>
        <a href="profile.php" class="back-btn">Cancel</a>
    </form>
</div>
</body>
</html>