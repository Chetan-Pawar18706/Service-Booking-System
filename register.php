<?php
require 'db.php';
session_start();

$message = '';

if (isset($_POST['btnreg'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    
    // Validate role
    if (!in_array($role, ['user', 'provider'])) {
        $role = 'user';
    }

    if ($name === '' || $email === '' || $password === '') {
        $message = "Name, email, and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        if (!$check) {
            $message = "Database error: could not prepare duplicate check.";
        } else {
            $check->bind_param('ss', $name, $email);
            $check->execute();
            $existing = $check->get_result();
            $alreadyExists = $existing && $existing->num_rows > 0;
            $check->close();

            if ($alreadyExists) {
                $message = "Username or email already exists.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)");

                if ($stmt) {
                    $stmt->bind_param('ssssss', $name, $email, $hashed, $role, $phone, $address);
                    if ($stmt->execute()) {
                        $stmt->close();
                        $_SESSION['flash_success'] = "Registration successful. Please login.";
                        header('Location: login.php');
                        exit();
                    }

                    $message = "Registration failed: " . $conn->error;
                    $stmt->close();
                } else {
                    $message = "Database error: could not prepare registration query.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Astra Service</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="auth-wrapper">
        <section class="auth-card card">
            <div class="auth-title">
                <a class="brand-full" href="index.php">Astra Service</a>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="auth-title">
                <h1 class="h1">Create Account</h1>
                <p class="small">Create your account and book trusted local providers.</p>
            </div>

            <form method="post" class="form" autocomplete="off">
                <label class="label" for="name">Name</label>
                <input id="name" type="text" name="name" required>

                <label class="label" for="email">Email</label>
                <input id="email" type="email" name="email" required>

                <label class="label" for="phone">Phone</label>
                <input id="phone" type="tel" name="phone">

                <label class="label" for="address">Address</label>
                <input id="address" type="text" name="address">

                <label class="label" for="role">Register as</label>
                <select id="role" name="role" required>
                    <option value="user">Customer (Book Services)</option>
                    <option value="provider">Service Provider</option>
                </select>

                <label class="label" for="password">Password</label>
                <input id="password" type="password" name="password" required>

                <button type="submit" class="btn btn-block" name="btnreg" style="margin-top:12px">Register</button>
            </form>

            <div class="auth-links">
                <span class="small">Already have an account?</span>
                <a href="login.php" style="color:var(--accent-2)">Login here</a>
            </div>
        </section>
    </main>
</body>
</html>
