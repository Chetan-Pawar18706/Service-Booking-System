<?php
include('db.php');
session_start();

// Get category from URL
$cat = isset($_GET['category']) ? trim($_GET['category']) : '';

// Validate category exists
$validCategory = false;
if ($cat !== '') {
    $catCheck = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
    if ($catCheck) {
        $catCheck->bind_param('s', $cat);
        $catCheck->execute();
        $catRes = $catCheck->get_result();
        $validCategory = $catRes && $catRes->num_rows > 0;
        $catCheck->close();
    }
}

// Handle booking
if (isset($_POST['book_service'])) {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit();
    }
    
    $serviceId = intval($_POST['service_id'] ?? 0);
    $userId = $_SESSION['user_id'];
    
    if ($serviceId > 0) {
        // Get service details
        $serviceStmt = $conn->prepare("SELECT provider_id, price FROM services WHERE id = ?");
        if ($serviceStmt) {
            $serviceStmt->bind_param('i', $serviceId);
            $serviceStmt->execute();
            $serviceRes = $serviceStmt->get_result();
            if ($serviceRes && $serviceRes->num_rows > 0) {
                $serviceData = $serviceRes->fetch_assoc();
                $providerId = $serviceData['provider_id'];
                $price = $serviceData['price'];
                
                // Get user details for customer info
                $userStmt = $conn->prepare("SELECT username, phone, address FROM users WHERE id = ?");
                if ($userStmt) {
                    $userStmt->bind_param('i', $userId);
                    $userStmt->execute();
                    $userData = $userStmt->get_result()->fetch_assoc();
                    
                    // Create booking
                    $bookingStmt = $conn->prepare("INSERT INTO bookings (user_id, service_id, provider_id, customer_name, customer_phone, customer_address, service_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
                    if ($bookingStmt) {
                        $bookingStmt->bind_param('iiiissd', $userId, $serviceId, $providerId, $userData['username'], $userData['phone'], $userData['address'], $price);
                        if ($bookingStmt->execute()) {
                            $_SESSION['flash_success'] = 'Service booked successfully! Check your bookings for details.';
                            header('Location: user/my_bookings.php');
                            exit();
                        } else {
                            $_SESSION['flash_error'] = 'Error booking service.';
                        }
                        $bookingStmt->close();
                    }
                    $userStmt->close();
                }
            }
            $serviceStmt->close();
        }
    }
}

// Get services for the category
$services = [];
if ($validCategory) {
    $stmt = $conn->prepare("SELECT id, name, phone, price, status, provider_id FROM services WHERE category = ? ORDER BY name ASC");
    if ($stmt) {
        $stmt->bind_param('s', $cat);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()) {
            $services[] = $row;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($cat ? $cat . ' Services' : 'Services'); ?> - Astra Service</title>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <a class="brand" href="index.php">Astra Service</a>
        <div class="nav">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <span class="small">Logged in as <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a class="btn secondary btn-sm" href="user/dashboard.php">Dashboard</a>
                <a class="btn secondary btn-sm" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="btn secondary btn-sm" href="login.php">Login</a>
                <a class="btn secondary btn-sm" href="register.php">Register</a>
            <?php endif; ?>
            <a class="btn secondary btn-sm" href="index.php">Back Home</a>
        </div>
    </div>

    <div class="card" style="margin-top: 18px;">
        <h1 class="h1"><?php echo $cat ? htmlspecialchars($cat) . ' Services' : 'All Services'; ?></h1>
        
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert success"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert error"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>

        <div class="grid">
        <?php if (!$validCategory && $cat === ''): ?>
            <p class="small">Please select a category from the home page.</p>
        <?php elseif (!$validCategory && $cat !== ''): ?>
            <div class="alert error">Invalid category selected.</div>
        <?php elseif (empty($services)): ?>
            <p class="small">No services available in this category.</p>
        <?php else: ?>
            <?php foreach ($services as $service): ?>
                <div class="card provider-card">
                    <div>
                        <div class="title"><?php echo htmlspecialchars($service['name']); ?></div>
                        <div class="small">Phone: <?php echo htmlspecialchars($service['phone']); ?></div>
                        <div class="small" style="margin-top: 8px; color: var(--accent-2);">Price: ₹<?php echo number_format($service['price'], 2); ?></div>
                    </div>
                    <div class="card-actions">
                        <?php if ($service['status'] === 'available'): ?>
                            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="service_id" value="<?php echo intval($service['id']); ?>">
                                    <button type="submit" name="book_service" class="btn">Book Now</button>
                                </form>
                            <?php else: ?>
                                <a href="login.php" class="btn">Login to Book</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="status-badge status-booked">Booked</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>

