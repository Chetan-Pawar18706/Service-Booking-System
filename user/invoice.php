<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';

checkUserAccess();
$pageTitle = 'Invoice';

$conn = getDbConnection();
$userId = getCurrentUserId();
$bookingId = intval($_GET['booking_id'] ?? 0);

if ($bookingId <= 0) {
    header('Location: my_bookings.php');
    exit();
}

// Get booking details with all related info
$booking = getBookingDetails($conn, $bookingId);

// Verify booking belongs to user
if (!$booking || $booking['user_id'] != $userId) {
    header('Location: my_bookings.php');
    exit();
}

// Create or update invoice record
$invoiceNumber = generateInvoiceNumber($bookingId);
$checkInvoice = $conn->prepare("SELECT id FROM invoices WHERE booking_id = ?");
if ($checkInvoice) {
    $checkInvoice->bind_param('i', $bookingId);
    $checkInvoice->execute();
    $invoiceResult = $checkInvoice->get_result();
    $checkInvoice->close();
    
    if (!$invoiceResult || $invoiceResult->num_rows === 0) {
        // Create invoice record
        $amount = $booking['service_price'] ?? 0;
        $dueDate = date('Y-m-d', strtotime('+7 days'));
        $stmt = $conn->prepare("INSERT INTO invoices (booking_id, user_id, provider_id, amount, invoice_number, due_date, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        if ($stmt) {
            $stmt->bind_param('iiiidss', $bookingId, $booking['user_id'], $booking['provider_id'], $amount, $invoiceNumber, $dueDate);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>
<?php include '../shared/header.php'; ?>

    <div style="max-width: 900px; margin: 0 auto; padding: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 class="h1">Invoice</h1>
            <a href="invoice_pdf.php?booking_id=<?php echo intval($bookingId); ?>" class="btn">📥 Download PDF</a>
        </div>

        <div class="card" style="padding: 30px;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid var(--border); padding-bottom: 20px;">
                <div>
                    <div class="h2">Astra Service</div>
                    <div class="small">Professional Service Platform</div>
                </div>
                <div style="text-align: right;">
                    <div class="h3">INVOICE</div>
                    <div class="small">INV-<?php echo htmlspecialchars(date('Ymd')); ?>-<?php echo str_pad($bookingId, 5, '0', STR_PAD_LEFT); ?></div>
                    <div class="small">Date: <?php echo formatDate(date('Y-m-d')); ?></div>
                </div>
            </div>

            <!-- Bill From and To -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                <div>
                    <div class="small" style="font-weight: bold; margin-bottom: 10px;">BILLED TO:</div>
                    <div class="small">
                        <strong><?php echo htmlspecialchars($booking['customer_name'] ?? 'N/A'); ?></strong><br>
                        Email: <?php echo htmlspecialchars($booking['customer_email'] ?? '-'); ?><br>
                        Phone: <?php echo htmlspecialchars($booking['customer_phone'] ?? '-'); ?><br>
                        Address: <?php echo htmlspecialchars($booking['customer_address'] ?? '-'); ?>
                    </div>
                </div>
                <div>
                    <div class="small" style="font-weight: bold; margin-bottom: 10px;">SERVICE PROVIDER:</div>
                    <div class="small">
                        <strong><?php echo htmlspecialchars($booking['provider_name'] ?? 'N/A'); ?></strong><br>
                        Email: <?php echo htmlspecialchars($booking['provider_email'] ?? '-'); ?><br>
                        Phone: <?php echo htmlspecialchars($booking['provider_phone'] ?? '-'); ?>
                    </div>
                </div>
            </div>

            <!-- Service Details Table -->
            <div style="margin-bottom: 30px;">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="background-color: var(--bg-secondary); border: 1px solid var(--border);">
                            <th style="padding: 12px; text-align: left;">Description</th>
                            <th style="padding: 12px; text-align: center;">Category</th>
                            <th style="padding: 12px; text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border: 1px solid var(--border);">
                            <td style="padding: 12px;">
                                <strong><?php echo htmlspecialchars($booking['service_name'] ?? 'Service'); ?></strong><br>
                                <span class="small">Booking ID: <?php echo intval($bookingId); ?></span><br>
                                <span class="small">Booking Date: <?php echo formatDate($booking['booking_date']); ?></span>
                            </td>
                            <td style="padding: 12px; text-align: center;"><?php echo htmlspecialchars($booking['category'] ?? '-'); ?></td>
                            <td style="padding: 12px; text-align: right;"><strong><?php echo formatCurrency($booking['service_price'] ?? 0); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div style="display: flex; justify-content: flex-end; margin-bottom: 30px;">
                <div style="width: 300px;">
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border);">
                        <span>Subtotal:</span>
                        <strong><?php echo formatCurrency($booking['service_price'] ?? 0); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border);">
                        <span>Tax (0%):</span>
                        <strong><?php echo formatCurrency(0); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 2px solid var(--border); margin-bottom: 10px;">
                        <span style="font-weight: bold;">Total:</span>
                        <strong style="font-size: 18px; color: var(--accent-2);"><?php echo formatCurrency($booking['service_price'] ?? 0); ?></strong>
                    </div>
                </div>
            </div>

            <!-- Status and Notes -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <div class="small" style="font-weight: bold;">Status:</div>
                    <span class="status-badge status-<?php echo htmlspecialchars($booking['status']); ?>" style="padding: 8px 12px; display: inline-block;">
                        <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                    </span>
                </div>
                <div>
                    <div class="small" style="font-weight: bold;">Payment Status:</div>
                    <span class="status-badge" style="padding: 8px 12px; display: inline-block;">
                        <?php echo $booking['status'] === 'completed' ? 'Paid' : 'Pending'; ?>
                    </span>
                </div>
            </div>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid var(--border); text-align: center; color: var(--text-secondary);">
                <div class="small">Thank you for using Astra Service. For queries, contact us at support@astraservice.com</div>
                <div class="small" style="margin-top: 10px;">This invoice is a system-generated document.</div>
            </div>
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <a href="my_bookings.php" class="btn secondary">Back to Bookings</a>
        </div>
    </div>

<?php include '../shared/footer.php'; ?>
