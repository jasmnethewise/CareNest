<?php

session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$active_form = $_SESSION['active_form'] ?? 'login';

session_unset();

function showEror($error) {
    return !empty($error) ? "<p class='eror-message'>$error</p>" : '';
}

function isActive($form, $active_form) {
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
    <div class="container">
        <div class="right-side">
            <div class="form-box active" id="login-form">
                <form action="CareNest.php" method="post">
                    <h2>Login</h2>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login" >Login</button>
                </form>
                <p>or sign up with</p>
                <div class="social-icons">
                    <button class="google"><i class="fa-brands fa-google"></i></button>
                    <button class="facebook"><i class="fa-brands fa-facebook-f"></i></button>
                    <button class="apple"><i class="fa-brands fa-apple"></i></button>
                </div>
                <p class="terms">By creating an account, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</p>
                <p>Don't have an account? <a href="#" onclick="showForm('register-form')">Create account</a></p>
            </div>

            <div class="form-box"  id="register-form">
                <form action="CareNest.php" method="post">
                    <h2>Create account</h2>
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

</body>
</html>