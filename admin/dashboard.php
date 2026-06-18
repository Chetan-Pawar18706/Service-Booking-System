<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';

checkAdminAccess();
$pageTitle = 'Admin Dashboard';

$conn = getDbConnection();
$messages = getFlashMessages();

// Get statistics
$stats = [
    'total_users' => 0,
    'total_providers' => 0,
    'total_services' => 0,
    'total_bookings' => 0,
    'total_revenue' => 0,
    'pending_bookings' => 0
];

// Count users
$result = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'user'");
if ($result) {
    $row = $result->fetch_assoc();
    $stats['total_users'] = $row['count'];
}

// Count providers
$result = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'provider'");
if ($result) {
    $row = $result->fetch_assoc();
    $stats['total_providers'] = $row['count'];
}

// Count services
$result = $conn->query("SELECT COUNT(*) AS count FROM services");
if ($result) {
    $row = $result->fetch_assoc();
    $stats['total_services'] = $row['count'];
}

// Count bookings
$result = $conn->query("SELECT COUNT(*) AS count FROM bookings");
if ($result) {
    $row = $result->fetch_assoc();
    $stats['total_bookings'] = $row['count'];
}

// Calculate revenue
$result = $conn->query("SELECT COALESCE(SUM(service_price), 0) AS total FROM bookings WHERE status = 'completed'");
if ($result) {
    $row = $result->fetch_assoc();
    $stats['total_revenue'] = $row['total'] ?? 0;
}

// Count pending bookings
$result = $conn->query("SELECT COUNT(*) AS count FROM bookings WHERE status IN ('pending', 'confirmed')");
if ($result) {
    $row = $result->fetch_assoc();
    $stats['pending_bookings'] = $row['count'];
}

// Get recent bookings
$recentBookings = [];
$result = $conn->query("
    SELECT b.id, b.booking_date, b.status, s.name AS service_name, u.username AS customer_name, p.username AS provider_name
    FROM bookings b
    LEFT JOIN services s ON b.service_id = s.id
    LEFT JOIN users u ON b.user_id = u.id
    LEFT JOIN users p ON b.provider_id = p.id
    ORDER BY b.booking_date DESC
    LIMIT 10
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentBookings[] = $row;
    }
}
?>
<?php include '../shared/header.php'; ?>

    <div class="breadcrumb" style="margin-bottom: 20px;">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="manage_users.php">Users</a>
        <a href="manage_bookings.php">Bookings</a>
        <a href="manage_categories.php">Categories</a>
        <a href="manage_services.php">Services</a>
    </div>

    <?php if (!empty($messages['success'])): ?>
        <div class="alert success"><?php echo htmlspecialchars($messages['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($messages['error'])): ?>
        <div class="alert error"><?php echo htmlspecialchars($messages['error']); ?></div>
    <?php endif; ?>

    <h1 class="h1" style="margin-top: 20px; margin-bottom: 20px;">Admin Dashboard</h1>

    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--accent-2);"><?php echo $stats['total_users']; ?></div>
            <div class="small">Total Customers</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--accent-3);"><?php echo $stats['total_providers']; ?></div>
            <div class="small">Service Providers</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--accent-1);"><?php echo $stats['total_services']; ?></div>
            <div class="small">Total Services</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--success);"><?php echo $stats['total_bookings']; ?></div>
            <div class="small">Total Bookings</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--warning);"><?php echo formatCurrency($stats['total_revenue']); ?></div>
            <div class="small">Total Revenue</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--error);"><?php echo $stats['pending_bookings']; ?></div>
            <div class="small">Pending Bookings</div>
        </div>
    </div>

    <div class="card">
        <h2 class="h2">Recent Bookings</h2>
        <?php if (empty($recentBookings)): ?>
            <p class="small">No bookings found.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 10px; text-align: left;">ID</th>
                            <th style="padding: 10px; text-align: left;">Customer</th>
                            <th style="padding: 10px; text-align: left;">Service</th>
                            <th style="padding: 10px; text-align: left;">Provider</th>
                            <th style="padding: 10px; text-align: left;">Date</th>
                            <th style="padding: 10px; text-align: left;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBookings as $booking): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['id']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['service_name'] ?? 'N/A'); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['provider_name'] ?? 'N/A'); ?></td>
                                <td style="padding: 10px;"><?php echo formatDate($booking['booking_date']); ?></td>
                                <td style="padding: 10px;"><span class="status-badge status-<?php echo htmlspecialchars($booking['status']); ?>"><?php echo ucfirst(htmlspecialchars($booking['status'])); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 15px;">
                <a href="manage_bookings.php" class="btn">View All Bookings</a>
            </div>
        <?php endif; ?>
    </div>

<?php include '../shared/footer.php'; ?>
