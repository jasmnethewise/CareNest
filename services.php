<?php
include 'config.php';
session_start();


$query = "SELECT id, name, profile_pic, specialization FROM users WHERE role = 'admin' OR role = 'doctor'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors</title>
    <link rel="stylesheet" href="services.css">
</head>
<body>

    <h1 class="page-title">Meet Our Doctors</h1>

    <div class="doctor-container">
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <div class="doctor-card">

                <?php 
                $profile_pic = !empty($row['profile_pic']) ? $row['profile_pic'] : 'images/pfp.png';
                ?>
                <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Doctor Picture" class="doctor-pic">

                <h2 class="doctor-name"><?php echo htmlspecialchars($row['name']); ?></h2>
                <p class="doctor-specialization"><?php echo htmlspecialchars($row['specialization']); ?></p>
                <a href="book.php?doctor_id=<?php echo $row['id']; ?>" class="book-btn">Book</a>
           </div>
        <?php } ?>
    </div>

</body>
</html>