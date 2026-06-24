<?php
/**
 * Eventify - Header Include
 */
session_start();

// Database configuration - adjust if needed
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'eventify_db');

// Connect to database
function getDB() {
    static $db = null;
    if ($db === null) {
        try {
            $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // If database doesn't exist, return null (graceful fallback)
            return null;
        }
    }
    return $db;
}

// Auth functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isOrganizer() {
    return isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'organizer' || $_SESSION['user_role'] === 'admin');
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        setFlash('error', 'Admin access required.');
        header('Location: index.php');
        exit;
    }
}

function requireAuth() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

function requireOrganizer() {
    requireAuth();
    if (!isOrganizer()) {
        setFlash('error', 'Organizer access required.');
        header('Location: index.php');
        exit;
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = getDB();
    if (!$db) return ['id' => $_SESSION['user_id'] ?? 0, 'full_name' => $_SESSION['user_name'] ?? 'User', 'email' => $_SESSION['user_email'] ?? '', 'role' => $_SESSION['user_role'] ?? 'user'];
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: ['id' => $_SESSION['user_id'], 'full_name' => $_SESSION['user_name'], 'email' => $_SESSION['user_email'], 'role' => $_SESSION['user_role']];
}

// Flash messages
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Data functions (fallback if no DB)
function getCategories() {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->query("SELECT * FROM categories ORDER BY name");
            $cats = $stmt->fetchAll();
            if ($cats) return $cats;
        } catch (PDOException $e) {}
    }
    // Fallback static data
    return [
        ['id' => 1, 'name' => 'Sports & Gaming', 'icon' => 'gamepad', 'event_count' => 42, 'theme' => 'cat-sports', 'slug' => 'sports-gaming'],
        ['id' => 2, 'name' => 'Music & Live Concerts', 'icon' => 'music', 'event_count' => 120, 'theme' => 'cat-music', 'slug' => 'music'],
        ['id' => 3, 'name' => 'Dance & After Party', 'icon' => 'sparkles', 'event_count' => 68, 'theme' => 'cat-dance', 'slug' => 'dance-party'],
        ['id' => 4, 'name' => 'Fashion', 'icon' => 'shirt', 'event_count' => 35, 'theme' => 'cat-fashion', 'slug' => 'fashion'],
        ['id' => 5, 'name' => 'Comedy', 'icon' => 'laugh', 'event_count' => 29, 'theme' => 'cat-comedy', 'slug' => 'comedy'],
        ['id' => 6, 'name' => 'Food & Drinks', 'icon' => 'utensils', 'event_count' => 54, 'theme' => 'cat-food', 'slug' => 'food-drinks']
    ];
}

function getFeaturedEvents() {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->query("SELECT e.*, c.name as category_name, c.slug as category_slug FROM events e LEFT JOIN categories c ON e.category_id = c.id WHERE e.status = 'active' AND e.is_featured = 1 ORDER BY e.date LIMIT 6");
            $events = $stmt->fetchAll();
            if ($events) return $events;
        } catch (PDOException $e) {}
    }
    return [];
}

function getAllEvents($filters = []) {
    $db = getDB();
    if ($db) {
        try {
            $sql = "SELECT e.*, c.name as category_name FROM events e LEFT JOIN categories c ON e.category_id = c.id WHERE e.status = 'active'";
            $params = [];
            if (!empty($filters['category'])) {
                $sql .= " AND c.name = ?";
                $params[] = $filters['category'];
            }
            if (!empty($filters['location'])) {
                $sql .= " AND e.location LIKE ?";
                $params[] = '%' . $filters['location'] . '%';
            }
            if (!empty($filters['search'])) {
                $sql .= " AND (e.title LIKE ? OR e.description LIKE ?)";
                $params[] = '%' . $filters['search'] . '%';
                $params[] = '%' . $filters['search'] . '%';
            }
            $sql .= " ORDER BY e.date";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $events = $stmt->fetchAll();
            if ($events) return $events;
        } catch (PDOException $e) {}
    }
    return [];
}

function getEventById($id) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT e.*, c.name as category_name, u.full_name as organizer_name FROM events e LEFT JOIN categories c ON e.category_id = c.id LEFT JOIN users u ON e.organizer_id = u.id WHERE e.id = ?");
            $stmt->execute([$id]);
            $event = $stmt->fetch();
            if ($event) return $event;
        } catch (PDOException $e) {}
    }
    return null;
}

function getUserEvents($userId) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT * FROM events WHERE organizer_id = ? ORDER BY date DESC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {}
    }
    return [];
}

function getUserTickets($userId) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT t.*, e.title as event_title, e.date as event_date, e.location as event_location, e.image_url FROM tickets t JOIN events e ON t.event_id = e.id WHERE t.user_id = ? ORDER BY t.created_at DESC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {}
    }
    return [];
}

