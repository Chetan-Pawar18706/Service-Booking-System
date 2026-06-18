<?php
include 'db.php';
session_start();

$error = $_SESSION['flash_error'] ?? '';
$success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_error'], $_SESSION['flash_success']);

if (isset($_POST["btnlogin"])) {
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter email/username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ? OR username = ? LIMIT 1");

        if ($stmt) {
            $stmt->bind_param('ss', $email, $email);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $stored = $row['password'];
                $ok = password_verify($password, $stored) || md5($password) === $stored;

                if ($ok) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['user_id'] = (int)$row['id'];
                    $_SESSION['username'] = $row['username'];

                    if ($row['username'] === 'admin') {
                        header('Location: admindashbord.php');
                        exit();
                    }

                    header('Location: dashbord.php');
                    exit();
                }
            }

            $stmt->close();
            $error = "Invalid username or password.";
        } else {
            $error = "Database error: could not prepare login query.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Astra Service</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
        }

        .auth-title {
            margin-bottom: 20px;
        }

        .auth-links {
            margin-top: 12px;
            text-align: center;
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <main class="auth-wrapper">
        <section class="auth-card card">
            <?php if (!empty($success)): ?>
                <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <div class="auth-title">
                <h1 class="h1">User Login</h1>
                <p class="small">Sign in to continue to your dashboard.</p>
            </div>

            <form method="post" class="form" autocomplete="off">
                <label for="email" class="label">Email or Username</label>
                <input type="text" id="email" name="email" placeholder="you@example.com" required>

                <label for="password" class="label">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>

                <button type="submit" name="btnlogin" class="btn btn-block" style="margin-top:14px">Login</button>

                <div class="auth-links">
                    <a href="register.php" class="btn secondary btn-sm">Create account</a>
                    <a href="index.php" class="btn info btn-sm">Home</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
