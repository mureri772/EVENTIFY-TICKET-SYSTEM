<?php
/**
 * Eventify - Create Event (Organizer Only)
 */
require_once 'includes/header.php';
requireAuth();

// Restrict to organizers only
if (!isOrganizer()) {
    header('Location: dashboard.php');
    exit;
}

$currentUser = getCurrentUser();
$pageTitle = 'Create Event - Eventify';
$activePage = 'create-event';

$errors = [];
$successMsg = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $eventTime = trim($_POST['event_time'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $capacity = intval($_POST['capacity'] ?? 0);
    $status = trim($_POST['status'] ?? 'draft');

    // Basic validation
    if (empty($title)) $errors[] = 'Event title is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if (empty($category)) $errors[] = 'Category is required.';
    if (empty($eventDate)) $errors[] = 'Event date is required.';
    if (empty($eventTime)) $errors[] = 'Event time is required.';
    if (empty($location)) $errors[] = 'Location is required.';
    if ($price < 0) $errors[] = 'Price cannot be negative.';
    if ($capacity < 1) $errors[] = 'Capacity must be at least 1.';

    // Handle image upload
    $imageUrl = 'images/events/default.jpg';
    if (!empty($_FILES['event_image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $fileName = $_FILES['event_image']['name'];
        $fileTmp = $_FILES['event_image']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowed)) {
            $newName = uniqid('evt_') . '.' . $fileExt;
            $uploadPath = 'uploads/events/' . $newName;

            if (!is_dir('uploads/events')) {
                mkdir('uploads/events', 0755, true);
            }

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $imageUrl = $uploadPath;
            } else {
                $errors[] = 'Failed to upload image. Check folder permissions.';
            }
        } else {
            $errors[] = 'Invalid image format. Use JPG, PNG, or WEBP.';
        }
    }

    // If no errors, save to database (wire up your DB function here)
    if (empty($errors)) {
        // Example: $result = createEvent([
        //     'title' => $title,
        //     'description' => $description,
        //     'category' => $category,
        //     'date' => $eventDate . ' ' . $eventTime,
        //     'location' => $location,
        //     'price' => $price,
        //     'capacity' => $capacity,
        //     'image_url' => $imageUrl,
        //     'status' => $status,
        //     'organizer_id' => $_SESSION['user_id']
        // ]);
        
        // if ($result) {
        //     header('Location: dashboard.php?created=1');
        //     exit;
        // } else {
        //     $errors[] = 'Database error. Could not create event.';
        // }
        
        $successMsg = 'Event created successfully! (Wire up your database save logic in the PHP section.)';
    }
}
?>

