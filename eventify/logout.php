<?php
/**
 * Eventify - Logout
 */
require_once 'includes/auth.php';
logoutUser();
setFlash('success', 'You have been logged out successfully.');
header('Location: index.php');
exit;
