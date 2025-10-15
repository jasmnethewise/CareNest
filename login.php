<?php

session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$active_form = $_SESSION['active_form'] ?? 'login';

unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form']);

function showEror($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($form, $active_form) {
    return $form === $active_form ? 'active' : '';

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="data:,">
</head>
<body>
    
    <div class="background">
        <img src="images/heart_icon.png" class="heart" style="--x:0.1; --d:0.3; --delay:0.2;">
        <img src="images/heart_icon.png" class="heart" style="--x:0.25; --d:0.5; --delay:0.6;">
        <img src="images/heart_icon.png" class="heart" style="--x:0.5; --d:0.8; --delay:0.1;">
        <img src="images/heart_icon.png" class="heart" style="--x:0.7; --d:0.4; --delay:0.9;">
        <img src="images/heart_icon.png" class="heart" style="--x:0.9; --d:0.7; --delay:0.3;">
    </div>




    <div class="container">
        <div class="right-side">
            <div class="form-box <?= isActiveForm('login', $active_form); ?> " id="login-form">
                <form action="CareNest.php" method="post">
                    <h2>Login</h2>
                    <?= showEror($errors['login']) ?>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login" >Login</button>
                </form>
                
                <p class="terms">By creating an account, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</p>
                <p>Don't have an account? <a href="#" onclick="showForm('register-form')">Create account</a></p>
            </div>

            <div class="form-box <?= isActiveForm('register', $active_form); ?>"  id="register-form">
                <form action="CareNest.php" method="post">
                    <h2>Create account</h2>
                    <?= showEror($errors['register']) ?>
                    <input type="text" name="name" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <select name="role" required>
                        <option value="">--- Select Role ---</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>


                    </select>
                    <button type="submit" name="register" >Register</button>
                </form>
                <p class="terms">By creating an account, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</p>
                <p> Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
            </div>
        </div>
    </div>


    <script src="login.js"></script>

    <script>

        const socialButtons = document.querySelectorAll('.social-icons button');

        socialButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault(); 
                alert('Feature coming soon!');
            });
        });
    </script>


</body>
</html>