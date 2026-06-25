<?php
/**
 * Eventify - Admin Panel (Add/Manage Admins)
 */
require_once 'includes/header.php';
requireAdmin();

$db = getDB();
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_admin') {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'organizer';

        if (empty($fullName) || empty($email) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } else {
            // Check if email exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = 'Email already registered.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (full_name, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$fullName, $email, $phone, $hash, $role]);
                $success = 'User created successfully!';
            }
        }
    } elseif ($action === 'delete_user') {
        $userId = intval($_POST['user_id'] ?? 0);
        if ($userId > 0 && $userId != $_SESSION['user_id']) {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $success = 'User deleted successfully.';
        } else {
            $error = 'Cannot delete yourself or invalid user.';
        }
    } elseif ($action === 'change_role') {
        $userId = intval($_POST['user_id'] ?? 0);
        $newRole = $_POST['new_role'] ?? 'user';
        if ($userId > 0 && $userId != $_SESSION['user_id']) {
            $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$newRole, $userId]);
            $success = 'Role updated successfully.';
        } else {
            $error = 'Cannot change your own role or invalid user.';
        }
    }
}

// Get all users
$users = [];
if ($db) {
    $stmt = $db->query("SELECT id, full_name, email, phone, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll() ?: [];
}

$pageTitle = 'Admin Panel - Eventify';
?>

<style>
.admin-container {
  padding: 120px 0 80px;
  min-height: 100vh;
}
.admin-card {
  background: rgba(17, 24, 39, 0.7);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 24px;
  padding: 40px;
  backdrop-filter: blur(20px);
  margin-bottom: 32px;
}
.admin-title {
  font-size: 1.8rem;
  font-weight: 800;
  margin-bottom: 8px;
}
.admin-subtitle {
  color: var(--text-muted);
  margin-bottom: 32px;
}
.form-error-msg {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  color: #F87171;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 20px;
}
.success-msg {
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.2);
  color: #34D399;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 20px;
}
.users-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 24px;
}
.users-table th {
  text-align: left;
  padding: 14px 16px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-muted);
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.users-table td {
  padding: 16px;
  font-size: 0.9rem;
  border-bottom: 1px solid rgba(255,255,255,0.04);
}
.users-table tr:hover td {
  background: rgba(255,255,255,0.02);
}
.role-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
}
.role-admin { background: rgba(139, 92, 246, 0.1); color: #A78BFA; border: 1px solid rgba(139, 92, 246, 0.2); }
.role-organizer { background: rgba(37, 99, 235, 0.1); color: #60A5FA; border: 1px solid rgba(37, 99, 235, 0.2); }
.role-user { background: rgba(107, 114, 128, 0.1); color: #9CA3AF; border: 1px solid rgba(107, 114, 128, 0.2); }
.action-btn {
  background: none;
  border: 1px solid rgba(255,255,255,0.1);
  color: var(--text-muted);
  cursor: pointer;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  transition: all 0.2s;
  margin-right: 6px;
}
.action-btn:hover { color: var(--text-primary); background: rgba(255,255,255,0.05); }
.action-btn.delete:hover { background: rgba(239,68,68,0.1); color: #EF4444; border-color: rgba(239,68,68,0.2); }
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.form-group { margin-bottom: 20px; }
.form-label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-secondary);
  margin-bottom: 6px;
}
.form-input {
  width: 100%;
  padding: 14px 16px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px;
  color: var(--text-primary);
  font-family: var(--font-sans);
  font-size: 0.95rem;
  outline: none;
  transition: all 0.25s;
}
.form-input:focus {
  border-color: rgba(37, 99, 235, 0.5);
  background: rgba(255,255,255,0.06);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}
.form-select {
  width: 100%;
  padding: 14px 16px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px;
  color: var(--text-primary);
  font-family: var(--font-sans);
  font-size: 0.95rem;
  outline: none;
  cursor: pointer;
}
.form-select option { background: #111827; color: #fff; }
@media (max-width: 768px) {
  .admin-card { padding: 24px; }
  .form-row { grid-template-columns: 1fr; }
  .users-table { display: block; overflow-x: auto; }
}
</style>

<div class="admin-container">
  <div class="container">
    <a href="dashboard.php" style="display:inline-flex; align-items:center; gap:8px; color:var(--text-muted); font-size:0.9rem; margin-bottom:16px; text-decoration:none;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      Back to Dashboard
    </a>

    <h1 class="admin-title">Admin Panel</h1>
    <p class="admin-subtitle">Manage users and administrators</p>

    <?php if ($success): ?>
    <div class="success-msg"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="form-error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Add New User Form -->
    <div class="admin-card">
      <h2 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 24px;">Add New User / Admin</h2>
      <form method="POST" action="admin.php">
        <input type="hidden" name="action" value="add_admin">

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="full_name">Full Name *</label>
            <input type="text" id="full_name" name="full_name" class="form-input" placeholder="e.g. Billie Jean" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="email">Email Address *</label>
            <input type="email" id="email" name="email" class="form-input" placeholder="email@example.com" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-input" placeholder="+254 7XX XXX XXX">
          </div>
          <div class="form-group">
            <label class="form-label" for="password">Password *</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="Min 6 characters" required>
          </div>
        </div>

        <div class="form-group" style="max-width: 300px;">
          <label class="form-label" for="role">Role</label>
          <select id="role" name="role" class="form-select">
            <option value="user">User</option>
            <option value="organizer">Organizer</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 8px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          Create User
        </button>
      </form>
    </div>

    <!-- Users List -->
    <div class="admin-card">
      <h2 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 24px;">All Users (<?php echo count($users); ?>)</h2>

      <?php if (empty($users)): ?>
      <p style="color: var(--text-muted); text-align: center; padding: 40px;">No users found in database.</p>
      <?php else: ?>
      <table class="users-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Joined</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($u['full_name']); ?></strong></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo htmlspecialchars($u['phone'] ?? '-'); ?></td>
            <td>
              <span class="role-badge role-<?php echo $u['role']; ?>">
                <?php echo ucfirst($u['role']); ?>
              </span>
            </td>
            <td><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
            <td>
              <?php if ($u['id'] != $_SESSION['user_id']): ?>
              <form method="POST" action="admin.php" style="display: inline;" onsubmit="return confirm('Change role for <?php echo htmlspecialchars($u['full_name']); ?>?');">
                <input type="hidden" name="action" value="change_role">
                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                <select name="new_role" onchange="this.form.submit()" class="form-select" style="width: auto; display: inline-block; padding: 6px 12px; font-size: 0.8rem;">
                  <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                  <option value="organizer" <?php echo $u['role'] === 'organizer' ? 'selected' : ''; ?>>Organizer</option>
                  <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
              </form>
              <form method="POST" action="admin.php" style="display: inline;" onsubmit="return confirm('Delete <?php echo htmlspecialchars($u['full_name']); ?>? This cannot be undone.');">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                <button type="submit" class="action-btn delete">Delete</button>
              </form>
              <?php else: ?>
              <span style="color: var(--text-muted); font-size: 0.85rem;">(You)</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>