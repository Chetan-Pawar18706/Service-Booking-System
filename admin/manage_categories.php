<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';

checkAdminAccess();
$pageTitle = 'Manage Categories';

$conn = getDbConnection();
$messages = getFlashMessages();

// Handle category operations
if (isset($_POST['btn_add_category'])) {
    $categoryName = trim($_POST['category_name'] ?? '');
    $categoryDesc = trim($_POST['category_description'] ?? '');

    if ($categoryName === '') {
        setFlashError('Category name is required.');
    } else {
        $check = $conn->prepare("SELECT id FROM categories WHERE name = ? LIMIT 1");
        if ($check) {
            $check->bind_param('s', $categoryName);
            $check->execute();
            $result = $check->get_result();
            $exists = $result && $result->num_rows > 0;
            $check->close();

            if ($exists) {
                setFlashError('Category already exists.');
            } else {
                $insert = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                if ($insert) {
                    $insert->bind_param('ss', $categoryName, $categoryDesc);
                    if ($insert->execute()) {
                        setFlashSuccess('Category added successfully!');
                    } else {
                        setFlashError('Error adding category: ' . $conn->error);
                    }
                    $insert->close();
                }
            }
        }
    }
    header('Location: manage_categories.php');
    exit();
}

if (isset($_POST['btn_update_category'])) {
    $categoryId = intval($_POST['category_id'] ?? 0);
    $categoryName = trim($_POST['category_name'] ?? '');
    $categoryDesc = trim($_POST['category_description'] ?? '');

    if ($categoryId <= 0 || $categoryName === '') {
        setFlashError('Invalid category data.');
    } else {
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('ssi', $categoryName, $categoryDesc, $categoryId);
            if ($stmt->execute()) {
                setFlashSuccess('Category updated successfully!');
            } else {
                setFlashError('Error updating category: ' . $conn->error);
            }
            $stmt->close();
        }
    }
    header('Location: manage_categories.php');
    exit();
}

if (isset($_GET['delete'])) {
    $categoryId = intval($_GET['delete']);
    if ($categoryId > 0) {
        $checkServices = $conn->prepare("SELECT COUNT(*) AS total FROM services WHERE category IN (SELECT name FROM categories WHERE id = ?)");
        if ($checkServices) {
            $checkServices->bind_param('i', $categoryId);
            $checkServices->execute();
            $res = $checkServices->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $checkServices->close();
            
            if ($row && intval($row['total']) > 0) {
                setFlashError('Cannot delete category with services assigned. Remove services first.');
            } else {
                $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param('i', $categoryId);
                    if ($stmt->execute()) {
                        setFlashSuccess('Category deleted successfully!');
                    } else {
                        setFlashError('Error deleting category.');
                    }
                    $stmt->close();
                }
            }
        }
    }
    header('Location: manage_categories.php');
    exit();
}

$editCategory = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($editId > 0) {
        $stmt = $conn->prepare("SELECT id, name, description FROM categories WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $editId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $editCategory = $result->fetch_assoc();
            }
            $stmt->close();
        }
    }
}

// Get all categories with service count
$categories = [];
$result = $conn->query("
    SELECT c.id, c.name, c.description, COUNT(s.id) AS service_count
    FROM categories c
    LEFT JOIN services s ON c.name = s.category
    GROUP BY c.id, c.name, c.description
    ORDER BY c.name ASC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
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

    <h1 class="h1" style="margin-top: 20px;">Manage Categories</h1>

    <div class="card">
        <h2 class="h2"><?php echo $editCategory ? 'Edit Category' : 'Add New Category'; ?></h2>
        <form method="POST" class="form">
            <input type="hidden" name="category_id" value="<?php echo $editCategory ? intval($editCategory['id']) : 0; ?>">
            <div class="form-grid">
                <div>
                    <label class="label">Category Name *</label>
                    <input type="text" name="category_name" required value="<?php echo $editCategory ? htmlspecialchars($editCategory['name']) : ''; ?>">
                </div>
                <div class="form-full">
                    <label class="label">Description</label>
                    <input type="text" name="category_description" value="<?php echo $editCategory ? htmlspecialchars($editCategory['description'] ?? '') : ''; ?>">
                </div>
            </div>
            <button type="submit" name="<?php echo $editCategory ? 'btn_update_category' : 'btn_add_category'; ?>" class="btn">
                <?php echo $editCategory ? 'Update Category' : 'Add Category'; ?>
            </button>
            <?php if ($editCategory): ?>
                <a href="manage_categories.php" class="btn secondary btn-sm">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h2 class="h2">All Categories</h2>
        <?php if (empty($categories)): ?>
            <p class="small">No categories found.</p>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($categories as $category): ?>
                    <div class="card provider-card">
                        <div>
                            <div class="title"><?php echo htmlspecialchars($category['name']); ?></div>
                            <?php if ($category['description']): ?>
                                <div class="small"><?php echo htmlspecialchars($category['description']); ?></div>
                            <?php endif; ?>
                            <div class="small" style="margin-top: 5px; color: var(--accent-2);">Services: <?php echo intval($category['service_count']); ?></div>
                        </div>
                        <div class="card-actions">
                            <a href="manage_categories.php?edit=<?php echo intval($category['id']); ?>" class="btn btn-sm">Edit</a>
                            <?php if (intval($category['service_count']) === 0): ?>
                                <a href="manage_categories.php?delete=<?php echo intval($category['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

<?php include '../shared/footer.php'; ?>
