<?php
session_start();
include('db.php');

$categories = [];
$cat_result = $conn->query("SELECT DISTINCT category FROM services WHERE category <> '' ORDER BY category ASC");
if ($cat_result) {
    while ($cat_row = $cat_result->fetch_assoc()) {
        $categories[] = $cat_row['category'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">Astra Service</div>
        <div class="nav">
            <a href="my_booking.php" class="btn btn-sm" style="background:var(--accent);text-decoration:none">My Bookings</a>
            <a href="logout.php" class="btn secondary">Logout</a>
        </div>
    </div>

    <h2 class="h1" style="margin-top:18px">User Dashboard</h2>

    <form method="GET" style="margin-bottom:20px;display:flex;gap:8px;flex-wrap:wrap">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <button type="submit" name="category" value="<?php echo htmlspecialchars($category); ?>" class="btn"><?php echo htmlspecialchars($category); ?></button>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="small">No categories available yet.</p>
        <?php endif; ?>
    </form>

    <hr>

<?php
if (isset($_GET['category'])) {
    $category = trim($_GET['category']);

    if ($category === '') {
        echo "<p>Invalid category.</p>";
    } else {
        $stmt = $conn->prepare("SELECT id, name, phone, status FROM services WHERE category = ?");
        if ($stmt) {
            $stmt->bind_param('s', $category);
            $stmt->execute();
            $result = $stmt->get_result();

            echo "<h3>Showing records for: <u>" . htmlspecialchars($category) . "</u></h3>";

            if ($result && $result->num_rows > 0) {
                echo '<div class="card-container">';
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p><strong>Phone:</strong> ' . htmlspecialchars($row['phone']) . '</p>';
                    echo '<p><strong>Status:</strong> ' . htmlspecialchars($row['status']) . '</p>';
                    if ($row['status'] === 'available') {
                        echo '<a href="book.php?id=' . intval($row['id']) . '" class="btn btn-sm"><button type="button" style="border:none;background:none;color:inherit;cursor:pointer">Book Now</button></a>';
                    } else {
                        echo '<button type="button" class="btn btn-sm" disabled>Booked</button>';
                    }
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>No providers found for this category.</p>';
            }

            $stmt->close();
        } else {
            echo '<p>Database error: could not prepare statement.</p>';
        }
    }
}
?>

</body>
</html>
