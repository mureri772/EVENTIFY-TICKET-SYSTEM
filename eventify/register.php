<?php
/**
 * Eventify - Registration Page
 */
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    // Validation
    if (empty($fullName) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $result = registerUser($fullName, $email, $password, $phone, $role);
        if ($result['success']) {
            setFlash('success', 'Welcome to Eventify, ' . htmlspecialchars($fullName) . '!');
            header('Location: index.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Sign Up - Eventify';
$activePage = 'register';
$prefillRole = $_GET['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css">
  <style>
    .auth-page {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      background: var(--bg-body);
      position: relative;
      overflow: hidden;
    }
    .auth-page::before {
      content: '';
      position: absolute;
      top: -20%;
      right: -10%;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }
    .auth-page::after {
      content: '';
      position: absolute;
      bottom: -20%;
      left: -10%;
      width: 500px;
      height: 500px;
      background: radial-gradient(circle, rgba(249, 115, 22, 0.07) 0%, transparent 70%);
      border-radius: 50%;
      pointer-events: none;
    }
    .auth-card {
      width: 100%;
      max-width: 480px;
      background: rgba(17, 24, 39, 0.9);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 24px;
      padding: 48px 40px;
      box-shadow: 0 25px 50px rgba(0,0,0,0.5);
      position: relative;
      z-index: 1;
    }
    .auth-logo {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-bottom: 32px;
      text-decoration: none;
    }
    .auth-logo-text {
      font-size: 1.8rem;
      font-weight: 800;
    }
    .auth-title {
      font-size: 1.5rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 8px;
    }
    .auth-subtitle {
      text-align: center;
      color: var(--text-muted);
      font-size: 0.95rem;
      margin-bottom: 32px;
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
    .form-input::placeholder { color: var(--text-muted); }
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
    .btn-full { width: 100%; justify-content: center; }
    .auth-footer {
      text-align: center;
      margin-top: 24px;
      color: var(--text-muted);
      font-size: 0.9rem;
    }
    .auth-footer a {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
    }
    .auth-footer a:hover { text-decoration: underline; }
    .role-select { display: flex; gap: 12px; margin-bottom: 20px; }
    .role-option {
      flex: 1;
      padding: 16px;
      background: rgba(255,255,255,0.03);
      border: 2px solid rgba(255,255,255,0.08);
      border-radius: 14px;
      cursor: pointer;
      text-align: center;
      transition: all 0.25s;
      color: var(--text-secondary);
    }
    .role-option:hover {
      border-color: rgba(37, 99, 235, 0.3);
      background: rgba(37, 99, 235, 0.05);
    }
    .role-option.active {
      border-color: var(--primary);
      background: rgba(37, 99, 235, 0.1);
      color: var(--text-primary);
    }
    .role-option input[type="radio"] { display: none; }
    .role-option-title { font-weight: 700; font-size: 0.9rem; }
    .role-option-desc { font-size: 0.75rem; margin-top: 4px; }
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    @media (max-width: 640px) {
      .auth-card { padding: 32px 24px; border-radius: 20px; }
      .form-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<div class="auth-page">
  <div class="auth-card">
    <a href="index.php" class="auth-logo">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="16" y1="2" x2="16" y2="6"></line>
        <line x1="8" y1="2" x2="8" y2="6"></line>
        <line x1="3" y1="10" x2="21" y2="10"></line>
      </svg>
      <span class="auth-logo-text"><span class="logo-blue">Event</span><span class="logo-orange">ify</span></span>
    </a>

    <h1 class="auth-title">Create Account</h1>
    <p class="auth-subtitle">Join Nairobi's premier event platform</p>

    <?php if ($error): ?>
    <div class="form-error-msg"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
      <label class="form-label">I want to:</label>
      <div class="role-select">
        <label class="role-option <?php echo $prefillRole === 'user' ? 'active' : ''; ?>" onclick="selectRole(this, 'user')">
          <input type="radio" name="role" value="user" <?php echo $prefillRole === 'user' ? 'checked' : ''; ?>>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
          <div class="role-option-title">Attend Events</div>
          <div class="role-option-desc">Discover & book</div>
        </label>
        <label class="role-option <?php echo $prefillRole === 'organizer' ? 'active' : ''; ?>" onclick="selectRole(this, 'organizer')">
          <input type="radio" name="role" value="organizer" <?php echo $prefillRole === 'organizer' ? 'checked' : ''; ?>>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
          <div class="role-option-title">Host Events</div>
          <div class="role-option-desc">Create & sell</div>
        </label>
      </div>

      <div class="form-group">
        <label class="form-label" for="full_name">Full Name *</label>
        <input type="text" id="full_name" name="full_name" class="form-input" placeholder="John Doe" required autofocus value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="email">Email Address *</label>
          <input type="email" id="email" name="email" class="form-input" placeholder="you@example.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone" class="form-input" placeholder="+254 7XX XXX XXX" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="password">Password *</label>
          <input type="password" id="password" name="password" class="form-input" placeholder="Min 6 characters" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="confirm_password">Confirm Password *</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Repeat password" required>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">Create Account</button>
    </form>

    <p class="auth-footer">
      Already have an account? <a href="login.php">Sign in</a>
    </p>
  </div>
</div>

<script>
function selectRole(el, role) {
  document.querySelectorAll('.role-option').forEach(o => o.classList.remove('active'));
  el.classList.add('active');
  el.querySelector('input[type="radio"]').checked = true;
}
</script>

</body>
</html>