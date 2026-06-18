<?php
session_start();
include_once '../shared/auth.php';
include_once '../shared/functions.php';

checkAdminAccess();
$pageTitle = 'Manage Users';

$conn = getDbConnection();
$messages = getFlashMessages();

// Handle user operations
if (isset($_POST['btn_add_user'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        setFlashError('Username, email, and password are required.');
    } elseif (!in_array($role, ['admin', 'user', 'provider'])) {
        setFlashError('Invalid role selected.');
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        if ($check) {
            $check->bind_param('ss', $username, $email);
            $check->execute();
            $existing = $check->get_result();
            $check->close();
            
            if ($existing && $existing->num_rows > 0) {
                setFlashError('Username or email already exists.');
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, phone) VALUES (?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param('sssss', $username, $email, $hashed, $role, $phone);
                    if ($stmt->execute()) {
                        setFlashSuccess('User added successfully!');
                    } else {
                        setFlashError('Error adding user: ' . $conn->error);
                    }
                    $stmt->close();
                } else {
                    setFlashError('Database error.');
                }
            }
        }
    }
    header('Location: manage_users.php');
    exit();
}

if (isset($_POST['btn_update_user'])) {
    $userId = intval($_POST['user_id'] ?? 0);
    $role = trim($_POST['role'] ?? 'user');
    $phone = trim($_POST['phone'] ?? '');
    
    if ($userId <= 0) {
        setFlashError('Invalid user.');
    } else {
        $stmt = $conn->prepare("UPDATE users SET role = ?, phone = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('ssi', $role, $phone, $userId);
            if ($stmt->execute()) {
                setFlashSuccess('User updated successfully!');
            } else {
                setFlashError('Error updating user: ' . $conn->error);
            }
            $stmt->close();
        }
    }
    header('Location: manage_users.php');
    exit();
}

if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    if ($userId > 0 && $userId !== 1) { // Prevent deleting admin user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $userId);
            if ($stmt->execute()) {
                setFlashSuccess('User deleted successfully!');
            } else {
                setFlashError('Error deleting user.');
            }
            $stmt->close();
        }
    } else {
        setFlashError('Cannot delete this user.');
    }
    header('Location: manage_users.php');
    exit();
}

$editUser = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    if ($editId > 0) {
        $stmt = $conn->prepare("SELECT id, username, email, role, phone FROM users WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $editId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $editUser = $result->fetch_assoc();
            }
            $stmt->close();
        }
    }
}

// Get all users
$users = [];
$result = $conn->query("SELECT id, username, email, role, phone, created_at FROM users ORDER BY role ASC, created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
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

    <h1 class="h1" style="margin-top: 20px;">Manage Users</h1>

    <div class="card">
        <h2 class="h2"><?php echo $editUser ? 'Edit User' : 'Add New User'; ?></h2>
        <form method="POST" class="form">
            <input type="hidden" name="user_id" value="<?php echo $editUser ? intval($editUser['id']) : 0; ?>">
            <div class="form-grid">
                <div>
                    <label class="label">Username <?php echo !$editUser ? '*' : ''; ?></label>
                    <input type="text" name="username" <?php echo !$editUser ? 'required' : 'disabled'; ?> value="<?php echo $editUser ? htmlspecialchars($editUser['username']) : ''; ?>">
                </div>
                <div>
                    <label class="label">Email <?php echo !$editUser ? '*' : ''; ?></label>
                    <input type="email" name="email" <?php echo !$editUser ? 'required' : 'disabled'; ?> value="<?php echo $editUser ? htmlspecialchars($editUser['email']) : ''; ?>">
                </div>
                <div>
                    <label class="label">Phone</label>
                    <input type="tel" name="phone" value="<?php echo $editUser ? htmlspecialchars($editUser['phone'] ?? '') : ''; ?>">
                </div>
                <div>
                    <label class="label">Role</label>
                    <select name="role">
                        <option value="user" <?php echo (!$editUser || $editUser['role'] === 'user') ? 'selected' : ''; ?>>Customer</option>
                        <option value="provider" <?php echo ($editUser && $editUser['role'] === 'provider') ? 'selected' : ''; ?>>Service Provider</option>
                        <option value="admin" <?php echo ($editUser && $editUser['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <?php if (!$editUser): ?>
                    <div>
                        <label class="label">Password *</label>
                        <input type="password" name="password" required>
                    </div>
                <?php endif; ?>
            </div>
            <button type="submit" name="<?php echo $editUser ? 'btn_update_user' : 'btn_add_user'; ?>" class="btn">
                <?php echo $editUser ? 'Update User' : 'Add User'; ?>
            </button>
            <?php if ($editUser): ?>
                <a href="manage_users.php" class="btn secondary btn-sm">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h2 class="h2">All Users</h2>
        <?php if (empty($users)): ?>
            <p class="small">No users found.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border);">
                            <th style="padding: 10px; text-align: left;">Username</th>
                            <th style="padding: 10px; text-align: left;">Email</th>
                            <th style="padding: 10px; text-align: left;">Role</th>
                            <th style="padding: 10px; text-align: left;">Phone</th>
                            <th style="padding: 10px; text-align: left;">Joined</th>
                            <th style="padding: 10px; text-align: left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid var(--border);">
                                <td style="padding: 10px;"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td style="padding: 10px;"><span class="status-badge"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span></td>
                                <td style="padding: 10px;"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                <td style="padding: 10px;"><?php echo formatDate($user['created_at']); ?></td>
                                <td style="padding: 10px;">
                                    <a href="manage_users.php?edit=<?php echo intval($user['id']); ?>" class="btn btn-sm">Edit</a>
                                    <?php if (intval($user['id']) !== 1): ?>
                                        <a href="manage_users.php?delete=<?php echo intval($user['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php include '../shared/footer.php'; ?>
