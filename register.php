<?php
require("db.php");

$message = '';

if (isset($_POST['btnreg'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        $message = "Please fill all fields.";
    } else {
        // Use prepared statement and store hashed password
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param('sss', $name, $email, $hashed);
            if ($stmt->execute()) {
                $stmt->close();
                echo "<script>alert('Form submitted successfully!');window.location.href='login.php'</script>";
                exit();
            } else {
                // Handle duplicate email/username or other DB error
                $message = 'Registration failed: ' . $conn->error;
            }
        } else {
            $message = 'Database error: could not prepare statement.';
        }
    }


    






}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register — Astra Service</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-wrapper { min-height: calc(100vh - 48px); display:flex; align-items:center; justify-content:center; padding:20px }
        .auth-card { width:100%; max-width:520px }
        .form .label { display:block; margin-bottom:6px; color:var(--muted) }
        .input { width:100%; padding:10px; border-radius:6px; border:1px solid #1f2937; background:#071126; color:var(--text); margin-top:8px }
    </style>
</head>

<body>

    <div class="auth-wrapper">
        <div class="auth-card card">
            <?php if (!empty($message)): ?>
                <div class="alert error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card .title">
                    <h2 class="h1">Create an account</h2>
                    <p class="small">Register to book services quickly</p>
                </div>

                <form method="POST" class="form" autocomplete="off">

                    <label class="label" for="name">Name</label>
                    <input id="name" type="text" name="name" class="input" required>

                    <label class="label" for="email">Email</label>
                    <input id="email" type="email" name="email" class="input" required>

                    <label class="label" for="password">Password</label>
                    <input id="password" type="password" name="password" class="input" required>

                    <button type="submit" class="btn btn-block" name="btnreg" style="margin-top:12px">✒️ Register</button>
                </form>

                <p style="margin-top:12px;text-align:center">Already have an account? <a href="login.php" style="color:var(--accent-2)">Login here</a></p>
            </div>
        </div>
    </div>

</body>

</html>