function getUserLikes($userId) {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->prepare("SELECT event_id FROM likes WHERE user_id = ?");
            $stmt->execute([$userId]);
            return array_column($stmt->fetchAll(), 'event_id');
        } catch (PDOException $e) {}
    }
    return [];
}

function getStats() {
    $db = getDB();
    $stats = ['users' => 0, 'events' => 0, 'tickets' => 0];
    if ($db) {
        try {
            $stats['users'] = $db->query("SELECT COUNT(*) FROM users")->fetchColumn() ?: 0;
            $stats['events'] = $db->query("SELECT COUNT(*) FROM events")->fetchColumn() ?: 0;
            $stats['tickets'] = $db->query("SELECT COUNT(*) FROM tickets")->fetchColumn() ?: 0;
        } catch (PDOException $e) {}
    }
    return $stats;
}

function getTestimonials() {
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->query("SELECT * FROM testimonials ORDER BY id");
            $t = $stmt->fetchAll();
            if ($t) return $t;
        } catch (PDOException $e) {}
    }
    return [
        ['id' => 1, 'name' => 'Brian Omondi', 'role' => 'University Student & Gamer', 'avatar_url' => 'images/avatars/brian-omondi.jpg', 'quote' => 'Eventify makes it super easy for me to find local FIFA tournaments and hiphop gigs. Booking is lightning fast via M-Pesa!', 'rating' => 5],
        ['id' => 2, 'name' => 'Amani Mwangi', 'role' => 'Young Marketing Professional', 'avatar_url' => 'images/avatars/amani-mwangi.jpg', 'quote' => 'I love the new dark theme and glowing interface! It makes browsing upcoming fashion events feel so exciting.', 'rating' => 4.9],
        ['id' => 3, 'name' => 'David Kilonzo', 'role' => 'Lead Coordinator', 'company' => 'Nairobi Creative Group', 'avatar_url' => 'images/avatars/david-kilonzo.jpg', 'quote' => 'Hosting the Nairobi Summer Fest on Eventify was a game-changer. We sold out in record time!', 'rating' => 5]
    ];
}

function createEvent($data) {
    $db = getDB();
    if (!$db) return false;
    try {
        $stmt = $db->prepare("INSERT INTO events (title, description, date, time, location, price, category_id, image_url, organizer_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
        return $stmt->execute([
            $data['title'], $data['description'], $data['date'], $data['time'],
            $data['location'], $data['price'], $data['category_id'],
            $data['image_url'], $data['organizer_id']
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

function updateEvent($id, $data) {
    $db = getDB();
    if (!$db) return false;
    try {
        $fields = [];
        $values = [];
        foreach ($data as $key => $val) {
            $fields[] = "$key = ?";
            $values[] = $val;
        }
        $values[] = $id;
        $stmt = $db->prepare("UPDATE events SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($values);
    } catch (PDOException $e) {
        return false;
    }
}

function deleteEvent($id) {
    $db = getDB();
    if (!$db) return false;
    try {
        $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        return false;
    }
}

function updateProfile($userId, $data) {
    $db = getDB();
    if (!$db) return false;
    try {
        $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
        return $stmt->execute([$data['full_name'], $data['email'], $data['phone'], $userId]);
    } catch (PDOException $e) {
        return false;
    }
}

// Page title
$pageTitle = isset($pageTitle) ? $pageTitle : 'Eventify';
$activePage = isset($activePage) ? $activePage : '';
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
</head>
<body>

  <!-- Navbar -->
  <header class="navbar scrolled" id="navbar">
    <div class="container navbar-container">
      <a href="index.php" class="logo">
        <svg class="logo-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
          <line x1="16" y1="2" x2="16" y2="6"></line>
          <line x1="8" y1="2" x2="8" y2="6"></line>
          <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        <span class="logo-text"><span class="logo-blue">Event</span><span class="logo-orange">ify</span></span>
      </a>

      <nav class="desktop-nav">
        <ul class="nav-links">
          <li><a href="index.php" class="<?php echo $activePage === 'home' ? 'active' : ''; ?>">Home</a></li>
          <li><a href="index.php#events">Events</a></li>
          <li><a href="index.php#categories">Categories</a></li>
          <li><a href="index.php#about">About</a></li>
        </ul>
      </nav>

      <div class="auth-buttons">
        <?php if (isLoggedIn()): ?>
          <a href="dashboard.php" class="btn-link"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></a>
          <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn-link">Login</a>
          <a href="register.php" class="btn btn-primary btn-sm">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Flash messages -->
  <?php $flash = getFlash(); if ($flash): ?>
  <div style="position:fixed;top:80px;left:50%;transform:translateX(-50%);z-index:9999;max-width:400px;width:90%;">
    <div style="padding:16px 24px;border-radius:12px;font-weight:600;font-size:0.9rem;<?php echo $flash['type'] === 'success' ? 'background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:#34D399;' : 'background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#F87171;'; ?>">
      <?php echo htmlspecialchars($flash['message']); ?>
    </div>
  </div>
  <?php endif; ?>