<style>
.create-container {
  padding: 120px 0 60px;
  min-height: 100vh;
}
.create-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 40px;
  gap: 20px;
  flex-wrap: wrap;
}
.create-header h1 {
  font-size: 1.6rem;
  font-weight: 700;
}
.create-header p {
  color: var(--text-muted);
  font-size: 0.95rem;
  margin-top: 4px;
}
.create-grid {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 32px;
}
.create-sidebar {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.sidebar-card {
  background: rgba(17, 24, 39, 0.7);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 16px;
  padding: 24px;
  backdrop-filter: blur(20px);
}
.sidebar-nav {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.sidebar-nav a {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 12px;
  color: var(--text-secondary);
  font-weight: 500;
  font-size: 0.95rem;
  transition: all 0.25s;
  text-decoration: none;
}
.sidebar-nav a:hover, .sidebar-nav a.active {
  background: rgba(37, 99, 235, 0.1);
  color: var(--text-primary);
}
.sidebar-nav a.active {
  border: 1px solid rgba(37, 99, 235, 0.2);
}
.sidebar-nav a svg { flex-shrink: 0; }
.sidebar-section-title {
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--text-muted);
  padding: 16px 16px 8px;
}
.create-content {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.content-card {
  background: rgba(17, 24, 39, 0.7);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 20px;
  padding: 32px;
  backdrop-filter: blur(20px);
}
.content-card-title {
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}
.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.form-group.full-width {
  grid-column: 1 / -1;
}
.form-group label {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-secondary);
}
.form-group input,
.form-group select,
.form-group textarea {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px;
  padding: 12px 16px;
  color: var(--text-primary);
  font-size: 0.95rem;
  font-family: inherit;
  transition: all 0.2s;
  width: 100%;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: rgba(37, 99, 235, 0.4);
  background: rgba(255,255,255,0.05);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}
.form-group input::placeholder,
.form-group textarea::placeholder {
  color: var(--text-muted);
}
.form-group textarea {
  resize: vertical;
  min-height: 120px;
}
.form-group select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
  padding-right: 40px;
}
.form-group select option {
  background: #111827;
  color: var(--text-primary);
}
.image-upload-zone {
  border: 2px dashed rgba(255,255,255,0.1);
  border-radius: 16px;
  padding: 40px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  position: relative;
  overflow: hidden;
}
.image-upload-zone:hover {
  border-color: rgba(37, 99, 235, 0.4);
  background: rgba(37, 99, 235, 0.03);
}
.image-upload-zone.has-image {
  border-style: solid;
  border-color: rgba(37, 99, 235, 0.3);
  padding: 0;
}
.image-upload-zone input[type="file"] {
  position: absolute;
  inset: 0;
  opacity: 0;
  cursor: pointer;
  width: 100%;
  height: 100%;
}
.upload-preview {
  width: 100%;
  height: 220px;
  object-fit: cover;
  border-radius: 14px;
  display: none;
}
.image-upload-zone.has-image .upload-preview {
  display: block;
}
.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  color: var(--text-muted);
}
.image-upload-zone.has-image .upload-placeholder {
  display: none;
}
.upload-placeholder svg {
  opacity: 0.5;
}
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 8px;
  padding-top: 24px;
  border-top: 1px solid rgba(255,255,255,0.06);
}
.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.95rem;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-primary {
  background: linear-gradient(135deg, #2563EB, #1D4ED8);
  color: #fff;
  box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
}
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
}
.btn-secondary {
  background: rgba(255,255,255,0.05);
  color: var(--text-secondary);
  border: 1px solid rgba(255,255,255,0.08);
}
.btn-secondary:hover {
  background: rgba(255,255,255,0.08);
  color: var(--text-primary);
}
.alert {
  padding: 14px 18px;
  border-radius: 12px;
  font-size: 0.9rem;
  margin-bottom: 20px;
}
.alert-error {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  color: #F87171;
}
.alert-success {
  background: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.2);
  color: #34D399;
}
.price-input-wrapper {
  position: relative;
}
.price-input-wrapper::before {
  content: 'KES';
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--text-muted);
  font-size: 0.85rem;
  font-weight: 600;
}
.price-input-wrapper input {
  padding-left: 52px !important;
}
.mobile-toggle {
  display: none;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  color: var(--text-primary);
  padding: 10px 16px;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 600;
  align-items: center;
  gap: 8px;
}
@media (max-width: 1024px) {
  .create-grid { grid-template-columns: 1fr; }
  .create-sidebar {
    position: fixed;
    left: -300px;
    top: 0;
    height: 100vh;
    width: 280px;
    z-index: 1000;
    background: rgba(11, 15, 25, 0.98);
    padding: 100px 20px 20px;
    transition: left 0.3s ease;
    overflow-y: auto;
  }
  .create-sidebar.open { left: 0; }
  .mobile-toggle { display: inline-flex; }
  .form-grid { grid-template-columns: 1fr; }
}
.sidebar-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 999;
}
.sidebar-overlay.active { display: block; }
</style>

