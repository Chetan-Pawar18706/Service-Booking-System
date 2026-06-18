<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin') {
        header('Location: admindashbord.php');
        exit();
    }

    header('Location: dashbord.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Astra Service</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .entry-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .entry-card {
            width: 100%;
            max-width: 520px;
            text-align: center;
        }

        .entry-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 24px;
        }

        @media (max-width: 520px) {
            .entry-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="entry-page">
        <section class="entry-card card">
            <h1 class="h1">Astra Service</h1>
            <p class="small">Book trusted service providers and manage your bookings from one place.</p>

            <div class="entry-actions">
                <a class="btn btn-block" href="login.php">Login</a>
                <a class="btn secondary btn-block" href="register.php">Register</a>
            </div>
        </section>
    </main>
</body>
</html>
