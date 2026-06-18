<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';

checkAdminAccess();
$pageTitle = 'Manage Bookings';

$conn = getDbConnection();
$messages = getFlashMessages();

// Handle booking status update
if (isset($_POST['btn_update_status'])) {
    $bookingId = intval($_POST['booking_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    
    if ($bookingId > 0 && in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
        $completionDate = null;
        if ($status === 'completed') {
            $completionDate = date('Y-m-d H:i:s');
        }
        
        $stmt = $conn->prepare("UPDATE bookings SET status = ?, completion_date = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('ssi', $status, $completionDate, $bookingId);
            if ($stmt->execute()) {
                setFlashSuccess('Booking status updated successfully!');
            } else {
                setFlashError('Error updating booking.');
            }
            $stmt->close();
        }
    }
    header('Location: manage_bookings.php');
    exit();
}

if (isset($_GET['delete'])) {
    $bookingId = intval($_GET['delete']);
    if ($bookingId > 0) {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $bookingId);
            if ($stmt->execute()) {
                setFlashSuccess('Booking deleted successfully!');
            } else {
                setFlashError('Error deleting booking.');
            }
            $stmt->close();
        }
    }
    header('Location: manage_bookings.php');
    exit();
}

// Get all bookings
$bookings = [];
$result = $conn->query("
    SELECT b.id, b.booking_date, b.status, b.service_price, 
           s.name AS service_name, s.category,
           u.username AS customer_name, 
           p.username AS provider_name
    FROM bookings b
    LEFT JOIN services s ON b.service_id = s.id
    LEFT JOIN users u ON b.user_id = u.id
    LEFT JOIN users p ON b.provider_id = p.id
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

    <h1 class="h1" style="margin-top: 20px;">Manage Bookings</h1>

    <div class="card">
        <?php if (empty($bookings)): ?>
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
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['customer_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['service_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($booking['provider_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo formatCurrency($booking['service_price'] ?? 0); ?></td>
                                <td style="padding: 10px;"><?php echo formatDate($booking['booking_date']); ?></td>
                                <td style="padding: 10px;"><span class="status-badge status-<?php echo htmlspecialchars($booking['status']); ?>"><?php echo ucfirst(htmlspecialchars($booking['status'])); ?></span></td>
                                <td style="padding: 10px;">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo intval($booking['id']); ?>">
                                        <select name="status" style="padding: 5px; font-size: 12px; margin-right: 5px;">
                                            <option value="<?php echo htmlspecialchars($booking['status']); ?>" selected><?php echo ucfirst(htmlspecialchars($booking['status'])); ?></option>
                                            <option value="pending">Pending</option>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                        <button type="submit" name="btn_update_status" class="btn btn-sm">Update</button>
                                    </form>
                                    <a href="manage_bookings.php?delete=<?php echo intval($booking['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this booking?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php include '../shared/footer.php'; ?>
