<?php
/**
 * Eventify - Database Setup Script
 * Run this once to create the database and tables, then delete it for security.
 */

echo "<h1>Eventify Database Setup</h1>";
echo "<pre>";

try {
    // Connect to MySQL server (without selecting a database)
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS eventify_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "[OK] Database 'eventify_db' created or already exists.\n";

    // Select the database
    $pdo->exec("USE eventify_db");

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        phone VARCHAR(50) DEFAULT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'organizer', 'admin') DEFAULT 'user',
        avatar_url VARCHAR(255) DEFAULT 'images/avatars/default.jpg',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "[OK] Table 'users' created.\n";

    // Create categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        icon VARCHAR(50) DEFAULT 'sparkles',
        event_count INT DEFAULT 0,
        theme VARCHAR(50) DEFAULT 'cat-dance',
        slug VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "[OK] Table 'categories' created.\n";

    // Create events table
    $pdo->exec("CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        date DATE NOT NULL,
        time TIME NOT NULL,
        location VARCHAR(255) NOT NULL,
        price INT NOT NULL DEFAULT 0,
        category_id INT,
        image_url VARCHAR(255) DEFAULT 'images/events/default.jpg',
        organizer_id INT,
        status ENUM('active', 'sold_out', 'cancelled', 'pending', 'completed') DEFAULT 'active',
        is_featured TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
        FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "[OK] Table 'events' created.\n";

    // Create tickets table
    $pdo->exec("CREATE TABLE IF NOT EXISTS tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        event_id INT NOT NULL,
        quantity INT NOT NULL DEFAULT 1,
        total_price INT NOT NULL DEFAULT 0,
        ticket_code VARCHAR(50) UNIQUE,
        payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "[OK] Table 'tickets' created.\n";

    // Create likes table
    $pdo->exec("CREATE TABLE IF NOT EXISTS likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        event_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_like (user_id, event_id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    echo "[OK] Table 'likes' created.\n";

    // Create testimonials table
    $pdo->exec("CREATE TABLE IF NOT EXISTS testimonials (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        role VARCHAR(255) NOT NULL,
        company VARCHAR(255) DEFAULT NULL,
        avatar_url VARCHAR(255) DEFAULT 'images/avatars/default.jpg',
        quote TEXT NOT NULL,
        rating DECIMAL(2,1) DEFAULT 5.0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "[OK] Table 'testimonials' created.\n";

    // Insert default categories
    $categories = [
        ['Sports & Gaming', 'gamepad', 42, 'cat-sports', 'sports-gaming'],
        ['Music & Live Concerts', 'music', 120, 'cat-music', 'music'],
        ['Dance & After Party', 'sparkles', 68, 'cat-dance', 'dance-party'],
        ['Fashion', 'shirt', 35, 'cat-fashion', 'fashion'],
        ['Comedy', 'laugh', 29, 'cat-comedy', 'comedy'],
        ['Food & Drinks', 'utensils', 54, 'cat-food', 'food-drinks']
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, icon, event_count, theme, slug) VALUES (?, ?, ?, ?, ?)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    echo "[OK] Default categories inserted.\n";

    // Insert admin user
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Admin User', 'admin@eventify.co.ke', '+254700000000', $hash, 'admin']);
    echo "[OK] Admin user created.\n";

    // Insert default testimonials
    $testimonials = [
        ['Brian Omondi', 'University Student & Gamer', null, 'images/avatars/brian-omondi.jpg', 'Eventify makes it super easy for me to find local FIFA tournaments and hiphop gigs. Booking is lightning fast via M-Pesa!', 5],
        ['Amani Mwangi', 'Young Marketing Professional', null, 'images/avatars/amani-mwangi.jpg', 'I love the new dark theme and glowing interface! It makes browsing upcoming fashion events feel so exciting.', 4.9],
        ['David Kilonzo', 'Lead Coordinator', 'Nairobi Creative Group', 'images/avatars/david-kilonzo.jpg', 'Hosting the Nairobi Summer Fest on Eventify was a game-changer. We sold out in record time!', 5]
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO testimonials (name, role, company, avatar_url, quote, rating) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($testimonials as $t) {
        $stmt->execute($t);
    }
    echo "[OK] Default testimonials inserted.\n";

    // Insert sample events
    $events = [
        ['Nairobi Summer Fest', 'Nairobi\'s biggest summer music event featuring the best of Afrobeats, Amapiano, and live band performances under the stars.', '2026-07-18', '14:00:00', "Ngong Racecourse, Lang'ata", 2500, 2, 'images/events/nairobi-summer-fest.jpg', 1, 'active', 1],
        ['FC26 FIFA Tournament', 'Compete with Nairobi\'s top FIFA players for a grand prize pool. Experience a premium pro gaming setup, food, and massive screens.', '2026-07-25', '10:00:00', 'Triclub Arena, Westlands', 1000, 1, 'images/events/fifa-tournament.jpg', 1, 'active', 1],
        ['Nairobi Fashion Night', 'A stunning showcase of contemporary African fashion, runway designs, and local designer exhibitions featuring Kenya\'s top models.', '2026-08-08', '18:00:00', 'The Alchemist, Westlands', 2000, 4, 'images/events/fashion-night.jpg', 1, 'active', 1],
        ['Stand Up Fridays', 'Prepare for a night of rib-cracking comedy featuring Kenya\'s top stand-up comedians and exciting open mic talents.', '2026-08-14', '19:30:00', "Carnivore Grounds, Lang'ata", 800, 5, 'images/events/comedy-show.jpg', 1, 'active', 1],
        ['Food & Vibes Festival', 'Sample diverse local and international culinary delights, craft drinks, and specialty grills with live DJ sets in a beautiful outdoor venue.', '2026-08-29', '12:00:00', 'Waterfront Mall, Karen', 1500, 6, 'images/events/food-festival.jpg', 1, 'active', 1],
        ['Battle Zone Kenya Hiphop', 'The ultimate street cypher, epic hiphop dance battle, followed by an explosive after-party featuring Nairobi\'s top hiphop DJs.', '2026-09-12', '16:00:00', 'Siron Hotel, Rongai', 3000, 3, 'images/events/hiphop-party.jpg', 1, 'active', 1]
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO events (title, description, date, time, location, price, category_id, image_url, organizer_id, status, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($events as $evt) {
        $stmt->execute($evt);
    }
    echo "[OK] Sample events inserted.\n";

    echo "\n========================================\n";
    echo "SETUP COMPLETE!\n";
    echo "========================================\n";
    echo "Admin Login:\n";
    echo "  Email: admin@eventify.co.ke\n";
    echo "  Password: admin123\n";
    echo "\nIMPORTANT: Delete this file (setup-db.php) after setup!\n";
    echo "</pre>";

} catch (PDOException $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    echo "\nMake sure:\n";
    echo "1. MySQL/MariaDB is running\n";
    echo "2. Username 'root' has no password (or update the credentials in includes/header.php)\n";
    echo "3. PHP has PDO MySQL extension enabled\n";
}