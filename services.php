<?php
include('db.php');
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Services</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">Astra Service</div>
        <div class="nav"><a href="dashbord.php">Back</a></div>
    </div>
    <div class="card" style="margin-top:18px">
        <h2 class="h1">Services</h2>
        <div class="grid">
        <?php
        $cat = isset($_GET['category']) ? trim($_GET['category']) : '';
        if ($cat !== '') {
            $stmt = $conn->prepare("SELECT id, name, phone, status FROM services WHERE category = ?");
            if ($stmt) {
                $stmt->bind_param('s', $cat);
                $stmt->execute();
                $res = $stmt->get_result();
                echo '<p class="small" style="margin-bottom:12px">Category: ' . htmlspecialchars($cat) . '</p>';
                while($row = $res->fetch_assoc()) {
                    echo '<div class="card">';
                    echo '<div class="title">' . htmlspecialchars($row['name']) . '</div>';
                    echo '<div class="small">' . htmlspecialchars($row['phone']) . '</div>';
                    if ($row['status'] === 'available') {
                        echo '<div class="actions"><a href="book.php?id=' . intval($row['id']) . '">Book Now</a></div>';
                    } else {
                        echo '<div class="small" style="color:var(--danger)">Booked</div>';
                    }
                    echo '</div>';
                }
                $stmt->close();
            }
        } else {
            echo '<p class="small">No category selected.</p>';
        }
        ?>
        </div>
    </div>
</div>
</body>
</html>
