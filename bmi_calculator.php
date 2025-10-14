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
    $bmi = round($bmi, 2);
    $date_recorded = date('Y-m-d');

    if ($bmi < 18.5) {
        $status = "Underweight";
        $advice = "Try increasing your calorie intake and include more protein in your diet!";
    } elseif ($bmi >= 18.5 && $bmi < 24.9) {
        $status = "Normal weight";
        $advice = "Great job! Keep maintaining your healthy habits!";
    } elseif ($bmi >= 25 && $bmi < 29.9) {
        $status = "Overweight";
        $advice = "Consider adding more cardio or adjusting your diet slightly.";
    } else {
        $status = "Obese";
        $advice = "It might be good to consult a nutritionist and start a consistent workout plan.";
    }

    $query = "INSERT INTO bmi_records (user_id, weight, height, bmi, date_recorded)
              VALUES ('$user_id', '$weight', '$height', '$bmi', '$date_recorded')";

    if (mysqli_query($conn, $query)) {
        $message = "• Your BMI is <b>$bmi</b> — <b>$status</b>.<br>$advice";
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
            background: linear-gradient(270deg, rgba(81, 174, 229, 0.3), rgba(100, 200, 255, 0.3), rgba(62, 192, 255, 0.3));
            background-size: 600% 600%;
            animation: gradientMove 15s ease infinite;
            text-align: center;
            padding: 50px;
        }

        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #ffffff;
            color: #dc3d87;
            border: 2px solid #dc3d87;
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 999;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            width: 100px;
        }

        .back-btn:hover {
            background: #dc3d87;
            color: white;
            transform: scale(1.05);
        }


        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            width: 480px;
            text-align: center;
            margin-top: 50px;
        }

        input {
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: 90%;
        }

        button {
            padding: 10px 20px;
            background: #dc3d87;
            border: none;
            border-radius: 6px;
            color: white;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background: #51aee5;
        }

        .msg {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 400px;
            text-align: center;
        }

        .msg b {
            color: #51aee5;
        }

        @media (max-width:768px ) {
            form {
                width: 300px;

            }
                
        }
        

    </style>
</head>
<body>

<button class="back-btn" onclick="window.location.href='user_page.php'">← Back</button>

<h2 style="color: #dc3d87; margin-top: 40px;">BMI Calculator</h2>

<div class="container">
    <form method="POST">
        <input type="number" name="weight" placeholder="Weight (kg)" step="0.1" required><br>
        <input type="number" name="height" placeholder="Height (cm)" step="0.1" required><br>
        <button type="submit">Calculate BMI</button>
    </form>

    <?php if (!empty($message)) : ?>
        <div class="msg"><?php echo $message; ?></div>
    <?php endif; ?>
</div>

</body>
</html>
