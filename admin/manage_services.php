<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';

checkAdminAccess();
$pageTitle = 'Manage Services';

$conn = getDbConnection();
$messages = getFlashMessages();

// Handle service operations
if (isset($_POST['btn_add_service']) || isset($_POST['btn_update_service'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $providerId = intval($_POST['provider_id'] ?? 0);
    $status = in_array($_POST['status'] ?? '', ['available', 'booked']) ? $_POST['status'] : 'available';

    if ($name === '' || $phone === '' || $category === '') {
        setFlashError('Please fill all required fields.');
    } else {
        $categoryCheck = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
        if ($categoryCheck) {
            $categoryCheck->bind_param('s', $category);
            $categoryCheck->execute();
            $categoryRes = $categoryCheck->get_result();
            $categoryCheck->close();
            
            if (!$categoryRes || $categoryRes->num_rows === 0) {
                setFlashError('Selected category does not exist. Please add it first.');
            } else {
                if (isset($_POST['btn_add_service'])) {
                    $check = $conn->prepare("SELECT id FROM services WHERE category = ? AND name = ? AND phone = ? LIMIT 1");
                    if ($check) {
                        $check->bind_param('sss', $category, $name, $phone);
                        $check->execute();
                        $existing = $check->get_result();
                        $exists = $existing && $existing->num_rows > 0;
                        $check->close();

                        if ($exists) {
                            setFlashError('This service already exists in the selected category.');
                        } else {
                            $stmt = $conn->prepare("INSERT INTO services (category, provider_id, name, phone, price, status) VALUES (?, ?, ?, ?, ?, ?)");
                            if ($stmt) {
                                $stmt->bind_param('sissds', $category, $providerId, $name, $phone, $price, $status);
                                if ($stmt->execute()) {
                                    setFlashSuccess('Service added successfully!');
                                } else {
                                    setFlashError('Error: ' . $conn->error);
                                }
                                $stmt->close();
                            }
                        }
                    }
                } else {
                    $serviceId = intval($_POST['service_id'] ?? 0);
                    if ($serviceId > 0) {
                        $stmt = $conn->prepare("UPDATE services SET category = ?, provider_id = ?, name = ?, phone = ?, price = ?, status = ? WHERE id = ?");
                        if ($stmt) {
                            $stmt->bind_param('sissdsi', $category, $providerId, $name, $phone, $price, $status, $serviceId);
                            if ($stmt->execute()) {
                                setFlashSuccess('Service updated successfully!');
                            } else {
                                setFlashError('Error updating service: ' . $conn->error);
                            }
                            $stmt->close();
                        }
                    } else {
                        setFlashError('Invalid service selected for update.');
                    }
                }
            }
        }
    }
    header('Location: manage_services.php');
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                setFlashSuccess('Service deleted successfully!');
            } else {
                setFlashError('Error deleting service: ' . $conn->error);
            }
            $stmt->close();
        }
    }
    header('Location: manage_services.php');
    exit();
}

$editService = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($editId > 0) {
        $stmt = $conn->prepare("SELECT id, category, provider_id, name, phone, price, status FROM services WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $editId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $editService = $result->fetch_assoc();
            }
            $stmt->close();
        }
    }
}

// Get categories and providers
$categories = [];
$category_result = $conn->query("SELECT name, id FROM categories ORDER BY name ASC");
if ($category_result) {
    while ($category_row = $category_result->fetch_assoc()) {
        $categories[] = $category_row;
    }
}

$providers = [];
$provider_result = $conn->query("SELECT id, username FROM users WHERE role = 'provider' ORDER BY username ASC");
if ($provider_result) {
    while ($provider_row = $provider_result->fetch_assoc()) {
        $providers[] = $provider_row;
    }
}

// Get all services
$services = [];
$result = $conn->query("
    SELECT s.*, u.username AS provider_name 
    FROM services s 
    LEFT JOIN users u ON s.provider_id = u.id 
    ORDER BY s.id DESC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
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

    <h1 class="h1" style="margin-top: 20px;">Manage Services</h1>

    <div class="card">
        <h2 class="h2"><?php echo $editService ? 'Edit Service' : 'Add New Service'; ?></h2>
        <form method="POST" class="form">
            <input type="hidden" name="service_id" value="<?php echo $editService ? intval($editService['id']) : 0; ?>">
            <div class="form-grid">
                <div>
                    <label class="label">Service Name *</label>
                    <input type="text" name="name" required value="<?php echo $editService ? htmlspecialchars($editService['name']) : ''; ?>">
                </div>
                <div>
                    <label class="label">Phone *</label>
                    <input type="tel" name="phone" required value="<?php echo $editService ? htmlspecialchars($editService['phone']) : ''; ?>">
                </div>
                <div>
                    <label class="label">Category *</label>
                    <select name="category" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['name']); ?>" <?php echo $editService && $editService['category'] === $category['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="label">Provider</label>
                    <select name="provider_id">
                        <option value="">Not Assigned</option>
                        <?php foreach ($providers as $provider): ?>
                            <option value="<?php echo intval($provider['id']); ?>" <?php echo $editService && $editService['provider_id'] == $provider['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($provider['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="label">Price</label>
                    <input type="number" name="price" step="0.01" value="<?php echo $editService ? floatval($editService['price']) : 0; ?>">
                </div>
                <div>
                    <label class="label">Status</label>
                    <select name="status">
                        <option value="available" <?php echo !$editService || $editService['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="booked" <?php echo $editService && $editService['status'] === 'booked' ? 'selected' : ''; ?>>Booked</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="<?php echo $editService ? 'btn_update_service' : 'btn_add_service'; ?>" class="btn">
                <?php echo $editService ? 'Update Service' : 'Add Service'; ?>
            </button>
            <?php if ($editService): ?>
                <a href="manage_services.php" class="btn secondary btn-sm">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h2 class="h2">All Services</h2>
        <?php if (empty($services)): ?>
            <p class="small">No services found.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 10px; text-align: left;">Name</th>
                            <th style="padding: 10px; text-align: left;">Category</th>
                            <th style="padding: 10px; text-align: left;">Provider</th>
                            <th style="padding: 10px; text-align: left;">Phone</th>
                            <th style="padding: 10px; text-align: left;">Price</th>
                            <th style="padding: 10px; text-align: left;">Status</th>
                            <th style="padding: 10px; text-align: left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 10px;"><?php echo htmlspecialchars($service['name']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($service['category']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($service['provider_name'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($service['phone']); ?></td>
                                <td style="padding: 10px;"><?php echo formatCurrency($service['price'] ?? 0); ?></td>
                                <td style="padding: 10px;"><span class="status-badge status-<?php echo htmlspecialchars($service['status']); ?>"><?php echo ucfirst(htmlspecialchars($service['status'])); ?></span></td>
                                <td style="padding: 10px;">
                                    <a href="manage_services.php?edit=<?php echo intval($service['id']); ?>" class="btn btn-sm">Edit</a>
                                    <a href="manage_services.php?delete=<?php echo intval($service['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php include '../shared/footer.php'; ?>
