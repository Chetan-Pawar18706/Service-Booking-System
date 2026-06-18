<?php
/**
 * Common Header for all pages
 */
if (!isset($_SESSION)) {
    session_start();
}

// Determine the relative path based on current depth
$depth = count(array_filter(explode('/', dirname($_SERVER['SCRIPT_NAME']))));
$basePath = str_repeat('../', $depth - 1);

include_once dirname(__DIR__) . '/db.php';
include_once __DIR__ . '/auth.php';
include_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle ?? 'Astra Service'; ?></title>
    <link rel="icon" type="image/svg+xml" href="<?php echo $basePath; ?>assets/img/favicon.svg">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <a class="brand" href="<?php echo $basePath; ?>index.php">Astra Service</a>
            <div class="nav">
                <span class="small">Welcome, <?php echo htmlspecialchars(getCurrentUsername()); ?> (<?php echo ucfirst(getCurrentUserRole()); ?>)</span>
                <?php 
                $role = getCurrentUserRole();
                if ($role === 'admin'): ?>
                    <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>admin/dashboard.php">Dashboard</a>
                    <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>admin/manage_users.php">Users</a>
                    <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>admin/manage_bookings.php">Bookings</a>
                <?php elseif ($role === 'provider'): ?>
                    <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>provider/dashboard.php">Dashboard</a>
                    <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>provider/my_bookings.php">Bookings</a>
                    <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>provider/earnings.php">Earnings</a>
                <?php elseif ($role === 'user'): ?>
                    <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>user/dashboard.php">Dashboard</a>
                    <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>user/my_bookings.php">My Bookings</a>
                    <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>services.php">Book Service</a>
                <?php endif; ?>
                <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>index.php">Home</a>
                <a class="btn secondary btn-sm" href="<?php echo $basePath; ?>logout.php">Logout</a>
            </div>
        </div>
