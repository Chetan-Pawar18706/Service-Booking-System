<?php
/**
 * Shared Helper Functions
 */

function generateInvoiceNumber($bookingId) {
    return 'INV-' . date('Ymd') . '-' . str_pad($bookingId, 5, '0', STR_PAD_LEFT);
}

function formatCurrency($amount) {
    return '₹ ' . number_format($amount, 2);
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function getServiceProviderInfo($conn, $providerId) {
    $stmt = $conn->prepare("SELECT id, username, email, phone, address FROM users WHERE id = ? AND role = 'provider'");
    if ($stmt) {
        $stmt->bind_param('i', $providerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
        $stmt->close();
        return $data;
    }
    return null;
}

function getCustomerInfo($conn, $userId) {
    $stmt = $conn->prepare("SELECT id, username, email, phone, address FROM users WHERE id = ? AND role = 'user'");
    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
        $stmt->close();
        return $data;
    }
    return null;
}

function getBookingDetails($conn, $bookingId) {
    $stmt = $conn->prepare("
        SELECT b.*, 
               s.name AS service_name, 
               s.category,
               u.username AS customer_name, 
               u.email AS customer_email,
               u.phone AS customer_phone,
               u.address AS customer_address,
               p.username AS provider_name, 
               p.email AS provider_email,
               p.phone AS provider_phone
        FROM bookings b
        LEFT JOIN services s ON b.service_id = s.id
        LEFT JOIN users u ON b.user_id = u.id
        LEFT JOIN users p ON b.provider_id = p.id
        WHERE b.id = ?
    ");
    if ($stmt) {
        $stmt->bind_param('i', $bookingId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;
        $stmt->close();
        return $data;
    }
    return null;
}

function calculateProviderEarnings($conn, $providerId, $fromDate = null, $toDate = null) {
    $query = "SELECT COALESCE(SUM(service_price), 0) AS total_earnings, COUNT(*) AS completed_bookings FROM bookings WHERE provider_id = ? AND status = 'completed'";
    
    if ($fromDate && $toDate) {
        $query .= " AND completion_date BETWEEN ? AND ?";
    }
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        if ($fromDate && $toDate) {
            $stmt->bind_param('iss', $providerId, $fromDate, $toDate);
        } else {
            $stmt->bind_param('i', $providerId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result && $result->num_rows > 0 ? $result->fetch_assoc() : ['total_earnings' => 0, 'completed_bookings' => 0];
        $stmt->close();
        return $data;
    }
    return ['total_earnings' => 0, 'completed_bookings' => 0];
}

function getProviderStats($conn, $providerId) {
    $stats = [
        'total_bookings' => 0,
        'completed_bookings' => 0,
        'pending_bookings' => 0,
        'total_earnings' => 0,
        'rating' => 0
    ];
    
    // Total bookings
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM bookings WHERE provider_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $providerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['total_bookings'] = $row['count'];
        $stmt->close();
    }
    
    // Completed bookings
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM bookings WHERE provider_id = ? AND status = 'completed'");
    if ($stmt) {
        $stmt->bind_param('i', $providerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['completed_bookings'] = $row['count'];
        $stmt->close();
    }
    
    // Pending bookings
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM bookings WHERE provider_id = ? AND status IN ('pending', 'confirmed')");
    if ($stmt) {
        $stmt->bind_param('i', $providerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['pending_bookings'] = $row['count'];
        $stmt->close();
    }
    
    // Total earnings
    $earnings = calculateProviderEarnings($conn, $providerId);
    $stats['total_earnings'] = $earnings['total_earnings'];
    
    return $stats;
}

function getUserStats($conn, $userId) {
    $stats = [
        'total_bookings' => 0,
        'completed_bookings' => 0,
        'pending_bookings' => 0,
        'total_spent' => 0
    ];
    
    // Total bookings
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM bookings WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['total_bookings'] = $row['count'];
        $stmt->close();
    }
    
    // Completed bookings
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM bookings WHERE user_id = ? AND status = 'completed'");
    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['completed_bookings'] = $row['count'];
        $stmt->close();
    }
    
    // Pending bookings
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM bookings WHERE user_id = ? AND status IN ('pending', 'confirmed')");
    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['pending_bookings'] = $row['count'];
        $stmt->close();
    }
    
    // Total spent
    $stmt = $conn->prepare("SELECT COALESCE(SUM(service_price), 0) AS total FROM bookings WHERE user_id = ? AND status = 'completed'");
    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stats['total_spent'] = $row['total'] ?? 0;
        $stmt->close();
    }
    
    return $stats;
}
