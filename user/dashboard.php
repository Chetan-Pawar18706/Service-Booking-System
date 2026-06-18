<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';

checkUserAccess();
$pageTitle = 'User Dashboard';

$conn = getDbConnection();
$messages = getFlashMessages();
$userId = getCurrentUserId();

// Get user stats
$stats = getUserStats($conn, $userId);

// Get recent bookings
$recentBookings = [];
$result = $conn->query("
    SELECT b.id, b.booking_date, b.status, b.service_price, 
           s.name AS service_name, s.category,
           p.username AS provider_name
    FROM bookings b
    LEFT JOIN services s ON b.service_id = s.id
    LEFT JOIN users p ON b.provider_id = p.id
    WHERE b.user_id = $userId
    ORDER BY b.booking_date DESC
    LIMIT 5
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recentBookings[] = $row;
    }
}

// Get categories for quick booking
$categories = [];
$category_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($category_result) {
    while ($category_row = $category_result->fetch_assoc()) {
        $categories[] = $category_row;
    }
}
?>
<?php include '../shared/header.php'; ?>

    <?php if (!empty($messages['success'])): ?>
        <div class="alert success"><?php echo htmlspecialchars($messages['success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($messages['error'])): ?>
        <div class="alert error"><?php echo htmlspecialchars($messages['error']); ?></div>
    <?php endif; ?>

    <h1 class="h1" style="margin-top: 20px;">Welcome back!</h1>

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
            <div class="h3" style="margin: 0; color: var(--accent-1);"><?php echo formatCurrency($stats['total_spent']); ?></div>
            <div class="small">Total Spent</div>
        </div>
    </div>

    <div class="card">
        <h2 class="h2">Quick Book a Service</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
            <?php foreach ($categories as $category): ?>
                <a href="../../services.php?category=<?php echo urlencode(htmlspecialchars($category['name'])); ?>" class="btn" style="text-align: center;">
                    <?php echo htmlspecialchars($category['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <h2 class="h2">Your Recent Bookings</h2>
        <?php if (empty($recentBookings)): ?>
            <p class="small">No bookings yet. <a href="../../services.php">Book a service now!</a></p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 10px; text-align: left;">Service</th>
                            <th style="padding: 10px; text-align: left;">Provider</th>
                            <th style="padding: 10px; text-align: left;">Amount</th>
                            <th style="padding: 10px; text-align: left;">Date</th>
                            <th style="padding: 10px; text-align: left;">Status</th>
                            <th style="padding: 10px; text-align: left;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBookings as $booking): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['service_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['provider_name'] ?? '-'); ?></td>
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
            <div style="margin-top: 15px;">
                <a href="my_bookings.php" class="btn">View All Bookings</a>
            </div>
        <?php endif; ?>
    </div>

<?php include '../shared/footer.php'; ?>
