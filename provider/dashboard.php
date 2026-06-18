<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';
include_once '../shared/functions.php';

checkProviderAccess();
$pageTitle = 'Provider Dashboard';

$conn = getDbConnection();
$messages = getFlashMessages();
$providerId = getCurrentUserId();

// Get provider stats
$stats = getProviderStats($conn, $providerId);

// Get recent bookings
$recentBookings = [];
$result = $conn->query("
    SELECT b.id, b.booking_date, b.status, b.service_price, 
           s.name AS service_name, s.category,
           u.username AS customer_name
    FROM bookings b
    LEFT JOIN services s ON b.service_id = s.id
    LEFT JOIN users u ON b.user_id = u.id
    WHERE b.provider_id = $providerId
    ORDER BY b.booking_date DESC
    LIMIT 5
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentBookings[] = $row;
    }
}

// Get earnings this month
$currentMonth = date('Y-m-01');
$nextMonth = date('Y-m-01', strtotime('+1 month'));
$monthlyEarnings = calculateProviderEarnings($conn, $providerId, $currentMonth, $nextMonth);
?>
<?php include '../shared/header.php'; ?>

    <div class="breadcrumb" style="margin-bottom: 20px;">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="my_bookings.php">My Bookings</a>
        <a href="earnings.php">Earnings</a>
    </div>

    <?php if (!empty($messages['success'])): ?>
        <div class="alert success"><?php echo htmlspecialchars($messages['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($messages['error'])): ?>
        <div class="alert error"><?php echo htmlspecialchars($messages['error']); ?></div>
    <?php endif; ?>

    <h1 class="h1" style="margin-top: 20px;">Provider Dashboard</h1>

    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--accent-2);"><?php echo $stats['total_bookings']; ?></div>
            <div class="small">Total Bookings</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--success);"><?php echo $stats['completed_bookings']; ?></div>
            <div class="small">Completed</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--warning);"><?php echo $stats['pending_bookings']; ?></div>
            <div class="small">Pending</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--accent-1);"><?php echo formatCurrency($stats['total_earnings']); ?></div>
            <div class="small">Total Earnings</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--accent-3);"><?php echo formatCurrency($monthlyEarnings['total_earnings']); ?></div>
            <div class="small">This Month</div>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="h2">Recent Bookings</h2>
            <a href="my_bookings.php" class="btn btn-sm">View All</a>
        </div>
        <?php if (empty($recentBookings)): ?>
            <p class="small">No bookings yet. Promote your services to get started!</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 10px; text-align: left;">Customer</th>
                            <th style="padding: 10px; text-align: left;">Service</th>
                            <th style="padding: 10px; text-align: left;">Amount</th>
                            <th style="padding: 10px; text-align: left;">Date</th>
                            <th style="padding: 10px; text-align: left;">Status</th>
                            <th style="padding: 10px; text-align: left;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBookings as $booking): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['customer_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['service_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo formatCurrency($booking['service_price'] ?? 0); ?></td>
                                <td style="padding: 10px;"><?php echo formatDate($booking['booking_date']); ?></td>
                                <td style="padding: 10px;"><span class="status-badge status-<?php echo htmlspecialchars($booking['status']); ?>"><?php echo ucfirst(htmlspecialchars($booking['status'])); ?></span></td>
                                <td style="padding: 10px;">
                                    <a href="invoice.php?booking_id=<?php echo intval($booking['id']); ?>" class="btn btn-sm">Invoice</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
        <div class="card">
            <h3 class="h3">Quick Links</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 10px;"><a href="my_bookings.php" class="btn btn-sm secondary">📋 Manage Bookings</a></li>
                <li style="margin-bottom: 10px;"><a href="earnings.php" class="btn btn-sm secondary">💰 View Earnings</a></li>
                <li><a href="../../admin/dashboard.php" class="btn btn-sm secondary">⚙️ Edit Profile (TODO)</a></li>
            </ul>
        </div>
        <div class="card">
            <h3 class="h3">Performance</h3>
            <div style="padding: 10px 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Completion Rate:</span>
                    <strong><?php echo $stats['total_bookings'] > 0 ? round(($stats['completed_bookings'] / $stats['total_bookings']) * 100, 1) . '%' : '0%'; ?></strong>
                </div>
                <div style="width: 100%; background-color: var(--bg-secondary); border-radius: 4px; height: 8px; overflow: hidden;">
                    <div style="background-color: var(--success); height: 100%; width: <?php echo $stats['total_bookings'] > 0 ? round(($stats['completed_bookings'] / $stats['total_bookings']) * 100, 1) : 0; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>

<?php include '../shared/footer.php'; ?>
