<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Confirmed</title>
    <link rel="stylesheet" href="book.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(270deg, rgba(81, 174, 229, 0.3), rgba(100, 200, 255, 0.3), rgba(62, 192, 255, 0.3));
            background-size: 600% 600%;
            animation: gradientMove 15s ease infinite;
            font-family: Arial, sans-serif;
        }
        .confirmed-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .confirmed-box h1 {
            color: #dc3d87;
            margin-bottom: 10px;
        }
        .confirmed-box p {
            color: #555;
        }
        a.back-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #dc3d87;
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }
        a.back-btn:hover {
            background: #dc3d87;
        }
    </style>
</head>
<body>
    <div class="confirmed-box">
        <h1> ✔️ Appointment Confirmed!</h1>
        <p>Your booking has been successfully submitted.</p>
        <a href="user_page.php" class="back-btn">Back to Home</a>
    </div>
</body>
</html>