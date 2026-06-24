<?php
// fix-admin.php - Run this once, then delete it
$db = new PDO('mysql:host=localhost;dbname=eventify_db;charset=utf8mb4', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$hash = password_hash('admin123', PASSWORD_DEFAULT);

// Delete old admin if exists (clean slate)
$db->prepare("DELETE FROM users WHERE email = ?")->execute(['admin@eventify.co.ke']);

// Insert fresh admin
$stmt = $db->prepare("INSERT INTO users (full_name, email, phone, password, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->execute(['Admin User', 'admin@eventify.co.ke', '+254700000000', $hash, 'organizer']);

echo "✅ DONE! Admin user created.<br>";
echo "Email: admin@eventify.co.ke<br>";
echo "Password: admin123<br>";
echo "Hash used: " . $hash . "<br>";
echo "<br>Delete this file now for security.";
?>