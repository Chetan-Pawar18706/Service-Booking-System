<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $role = $_SESSION['role'] ?? 'user';
    switch ($role) {
        case 'admin':
            header('Location: admin/dashboard.php');
            break;
        case 'provider':
            header('Location: provider/dashboard.php');
            break;
        case 'user':
        default:
            header('Location: user/dashboard.php');
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Astra Service - Book Trusted Local Services</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="entry-page">
        <section class="entry-hero">
            <div class="hero-copy">
                <a class="brand-full" href="index.php">Astra Service</a>
                <div class="eyebrow">Fast booking for home service providers</div>
                <h1 class="hero-title">Book local help <span>without the hassle.</span></h1>
                <p class="hero-lead">Find available plumbers, electricians, AC repair providers, cleaners, and more. Astra Service keeps provider status, bookings, and cancellations simple for users and admins.</p>

                <div class="hero-actions">
                    <a class="btn btn-lg" href="register.php">Create Account</a>
                    <a class="btn secondary btn-lg" href="login.php">Login</a>
                </div>

                <div class="hero-stats">
                    <div class="stat-pill">
                        <strong>24/7</strong>
                        <span class="small">Booking access</span>
                    </div>
                    <div class="stat-pill">
                        <strong>Live</strong>
                        <span class="small">Provider status</span>
                    </div>
                    <div class="stat-pill">
                        <strong>Admin</strong>
                        <span class="small">Service control</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="visual-panel card">
                    <img src="assets/img/service-visual.svg" alt="Astra Service booking dashboard preview">
                </div>
                <div class="feature-strip">
                    <div class="feature-item">
                        <strong>Quick Discovery</strong>
                        <span class="small">Browse categories and find available providers.</span>
                    </div>
                    <div class="feature-item">
                        <strong>Simple Booking</strong>
                        <span class="small">Confirm name, phone, address, and reserve.</span>
                    </div>
                    <div class="feature-item">
                        <strong>Easy Admin</strong>
                        <span class="small">Add, remove, and track service providers.</span>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
