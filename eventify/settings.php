<?php
/**
 * Eventify - Settings Page
 */
require_once 'includes/header.php';
requireAuth();

$currentUser = getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (!empty($fullName) && !empty($email)) {
            if (updateProfile($_SESSION['user_id'], [
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone
            ])) {
                $_SESSION['user_name'] = $fullName;
                $_SESSION['user_email'] = $email;
                $success = 'Profile updated successfully.';
            } else {
                $error = 'Failed to update profile.';
            }
        } else {
            $error = 'Name and email are required.';
        }
    } elseif ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($current) || empty($new) || empty($confirm)) {
            $error = 'All password fields are required.';
        } elseif ($new !== $confirm) {
            $error = 'New passwords do not match.';
        } elseif (strlen($new) < 6) {
            $error = 'New password must be at least 6 characters.';
        } else {
            $result = changePassword($_SESSION['user_id'], $current, $new);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }
}

$pageTitle = 'Settings - Eventify';
$activePage = 'settings';
?>

<style>
.settings-container {
  padding: 120px 0 80px;
  min-height: 100vh;
}
.settings-grid {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 32px;
}
.settings-sidebar {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.settings-nav {
  list-style: none;
}
.settings-nav a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 12px;
  color: var(--text-secondary);
  font-weight: 500;
  transition: all 0.25s;
  text-decoration: none;
}
.settings-nav a:hover, .settings-nav a.active {
  background: rgba(37, 99, 235, 0.1);
  color: var(--text-primary);
}
.settings-nav a.active {
  border: 1px solid rgba(37, 99, 235, 0.2);
}
.settings-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.settings-card {
  background: rgba(17, 24, 39, 0.7);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 20px;
  padding: 32px;
  backdrop-filter: blur(20px);
}
.settings-card-title {
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 24px;
}
.settings-avatar-section {
  display: flex;
  align-items: center;
  gap: 24px;
  margin-bottom: 32px;
  padding-bottom: 32px;
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.settings-avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid rgba(37, 99, 235, 0.3);
}
.settings-avatar-info h3 {
  font-size: 1.1rem;
  margin-bottom: 4px;
}
.settings-avatar-info p {
  color: var(--text-muted);
  font-size: 0.9rem;
}
.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
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
.danger-zone {
  border-color: rgba(239, 68, 68, 0.2) !important;
}
.danger-zone .settings-card-title {
  color: #EF4444;
}
@media (max-width: 768px) {
  .settings-grid { grid-template-columns: 1fr; }
  .settings-sidebar { display: none; }
  .form-row { grid-template-columns: 1fr; }
}
</style>

<div class="settings-container">
  <div class="container">
    <h1 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 32px;">Settings</h1>

    <div class="settings-grid">
      <!-- Sidebar -->
      <aside class="settings-sidebar">
        <div class="settings-card" style="padding: 16px;">
          <ul class="settings-nav">
            <li><a href="#profile" class="active" onclick="showSettingTab('profile', this)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              Profile
            </a></li>
            <li><a href="#password" onclick="showSettingTab('password', this)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
              Password
            </a></li>
          </ul>
        </div>
      </aside>

      <!-- Content -->
      <div class="settings-content">
        <?php if ($success): ?>
        <div class="success-msg"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="form-error-msg" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #F87171; padding: 12px 16px; border-radius: 12px; font-size: 0.9rem; font-weight: 600; margin-bottom: 20px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Profile Tab -->
        <div id="profile" class="tab-panel active">
          <div class="settings-card">
            <h2 class="settings-card-title">Profile Information</h2>

            <div class="settings-avatar-section">
              <img src="<?php echo htmlspecialchars($currentUser['avatar_url'] ?? 'images/avatars/default.jpg'); ?>" alt="" class="settings-avatar">
              <div class="settings-avatar-info">
                <h3><?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
                <p><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                <span class="status-badge status-active" style="margin-top: 8px;"><?php echo ucfirst($currentUser['role'] ?? 'User'); ?></span>
              </div>
            </div>

            <form method="POST" action="settings.php">
              <input type="hidden" name="action" value="profile">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="full_name">Full Name</label>
                  <input type="text" id="full_name" name="full_name" class="form-input" value="<?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="email">Email Address</label>
                  <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                </div>
              </div>
              <div class="form-group" style="max-width: 50%;">
                <label class="form-label" for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-input" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>" placeholder="+254 7XX XXX XXX">
              </div>
              <div style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Password Tab -->
        <div id="password" class="tab-panel">
          <div class="settings-card">
            <h2 class="settings-card-title">Change Password</h2>
            <form method="POST" action="settings.php">
              <input type="hidden" name="action" value="password">
              <div class="form-group">
                <label class="form-label" for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-input" required>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="new_password">New Password</label>
                  <input type="password" id="new_password" name="new_password" class="form-input" required placeholder="Min 6 characters">
                </div>
                <div class="form-group">
                  <label class="form-label" for="confirm_password">Confirm New Password</label>
                  <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
                </div>
              </div>
              <div style="margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Update Password</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
function showSettingTab(tabId, linkEl) {
  document.querySelectorAll('.settings-content .tab-panel').forEach(p => p.classList.remove('active'));
  document.getElementById(tabId).classList.add('active');
  document.querySelectorAll('.settings-nav a').forEach(a => a.classList.remove('active'));
  if (linkEl) linkEl.classList.add('active');
}
</script>

<?php require_once 'includes/footer.php'; ?>
