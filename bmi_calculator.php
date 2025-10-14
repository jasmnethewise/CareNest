<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];

    
    $bmi = $weight / pow($height / 100, 2);

    
    $date_recorded = date('Y-m-d');

    
    $query = "INSERT INTO bmi_records (user_id, weight, height, bmi, date_recorded)
              VALUES ('$user_id', '$weight', '$height', '$bmi', '$date_recorded')";

    if (mysqli_query($conn, $query)) {
        $message = "✅ BMI recorded successfully! Your BMI is: " . round($bmi, 2);
    } else {
        $message = "❌ Database error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BMI Calculator</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            text-align: center;
            padding: 50px;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        input {
            padding: 10px;
            margin: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 200px;
        }
        button {
            padding: 10px 20px;
            background: #007bff;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .msg {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>BMI Calculator</h2>
<form method="POST">
    <input type="number" name="weight" placeholder="Weight (kg)" step="0.1" required><br>
    <input type="number" name="height" placeholder="Height (cm)" step="0.1" required><br>
    <button type="submit">Calculate BMI</button>
</form>

<div class="msg"><?php echo $message; ?></div>

</body>
</html>
