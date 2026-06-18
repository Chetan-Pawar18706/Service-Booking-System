<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';

checkUserAccess();
$pageTitle = 'My Bookings';

$conn = getDbConnection();
$messages = getFlashMessages();
$userId = getCurrentUserId();

// Handle cancellation
if (isset($_GET['cancel'])) {
    $bookingId = intval($_GET['cancel']);
    if ($bookingId > 0) {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?");
        if ($stmt) {
            $stmt->bind_param('ii', $bookingId, $userId);
            if ($stmt->execute()) {
                setFlashSuccess('Booking cancelled successfully!');
            } else {
                setFlashError('Error cancelling booking.');
            }
            $stmt->close();
        }
    }
    header('Location: my_bookings.php');
    exit();
}

// Get all bookings
$bookings = [];
$result = $conn->query("
    SELECT b.id, b.booking_date, b.status, b.service_price, 
           s.name AS service_name, s.category,
           p.username AS provider_name, p.phone AS provider_phone
    FROM bookings b
    LEFT JOIN services s ON b.service_id = s.id
    LEFT JOIN users p ON b.provider_id = p.id
    WHERE b.user_id = $userId
    ORDER BY b.booking_date DESC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
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

    <h1 class="h1" style="margin-top: 20px;">My Bookings</h1>

    <div class="card">
        <?php if (empty($bookings)): ?>
            <p class="small">No bookings found. <a href="../../services.php">Book a service now!</a></p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 10px; text-align: left;">ID</th>
                            <th style="padding: 10px; text-align: left;">Service</th>
                            <th style="padding: 10px; text-align: left;">Category</th>
                            <th style="padding: 10px; text-align: left;">Provider</th>
                            <th style="padding: 10px; text-align: left;">Amount</th>
                            <th style="padding: 10px; text-align: left;">Date</th>
                            <th style="padding: 10px; text-align: left;">Status</th>
                            <th style="padding: 10px; text-align: left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 10px;"><?php echo intval($booking['id']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['service_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['category'] ?? '-'); ?></td>
                                <td style="padding: 10px;">
                                    <?php echo htmlspecialchars($booking['provider_name'] ?? '-'); ?>
                                    <?php if ($booking['provider_phone']): ?>
                                        <div class="small"><?php echo htmlspecialchars($booking['provider_phone']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 10px;"><?php echo formatCurrency($booking['service_price'] ?? 0); ?></td>
                                <td style="padding: 10px;"><?php echo formatDate($booking['booking_date']); ?></td>
                                <td style="padding: 10px;"><span class="status-badge status-<?php echo htmlspecialchars($booking['status']); ?>"><?php echo ucfirst(htmlspecialchars($booking['status'])); ?></span></td>
                                <td style="padding: 10px;">
                                    <a href="invoice.php?booking_id=<?php echo intval($booking['id']); ?>" class="btn btn-sm">Invoice</a>
                                    <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                        <a href="my_bookings.php?cancel=<?php echo intval($booking['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this booking?')">Cancel</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php include '../shared/footer.php'; ?>
