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
            background: #e9f9f1;
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
            color: #2ecc71;
            margin-bottom: 10px;
        }
        .confirmed-box p {
            color: #555;
        }
        a.back-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #2ecc71;
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }
        a.back-btn:hover {
            background: #27ae60;
        }
    </style>
</head>
<body>
    <div class="confirmed-box">
        <h1>✅ Appointment Confirmed!</h1>
        <p>Your booking has been successfully submitted.</p>
        <a href="user_dashboard.php" class="back-btn">Back to Home</a>
    </div>
</body>
</html>