<?php
include('db.php');
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check if user is admin
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: admindashbord.php');
        exit();
    } else {
        $error = 'Invalid credentials.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="card" style="max-width:350px;margin:40px auto">
        <h2 class="h1">Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" class="form">
            <label class="small">Username</label>
            <input type="text" name="username" required>
            <label class="small">Password</label>
            <input type="password" name="password" required>
            <div style="margin-top:14px"><button class="btn" type="submit">Login</button></div>
        </form>
    </div>
</div>
</body>
</html>
