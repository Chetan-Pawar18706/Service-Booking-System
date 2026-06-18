<?php
include('db.php');
session_start();

// Validate booking id
$bid = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($bid <= 0) {
	header('Location: my_booking.php');
	exit();
}

// Determine current user id
$user_id = null;
if (!empty($_SESSION['user_id'])) {
	$user_id = intval($_SESSION['user_id']);
} elseif (!empty($_SESSION['username'])) {
	$uname = $_SESSION['username'];
	$ust = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
	if ($ust) {
		$ust->bind_param('ss', $uname, $uname);
		$ust->execute();
		$ures = $ust->get_result();
		if ($ures && $ures->num_rows > 0) {
			$urow = $ures->fetch_assoc();
			$user_id = (int)$urow['id'];
		}
		$ust->close();
	}
}

if (!$user_id) {
	// Not logged in properly
	header('Location: login.php');
	exit();
}

// Fetch booking and ensure it belongs to current user
$bst = $conn->prepare("SELECT service_id, user_id FROM bookings WHERE id = ? LIMIT 1");
if (!$bst) {
	header('Location: my_booking.php');
	exit();
}
$bst->bind_param('i', $bid);
$bst->execute();
$bres = $bst->get_result();
if (!$bres || $bres->num_rows === 0) {
	$bst->close();
	header('Location: my_booking.php');
	exit();
}
$brow = $bres->fetch_assoc();
$bst->close();

if ((int)$brow['user_id'] !== $user_id) {
	// Not authorized to unbook this booking
	header('Location: my_booking.php');
	exit();
}

$service = (int)$brow['service_id'];

// Delete booking
$dstmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
if ($dstmt) {
	$dstmt->bind_param('i', $bid);
	$dstmt->execute();
	$dstmt->close();
}

// Mark service as available again
$ust = $conn->prepare("UPDATE services SET status = 'available' WHERE id = ?");
if ($ust) {
	$ust->bind_param('i', $service);
	$ust->execute();
	$ust->close();
}

header('Location: my_booking.php');
exit();
?>
