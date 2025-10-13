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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav>
         <img src="images/CareNest.png" alt="CareNest Logo" class="logo">
         <ul>
            <li><a href="user_page.php">Home</a></li> 
            <li><a href="services.php">Services</a></li> 
            <li><a href="chatbot.html">Chatbot</a></li> 
            <li><a href="#">Dashboard</a></li> 
            
        </ul>

        <div class="profile">
            <a href="profile.php"><i class="fa-solid fa-user"></i></a>
        </div>
        
    </nav>
    <h1 class="page-title">Book With Our Doctors</h1>

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