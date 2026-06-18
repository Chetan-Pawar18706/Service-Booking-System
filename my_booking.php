<?php
include('db.php');
session_start();

$success_msg = $_SESSION['flash_success'] ?? '';
$error_msg = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Ensure user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Determine user id: prefer stored session `user_id`, fallback to lookup by username/email
$uid = null;
if (!empty($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
} elseif (!empty($_SESSION['username'])) {
    $uname = $_SESSION['username'];
    $ustmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
    if ($ustmt) {
        $ustmt->bind_param('ss', $uname, $uname);
        $ustmt->execute();
        $ures = $ustmt->get_result();
        if ($ures && $ures->num_rows > 0) {
            $urow = $ures->fetch_assoc();
            $uid = (int)$urow['id'];
        }
        $ustmt->close();
    }
}

if (!$uid) {
    // user id not available — ask user to re-login
    echo "<p>User not found. Please <a href=\"login.php\">login</a> again.</p>";
    exit();
}

// Fetch bookings for this user using prepared statement
$stmt = $conn->prepare("SELECT bookings.id AS bid, bookings.customer_name, bookings.customer_phone, bookings.customer_address, bookings.booking_date, services.name, services.phone FROM bookings JOIN services ON bookings.service_id = services.id WHERE bookings.user_id = ? ORDER BY bookings.booking_date DESC");
if (!$stmt) {
    echo "<p>Database error: could not prepare statement.</p>";
    exit();
}
$stmt->bind_param('i', $uid);
$stmt->execute();
$res = $stmt->get_result();

if (!$res || $res->num_rows === 0) {
    $stmt->close();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>My Bookings</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
    <div class="container">
        <div class="header">
            <div class="logo">My Bookings</div>
            <div class="nav"><a href="dashbord.php">Back to Dashboard</a></div>
        </div>
        <?php if (!empty($success_msg)): ?>
            <div class="alert success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>
        <div class="card" style="margin-top:18px">
            <p class="small">You have no bookings.</p>
            <a href="dashbord.php" class="btn" style="margin-top:12px">Browse Services</a>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit();
}

$bookings = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .booking-ref { color: var(--muted); font-size: .9rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">My Bookings</div>
        <div class="nav"><a href="dashbord.php">Back to Dashboard</a></div>
    </div>

    <?php if (!empty($success_msg)): ?>
        <div class="alert success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="alert error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:18px;margin-top:18px">
        <?php foreach($bookings as $row): ?>
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:start">
                    <div class="title"><?php echo htmlspecialchars($row['name']); ?></div>
                    <span class="status-badge status-available">Active</span>
                </div>
                <div class="small booking-ref" style="margin-top:8px;margin-bottom:12px">Booking ID: #<?php echo intval($row['bid']); ?></div>

                <div style="background:rgba(37,99,235,0.05);padding:12px;border-radius:6px;margin:12px 0">
                    <p style="margin:0;font-size:.9rem;color:var(--muted)"><strong>Your Details:</strong></p>
                    <p style="margin:6px 0;font-size:.9rem">📝 <?php echo htmlspecialchars($row['customer_name']); ?></p>
                    <p style="margin:6px 0;font-size:.9rem">📞 <?php echo htmlspecialchars($row['customer_phone']); ?></p>
                    <p style="margin:6px 0;font-size:.9rem">📍 <?php echo htmlspecialchars($row['customer_address']); ?></p>
                </div>

                <div style="background:rgba(6,182,212,0.05);padding:12px;border-radius:6px;margin:12px 0">
                    <p style="margin:0;font-size:.9rem;color:var(--muted)"><strong>Service Provider:</strong></p>
                    <p style="margin:6px 0;font-size:.9rem">💼 <?php echo htmlspecialchars($row['name']); ?></p>
                    <p style="margin:6px 0;font-size:.9rem">📞 <?php echo htmlspecialchars($row['phone']); ?></p>
                </div>

                <p style="margin:8px 0;font-size:.85rem;color:var(--muted)">Booked: <?php echo date('M d, Y H:i', strtotime($row['booking_date'])); ?></p>

                <div class="card-actions">
                    <a href="unbook.php?id=<?php echo intval($row['bid']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this booking?')">✕ Cancel Booking</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
</body>
</html>

<?php
