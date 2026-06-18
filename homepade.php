<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit();
}

include('db.php');

$categories = [];
$category_result = $conn->query("SELECT DISTINCT category FROM services WHERE category <> '' ORDER BY category ASC");
if ($category_result) {
    while ($category_row = $category_result->fetch_assoc()) {
        $categories[] = $category_row['category'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astra Service - Homepage</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0d0d0d;
            color: #d3d3d3;
            margin: 0;
            padding: 20px;
        }
        header {
            background-color: #1a1a1a;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #404040;
        }
        .logo {
            color: #ffffff;
            font-size: 2em;
            font-weight: bold;
        }
        .search-container {
            width: 50%;
            position: relative;
        }
        .search-container input {
            width: 100%;
            padding: 10px;
            background-color: #262626;
            border: 1px solid #404040;
            border-radius: 20px;
            color: #ffffff;
            padding-left: 40px;
        }
        .search-container .icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #a0a0a0;
        }
        .logout-btn {
            background-color: #595959;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .logout-btn:hover {
            background-color: #6c6c6c;
        }
        .main-content {
            padding: 40px 0;
            text-align: center;
        }
        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }
        .service-block {
            background-color: #1a1a1a;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s, background-color 0.2s;
            text-align: center;
            text-decoration: none;
            color: #ffffff;
            display: block;
        }
        .service-block:hover {
            transform: translateY(-5px);
            background-color: #262626;
        }
        .service-block h3 {
            margin: 0;
            color: #ffffff;
            font-size: 1.2em;
        }
        .welcome-message {
            font-size: 1.5em;
            color: #ffffff;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="logo">Astra Service</div>
        <div class="search-container">
            <span class="icon"><i class="fas fa-search"></i></span>
            <input type="text" placeholder="Search for services...">
        </div>
        <div style="display:flex;gap:12px;align-items:center">
            <a href="my_booking.php" class="btn btn-sm" style="background:var(--accent);text-decoration:none">My Bookings</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="main-content">
        <h1 class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>Find the best professionals for your needs.</p>

        <div class="suggestions-grid">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <a class="service-block" href="dashbord.php?category=<?php echo urlencode($category); ?>">
                        <h3><?php echo htmlspecialchars($category); ?></h3>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="service-block">
                    <h3>No categories added yet</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
