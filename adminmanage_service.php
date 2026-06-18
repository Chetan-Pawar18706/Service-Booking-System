<?php
include('db.php');
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || ($_SESSION['username'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}

$success_msg = $_SESSION['flash_success'] ?? '';
$error_msg = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

if (isset($_POST['add'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $cat = preg_replace('/\s+/', ' ', trim($_POST['category'] ?? ''));

    if (empty($name) || empty($phone) || empty($cat)) {
        $_SESSION['flash_error'] = 'Please fill all required fields.';
    } else {
        $check = $conn->prepare("SELECT id FROM services WHERE category = ? AND name = ? AND phone = ? LIMIT 1");
        if ($check) {
            $check->bind_param('sss', $cat, $name, $phone);
            $check->execute();
            $existing = $check->get_result();
            $alreadyExists = $existing && $existing->num_rows > 0;
            $check->close();

            if ($alreadyExists) {
                $_SESSION['flash_error'] = 'This service provider already exists.';
            } else {
                $stmt = $conn->prepare("INSERT INTO services (category, name, phone, status) VALUES (?, ?, ?, 'available')");
                if ($stmt) {
                    $stmt->bind_param('sss', $cat, $name, $phone);
                    if ($stmt->execute()) {
                        $_SESSION['flash_success'] = 'Service provider added successfully!';
                    } else {
                        $_SESSION['flash_error'] = 'Error: ' . $conn->error;
                    }
                    $stmt->close();
                } else {
                    $_SESSION['flash_error'] = 'Database error: could not prepare insert query.';
                }
            }
        } else {
            $_SESSION['flash_error'] = 'Database error: could not prepare duplicate check.';
        }
    }

    header('Location: adminmanage_service.php');
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                $_SESSION['flash_success'] = 'Service provider deleted successfully!';
            } else {
                $_SESSION['flash_error'] = 'Error deleting service: ' . $conn->error;
            }
            $stmt->close();
        }
    }

    header('Location: adminmanage_service.php');
    exit();
}

$categories = [];
$category_result = $conn->query("SELECT DISTINCT category FROM services WHERE category <> '' ORDER BY category ASC");
if ($category_result) {
    while ($category_row = $category_result->fetch_assoc()) {
        $categories[] = $category_row['category'];
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Manage Services</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .form-full {
            grid-column: 1 / -1;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .status-available {
            background: rgba(22, 163, 74, 0.1);
            color: var(--success);
        }

        .status-booked {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .card-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .btn-sm {
            padding: 6px 10px;
            font-size: 0.9rem;
        }

        .btn-danger {
            background: var(--danger);
        }

        .btn-danger:hover {
            filter: brightness(1.1);
        }

        @media(max-width:600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">Astra Service - Admin</div>
            <div class="nav"><a href="admindashbord.php">Dashboard</a> | <a href="logout.php">Logout</a></div>
        </div>

        <?php if (!empty($success_msg)): ?>
            <div class="alert success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_msg)): ?>
            <div class="alert error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <div class="card" style="margin-top:18px">
            <h2 class="h1">Add New Service Provider</h2>
            <form method="POST" class="form">
                <div class="form-grid">
                    <div>
                        <label class="small">Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    <div>
                        <label class="small">Phone *</label>
                        <input type="text" name="phone" required>
                    </div>
                    <div class="form-full">
                        <label class="small">Category *</label>
                        <input type="text" name="category" list="category-list"
                            placeholder="e.g. Plumber, Electrician, AC Repair" required>
                        <datalist id="category-list">
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>">
                                <?php endforeach; ?>
                        </datalist>
                        <div class="small" style="margin-top:6px">Type a new category or pick an existing one.</div>
                    </div>
                </div>
                <div style="margin-top:14px"><button type="submit" name="add" class="btn">+ Add Service
                        Provider</button></div>
            </form>
        </div>

        <h2 class="h1" style="margin-top:24px">All Service Providers</h2>
        <?php
        $res = $conn->query("SELECT * FROM services ORDER BY id DESC");
        if (!$res || $res->num_rows === 0) {
            echo '<div class="card"><p class="small">No service providers found. Add one above.</p></div>';
        } else {
            echo '<div class="grid">';
            while ($row = $res->fetch_assoc()) {
                $status_class = ($row['status'] === 'available') ? 'status-available' : 'status-booked';
                $status_text = ucfirst($row['status']);
                echo '<div class="card">';
                echo '<div style="display:flex;justify-content:space-between;align-items:start">';
                echo '<div class="title">' . htmlspecialchars($row['name']) . '</div>';
                echo '<span class="status-badge ' . $status_class . '">' . $status_text . '</span>';
                echo '</div>';
                echo '<div class="small" style="margin-top:8px">';
                echo '<div>Phone: ' . htmlspecialchars($row['phone']) . '</div>';
                echo '<div>Category: ' . htmlspecialchars($row['category']) . '</div>';
                echo '</div>';
                echo '<div class="card-actions">';
            echo '<a href="adminmanage_service.php?delete=' . intval($row['id']) . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this provider?\')">Delete</a>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
    </div>
</body>

</html>