<div class="create-container">
  <div class="container">
    <!-- Header -->
    <div class="create-header">
      <div>
        <h1>Create New Event</h1>
        <p>Fill in the details below to publish your event</p>
      </div>
      <button class="mobile-toggle" onclick="toggleSidebar()">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        Menu
      </button>
    </div>

    <div class="create-grid">
      <!-- Sidebar -->
      <aside class="create-sidebar" id="createSidebar">
        <div class="sidebar-card">
          <ul class="sidebar-nav">
            <li><a href="dashboard.php">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
              Overview
            </a></li>
            <li><a href="dashboard.php#tickets">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
              My Tickets
            </a></li>
            
            <li class="sidebar-section-title">Organizer</li>
            <li><a href="create-event.php" class="active">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              Create Event
            </a></li>
            <li><a href="dashboard.php#myevents">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              My Events
            </a></li>
            
            <?php if (isAdmin()): ?>
            <li class="sidebar-section-title">Administration</li>
            <li><a href="admin.php">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
              Admin Panel
            </a></li>
            <?php endif; ?>
            
            <li class="sidebar-section-title">Account</li>
            <li><a href="dashboard.php#liked">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
              Liked Events
            </a></li>
            <li><a href="settings.php">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
              Settings
            </a></li>
            <li><a href="logout.php" style="color: #EF4444;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
              Logout
            </a></li>
          </ul>
        </div>
      </aside>

      <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

      <!-- Main Form -->
      <div class="create-content">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <strong>Please fix the following:</strong>
          <ul style="margin-top: 8px; padding-left: 18px;">
            <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <?php if ($successMsg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="content-card">
          <h2 class="content-card-title">Event Details</h2>
          
          <div class="form-grid">
            <!-- Event Title -->
            <div class="form-group full-width">
              <label for="title">Event Title *</label>
              <input type="text" id="title" name="title" placeholder="e.g. Nairobi Tech Summit 2026" 
                     value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
            </div>

            <!-- Description -->
            <div class="form-group full-width">
              <label for="description">Description *</label>
              <textarea id="description" name="description" placeholder="Describe your event, what attendees can expect, schedule, etc..." required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
            </div>

            <!-- Category -->
            <div class="form-group">
              <label for="category">Category *</label>
              <select id="category" name="category" required>
                <option value="">Select category</option>
                <option value="Music" <?php echo ($_POST['category'] ?? '') === 'Music' ? 'selected' : ''; ?>>Music</option>
                <option value="Technology" <?php echo ($_POST['category'] ?? '') === 'Technology' ? 'selected' : ''; ?>>Technology</option>
                <option value="Business" <?php echo ($_POST['category'] ?? '') === 'Business' ? 'selected' : ''; ?>>Business</option>
                <option value="Sports" <?php echo ($_POST['category'] ?? '') === 'Sports' ? 'selected' : ''; ?>>Sports</option>
                <option value="Food & Drink" <?php echo ($_POST['category'] ?? '') === 'Food & Drink' ? 'selected' : ''; ?>>Food & Drink</option>
                <option value="Arts" <?php echo ($_POST['category'] ?? '') === 'Arts' ? 'selected' : ''; ?>>Arts</option>
                <option value="Education" <?php echo ($_POST['category'] ?? '') === 'Education' ? 'selected' : ''; ?>>Education</option>
                <option value="Networking" <?php echo ($_POST['category'] ?? '') === 'Networking' ? 'selected' : ''; ?>>Networking</option>
              </select>
            </div>

            <!-- Location -->
            <div class="form-group">
              <label for="location">Location *</label>
              <input type="text" id="location" name="location" placeholder="e.g. KICC, Nairobi" 
                     value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" required>
            </div>

            <!-- Date -->
            <div class="form-group">
              <label for="event_date">Event Date *</label>
              <input type="date" id="event_date" name="event_date" 
                     value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>" required>
            </div>

            <!-- Time -->
            <div class="form-group">
              <label for="event_time">Start Time *</label>
              <input type="time" id="event_time" name="event_time" 
                     value="<?php echo htmlspecialchars($_POST['event_time'] ?? ''); ?>" required>
            </div>

            <!-- Price -->
            <div class="form-group">
              <label for="price">Ticket Price (KES) *</label>
              <div class="price-input-wrapper">
                <input type="number" id="price" name="price" min="0" step="1" placeholder="0" 
                       value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
              </div>
            </div>

            <!-- Capacity -->
            <div class="form-group">
              <label for="capacity">Total Capacity *</label>
              <input type="number" id="capacity" name="capacity" min="1" step="1" placeholder="100" 
                     value="<?php echo htmlspecialchars($_POST['capacity'] ?? ''); ?>" required>
            </div>

            <!-- Status -->
            <div class="form-group">
              <label for="status">Event Status</label>
              <select id="status" name="status">
                <option value="draft" <?php echo ($_POST['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                <option value="active" <?php echo ($_POST['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
              </select>
            </div>

            <!-- Image Upload -->
            <div class="form-group full-width">
              <label>Event Cover Image</label>
              <div class="image-upload-zone" id="uploadZone" onclick="document.getElementById('eventImage').click()">
                <input type="file" id="eventImage" name="event_image" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)">
                <img src="" alt="Preview" class="upload-preview" id="imagePreview">
                <div class="upload-placeholder">
                  <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                  <div>
                    <strong>Click to upload</strong> or drag and drop<br>
                    <span style="font-size: 0.8rem; opacity: 0.7;">JPG, PNG, WEBP up to 5MB</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              Create Event
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function previewImage(input) {
  const preview = document.getElementById('imagePreview');
  const zone = document.getElementById('uploadZone');
  
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
      zone.classList.add('has-image');
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Drag and drop support
const uploadZone = document.getElementById('uploadZone');
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
  uploadZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
  e.preventDefault();
  e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
  uploadZone.addEventListener(eventName, () => {
    uploadZone.style.borderColor = 'rgba(37, 99, 235, 0.6)';
    uploadZone.style.background = 'rgba(37, 99, 235, 0.05)';
  }, false);
});

['dragleave', 'drop'].forEach(eventName => {
  uploadZone.addEventListener(eventName, () => {
    uploadZone.style.borderColor = '';
    uploadZone.style.background = '';
  }, false);
});

uploadZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
  const dt = e.dataTransfer;
  const files = dt.files;
  document.getElementById('eventImage').files = files;
  previewImage(document.getElementById('eventImage'));
}

// Mobile sidebar
function toggleSidebar() {
  document.getElementById('createSidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('active');
}
</script>

<?php require_once 'includes/footer.php'; ?>