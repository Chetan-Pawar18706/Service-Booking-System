<?php
include 'db.php';
session_start();

$error = '';

if (isset($_POST["btnlogin"])) {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    // Authenticate against `users` table. Support legacy MD5-stored passwords too.
    $q = "SELECT id, username, password FROM users WHERE email='$email' OR username='$email' LIMIT 1";
    $res = mysqli_query($conn, $q);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $stored = $row['password'];

        $ok = false;
        if (password_verify($password, $stored)) {
            $ok = true;
        } elseif (md5($password) === $stored) {
            // legacy support for MD5-hashed password in DB
            $ok = true;
        }

        if ($ok) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $row['username'];

            if ($row['username'] === 'admin') {
                header('Location: admindashbord.php');
                exit();
            } else {
                header('Location: dashbord.php');
                exit();
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Login — Astra Service</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-wrapper {
            min-height: calc(100vh - 48px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
        }
        .form .label {display:block;margin-bottom:6px;color:var(--muted);}
    </style>
</head>

<body>

    <div class="auth-wrapper">
        <div class="auth-card card">
            <?php if (!empty($error)): ?>
                <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card .title">
                    <h2 class="h1">User Login</h2>
                    <p class="small">Sign in to continue to your dashboard</p>
                </div>

                <form method="post" class="form" autocomplete="off">

                    <label for="email" class="label">Email or Username</label>
                    <input type="text" id="email" name="email" placeholder="you@example.com" required>

                    <label for="password" class="label">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>

                    <button type="submit" name="btnlogin" class="btn btn-block" style="margin-top:14px">🔓 Login</button>

                    <div style="margin-top:12px;text-align:center">
                        <a href="register.php" class="btn secondary btn-sm" style="text-decoration:none">Create account</a>
                        <a href="index.php" class="btn info btn-sm" style="text-decoration:none;margin-left:8px">Home</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>










