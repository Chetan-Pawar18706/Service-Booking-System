<?php
// Booking flow adapted to this project's schema:
// - uses `services` table (id, name, phone, status)
// - creates a row in `bookings` (user_id, service_id)
// - sets `services.status` to 'booked'

include 'db.php';
session_start();

// Require login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Booking</title><link rel='stylesheet' href='assets/css/style.css'></head><body><div class='container'><div class='alert error'>No service selected.</div></div></body></html>";
    exit;
}

$service_id = intval($_GET['id']);

// Fetch the service
$stmt = $conn->prepare("SELECT id, name, phone, status FROM services WHERE id = ?");
if (!$stmt) {
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Booking</title><link rel='stylesheet' href='assets/css/style.css'></head><body><div class='container'><div class='alert error'>Database error: could not prepare statement.</div></div></body></html>";
    exit;
}
$stmt->bind_param('i', $service_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows == 0) {
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Booking</title><link rel='stylesheet' href='assets/css/style.css'></head><body><div class='container'><div class='alert error'>Service not found.</div><a href='dashbord.php' class='btn'>Back to Dashboard</a></div></body></html>";
    exit;
}
$service = $result->fetch_assoc();
$stmt->close();

// If service already booked
if ($service['status'] !== 'available') {
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Booking</title><link rel='stylesheet' href='assets/css/style.css'></head><body><div class='container'><div class='card' style='max-width:600px;margin:40px auto'><h2 class='h1'>⚠️ Service Unavailable</h2><p class='small'>This service is currently booked. Please select another service.</p><a href='dashbord.php' class='btn'>Back to Dashboard</a></div></div></body></html>";
    exit;
}

// Get current user id
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$user_id = null;
if ($username !== '') {
    $ustmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
    if ($ustmt) {
        $ustmt->bind_param('ss', $username, $username);
        $ustmt->execute();
        $ures = $ustmt->get_result();
        if ($ures && $ures->num_rows > 0) {
            $urow = $ures->fetch_assoc();
            $user_id = (int)$urow['id'];
        }
        $ustmt->close();
    }
}

if (!$user_id) {
    echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Booking</title><link rel='stylesheet' href='assets/css/style.css'></head><body><div class='container'><div class='alert error'>User record not found. Please login again.</div><a href='login.php' class='btn'>Back to Login</a></div></body></html>";
    exit;
}

// If form submitted -> create booking
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $customer_address = trim($_POST['customer_address'] ?? '');
    $error = '';

    if (empty($customer_name) || empty($customer_phone) || empty($customer_address)) {
        $error = "Please fill in all customer details.";
    } else {
        // Create booking entry with customer details
        $bstmt = $conn->prepare("INSERT INTO bookings (user_id, service_id, customer_name, customer_phone, customer_address) VALUES (?, ?, ?, ?, ?)");
        if (!$bstmt) {
            $error = "Database error: could not prepare booking statement.";
        } else {
            $bstmt->bind_param('iisss', $user_id, $service_id, $customer_name, $customer_phone, $customer_address);
            $ok = $bstmt->execute();
            $bstmt->close();

            if ($ok) {
                // mark service as booked
                $ust = $conn->prepare("UPDATE services SET status = 'booked' WHERE id = ?");
                if ($ust) {
                    $ust->bind_param('i', $service_id);
                    $ust->execute();
                    $ust->close();
                }

                $_SESSION['flash_success'] = 'Booking successful. Your booking has been confirmed.';
                header('Location: my_booking.php');
                exit();
            } else {
                $error = "Booking failed: " . htmlspecialchars($conn->error);
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirm Booking</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .booking-details {
            background: rgba(37, 99, 235, 0.05);
            border-left: 4px solid var(--accent);
            padding: 16px;
            border-radius: 6px;
            margin: 16px 0;
        }
        .booking-details .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
        }
        .booking-details .label {
            color: var(--muted);
            font-weight: 500;
        }
        .booking-details .value {
            font-weight: 600;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 18px;
        }
        @media(max-width:600px) {
            .form-actions {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">Astra Service</div>
        <div class="nav"><a href="dashbord.php">← Back to Dashboard</a></div>
    </div>

    <div class="card" style="max-width:600px;margin:24px auto">
        <h2 class="h1">📅 Confirm Booking</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="booking-details">
            <div class="detail-row">
                <span class="label">Service Provider:</span>
                <span class="value"><?php echo htmlspecialchars($service['name']); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Contact:</span>
                <span class="value">📞 <?php echo htmlspecialchars($service['phone']); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Status:</span>
                <span class="value" style="color:var(--success)">✓ Available</span>
            </div>
        </div>

        <form method="post" class="form">
            <p class="small" style="margin:12px 0;color:var(--muted)">Please fill in your details and confirm your booking:</p>

            <label class="label" for="customer_name">Your Name</label>
            <input type="text" id="customer_name" name="customer_name" placeholder="Full name" required>

            <label class="label" for="customer_phone">Phone Number</label>
            <input type="tel" id="customer_phone" name="customer_phone" placeholder="10-digit phone" required>

            <label class="label" for="customer_address">Address</label>
            <input type="text" id="customer_address" name="customer_address" placeholder="Street address and location" required>

            <div class="form-actions">
                <button type="submit" class="btn btn-lg btn-block">✓ Confirm Booking</button>
                <a class="btn btn-lg secondary btn-block" href="dashbord.php">✕ Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
