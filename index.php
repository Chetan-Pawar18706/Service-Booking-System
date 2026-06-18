<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astra Service - Login/Sign Up</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0d0d0d;
            color: #d3d3d3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #1a1a1a;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            width: 350px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #ffffff;
            font-size: 2em;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #a0a0a0;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            background-color: #262626;
            border: 1px solid #404040;
            border-radius: 5px;
            color: #ffffff;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-login {
            background-color: #595959;
            color: #ffffff;
        }
        .btn-login:hover {
            background-color: #6c6c6c;
        }
        .btn-signup {
            background-color: #404040;
            color: #ffffff;
            margin-top: 10px;
        }
        .btn-signup:hover {
            background-color: #595959;
        }
        .message {
            margin-top: 15px;
            color: #90ee90;
        }
        .error {
            margin-top: 15px;
            color: #ff6347;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Astra Service</h2>
        
        <?php
        session_start();
        if (isset($_SESSION['message'])) {
            echo '<p class="message">' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']);
        }
        if (isset($_SESSION['error'])) {
            echo '<p class="error">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <h3>Login</h3>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="login_username">Email</label>
                <input type="email" id="login_username" name="email" required>
            </div>
            <div class="form-group">
                <label for="login_password">Password</label>
                <input type="password" id="login_password" name="password" required>
            </div>
            <button type="submit" class="btn btn-block" name="btnlogin">🔓 Login</button>
        </form>

        <hr style="border-color: #404040; margin: 30px 0;">

        <h3>Sign Up</h3>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="signup_username">Username</label>
                <input type="text" id="signup_username" name="name" required>
            </div>
            <div class="form-group">
                <label for="signup_email">Email</label>
                <input type="email" id="signup_email" name="email" required>
            </div>
            <div class="form-group">
                <label for="signup_password">Password</label>
                <input type="password" id="signup_password" name="password" required>
            </div>
            <button type="submit" class="btn btn-block secondary" name="btnreg">✒️ Sign Up</button>
        </form>
    </div>
</body>
</html>
