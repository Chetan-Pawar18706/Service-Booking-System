<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';

checkProviderAccess();

$conn = getDbConnection();
$providerId = getCurrentUserId();
$bookingId = intval($_GET['booking_id'] ?? 0);

if ($bookingId <= 0) {
    http_response_code(404);
    exit();
}

// Get booking details
$booking = getBookingDetails($conn, $bookingId);

// Verify booking belongs to provider
if (!$booking || $booking['provider_id'] != $providerId) {
    http_response_code(403);
    exit();
}

$invoiceNumber = generateInvoiceNumber($bookingId);

// Generate PDF using HTML
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service Invoice - ' . $invoiceNumber . '</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px;
            background: white;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 20px;
        }
        .company-info h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .bill-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        .bill-item h3 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            color: #666;
        }
        .bill-item p {
            font-size: 14px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        thead {
            background-color: #f5f5f5;
        }
        th {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: bold;
        }
        td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .amount-right {
            text-align: right;
        }
        .summary {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .summary-box {
            width: 300px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .summary-row.total {
            border-bottom: 2px solid #333;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: bold;
        }
        .status-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }
        .status-item h3 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 12px;
            background-color: #e8f5e9;
            color: #2e7d32;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
        @media print {
            body { margin: 0; padding: 0; }
            .container { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-info">
                <h1>Astra Service</h1>
                <p>Professional Service Platform</p>
            </div>
            <div class="invoice-info">
                <h2>SERVICE INVOICE</h2>
                <p><strong>' . htmlspecialchars($invoiceNumber) . '</strong></p>
                <p>Date: ' . formatDate(date('Y-m-d')) . '</p>
            </div>
        </div>

        <div class="bill-section">
            <div class="bill-item">
                <h3>Service Provider:</h3>
                <p><strong>' . htmlspecialchars($booking['provider_name'] ?? 'N/A') . '</strong></p>
                <p>Email: ' . htmlspecialchars($booking['provider_email'] ?? '-') . '</p>
                <p>Phone: ' . htmlspecialchars($booking['provider_phone'] ?? '-') . '</p>
            </div>
            <div class="bill-item">
                <h3>Customer:</h3>
                <p><strong>' . htmlspecialchars($booking['customer_name'] ?? 'N/A') . '</strong></p>
                <p>Email: ' . htmlspecialchars($booking['customer_email'] ?? '-') . '</p>
                <p>Phone: ' . htmlspecialchars($booking['customer_phone'] ?? '-') . '</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Service Description</th>
                    <th class="amount-right">Category</th>
                    <th class="amount-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>' . htmlspecialchars($booking['service_name'] ?? 'Service') . '</strong><br>
                        <small>Booking ID: ' . intval($bookingId) . '</small><br>
                        <small>Service Date: ' . formatDate($booking['booking_date']) . '</small>
                    </td>
                    <td class="amount-right">' . htmlspecialchars($booking['category'] ?? '-') . '</td>
                    <td class="amount-right"><strong>' . formatCurrency($booking['service_price'] ?? 0) . '</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-box">
                <div class="summary-row">
                    <span>Service Fee:</span>
                    <strong>' . formatCurrency($booking['service_price'] ?? 0) . '</strong>
                </div>
                <div class="summary-row">
                    <span>Platform Fee (0%):</span>
                    <strong>' . formatCurrency(0) . '</strong>
                </div>
                <div class="summary-row total">
                    <span>Total Amount:</span>
                    <strong style="font-size: 18px;">' . formatCurrency($booking['service_price'] ?? 0) . '</strong>
                </div>
            </div>
        </div>

        <div class="status-row">
            <div class="status-item">
                <h3>Booking Status:</h3>
                <div class="status-badge">' . ucfirst(htmlspecialchars($booking['status'])) . '</div>
            </div>
            <div class="status-item">
                <h3>Payment Status:</h3>
                <div class="status-badge">' . ($booking['status'] === 'completed' ? 'Completed' : 'Pending') . '</div>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for providing professional services through Astra Service.</p>
            <p style="margin-top: 10px;">This invoice is a system-generated document.</p>
        </div>
    </div>
</body>
</html>
';

// Set headers for HTML download (can be printed to PDF)
header('Content-Type: text/html; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $invoiceNumber . '.html"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo $html;
exit();
?>
