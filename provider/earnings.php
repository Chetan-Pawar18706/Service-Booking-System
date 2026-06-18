<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';

checkProviderAccess();
$pageTitle = 'My Earnings';

$conn = getDbConnection();
$providerId = getCurrentUserId();

// Get date range for filter
$fromDate = trim($_GET['from_date'] ?? date('Y-m-01'));
$toDate = trim($_GET['to_date'] ?? date('Y-m-d'));

// Validate dates
if (!strtotime($fromDate) || !strtotime($toDate)) {
    $fromDate = date('Y-m-01');
    $toDate = date('Y-m-d');
}

// Get earnings for date range
$earnings = calculateProviderEarnings($conn, $providerId, $fromDate, $toDate);

// Get all stats
$allStats = getProviderStats($conn, $providerId);

// Get completed bookings with details
$completedBookings = [];
$result = $conn->query("
    SELECT b.id, b.booking_date, b.completion_date, b.service_price, 
           s.name AS service_name, s.category,
           u.username AS customer_name
    FROM bookings b
    LEFT JOIN services s ON b.service_id = s.id
    LEFT JOIN users u ON b.user_id = u.id
    WHERE b.provider_id = $providerId AND b.status = 'completed' AND DATE(b.completion_date) BETWEEN '$fromDate' AND '$toDate'
    ORDER BY b.completion_date DESC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $completedBookings[] = $row;
    }
}

// Calculate monthly data for chart
$monthlyData = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m-01', strtotime("-$i months"));
    $nextMonth = date('Y-m-01', strtotime('-' . ($i - 1) . ' months'));
    $monthStats = calculateProviderEarnings($conn, $providerId, $month, $nextMonth);
    $monthlyData[date('M', strtotime($month))] = $monthStats['total_earnings'];
}
?>
<?php include '../shared/header.php'; ?>

    <h1 class="h1" style="margin-top: 20px;">My Earnings</h1>

    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--accent-1);"><?php echo formatCurrency($allStats['total_earnings']); ?></div>
            <div class="small">Total Lifetime Earnings</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--success);"><?php echo $allStats['completed_bookings']; ?></div>
            <div class="small">Completed Services</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--accent-2);"><?php echo formatCurrency($earnings['total_earnings']); ?></div>
            <div class="small">Selected Period Earnings</div>
        </div>
        <div class="card" style="text-align: center; padding: 20px;">
            <div class="h3" style="margin: 0; color: var(--warning);"><?php echo $earnings['completed_bookings']; ?></div>
            <div class="small">Services This Period</div>
        </div>
    </div>

    <div class="card">
        <h2 class="h2">Filter by Date Range</h2>
        <form method="GET" class="form" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; align-items: flex-end;">
            <div>
                <label class="label">From Date</label>
                <input type="date" name="from_date" value="<?php echo htmlspecialchars($fromDate); ?>">
            </div>
            <div>
                <label class="label">To Date</label>
                <input type="date" name="to_date" value="<?php echo htmlspecialchars($toDate); ?>">
            </div>
            <div>
                <button type="submit" class="btn">Filter</button>
                <a href="earnings.php" class="btn secondary btn-sm" style="margin-left: 5px;">Reset</a>
            </div>
        </form>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h2 class="h2">Completed Services & Invoices</h2>
        <?php if (empty($completedBookings)): ?>
            <p class="small">No completed services in the selected period.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 10px; text-align: left;">ID</th>
                            <th style="padding: 10px; text-align: left;">Customer</th>
                            <th style="padding: 10px; text-align: left;">Service</th>
                            <th style="padding: 10px; text-align: left;">Amount</th>
                            <th style="padding: 10px; text-align: left;">Booking Date</th>
                            <th style="padding: 10px; text-align: left;">Completed</th>
                            <th style="padding: 10px; text-align: left;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completedBookings as $booking): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 10px;"><?php echo intval($booking['id']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['customer_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;">
                                    <?php echo htmlspecialchars($booking['service_name'] ?? '-'); ?>
                                    <div class="small"><?php echo htmlspecialchars($booking['category'] ?? '-'); ?></div>
                                </td>
                                <td style="padding: 10px;"><strong><?php echo formatCurrency($booking['service_price'] ?? 0); ?></strong></td>
                                <td style="padding: 10px;"><?php echo formatDate($booking['booking_date']); ?></td>
                                <td style="padding: 10px;"><?php echo formatDate($booking['completion_date']); ?></td>
                                <td style="padding: 10px;">
                                    <a href="invoice.php?booking_id=<?php echo intval($booking['id']); ?>" class="btn btn-sm">Invoice</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 15px; padding: 15px; background-color: var(--bg-secondary); border-radius: 4px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <strong>Total Earnings (Selected Period):</strong>
                    <div style="font-size: 24px; color: var(--success);"><?php echo formatCurrency($earnings['total_earnings']); ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>

<?php include '../shared/footer.php'; ?>
