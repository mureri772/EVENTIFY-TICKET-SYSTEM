<?php
/**
 * Eventify - Auth Functions
 * This file is included by login.php and register.php
 * Do NOT include header.php here - those pages have their own layout.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database functions from header.php without outputting HTML
require_once __DIR__ . '/header.php';

function loginUser($email, $password) {
    $db = getDB();
    if (!$db) {
        // Demo fallback - ensure admin role is 'admin'
        if ($email === 'admin@eventify.co.ke' && $password === 'admin123') {
            $_SESSION['user_id'] = 1;
            $_SESSION['user_name'] = 'Admin User';
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'admin';  // FIXED: was 'organizer'
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Database connection failed.'];
    }
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    
    return ['success' => true];
}

function registerUser($fullName, $email, $password, $phone = '', $role = 'user') {
    $db = getDB();
    if (!$db) {
        // Demo fallback - just log them in
        $_SESSION['user_id'] = rand(1000, 9999);
        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;
        return ['success' => true];
    }
    
    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already registered.'];
    }
    
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (full_name, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$fullName, $email, $phone, $hash, $role]);
    
    $_SESSION['user_id'] = $db->lastInsertId();
    $_SESSION['user_name'] = $fullName;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_role'] = $role;
    
    return ['success' => true];
}

function logoutUser() {
    session_destroy();
    $_SESSION = [];
}

function changePassword($userId, $current, $new) {
    $db = getDB();
    if (!$db) {
        return ['success' => true, 'message' => 'Password updated (demo mode).'];
    }
    
    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($current, $user['password'])) {
        return ['success' => false, 'message' => 'Current password is incorrect.'];
    }
    
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hash, $userId]);
    
    return ['success' => true, 'message' => 'Password updated successfully.'];
}