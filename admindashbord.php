<?php include('db.php'); session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Admin Panel</title>
	<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
	<div class="header">
		<div class="logo">Astra Service — Admin</div>
		<div class="nav"><a href="adminmanage_service.php">Manage Services</a> | <a href="logout.php">Logout</a></div>
	</div>
	<div class="card" style="margin-top:18px">
		<h2 class="h1">Admin Panel</h2>
		<p class="small">Use the links above to manage services and bookings.</p>
	</div>
</div>
</body>
</html>