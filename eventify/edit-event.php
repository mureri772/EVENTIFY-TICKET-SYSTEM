<?php
/**
 * Eventify - Edit Event Page
 */
require_once 'includes/header.php';
requireOrganizer();

$eventId = intval($_GET['id'] ?? 0);
$event = $eventId > 0 ? getEventById($eventId) : null;

if (!$event || $event['organizer_id'] != $_SESSION['user_id']) {
    setFlash('error', 'Event not found or access denied.');
    header('Location: dashboard.php#myevents');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update';

    if ($action === 'delete') {
        deleteEvent($eventId);
        setFlash('success', 'Event deleted successfully.');
        header('Location: dashboard.php#myevents');
        exit;
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $price = intval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $status = $_POST['status'] ?? 'active';

    if (empty($title) || empty($date) || empty($time) || empty($location) || $category_id <= 0) {
        $error = 'Please fill in all required fields.';
    } else {
        $updateData = [
            'title' => $title,
            'description' => $description,
            'date' => $date,
            'time' => $time,
            'location' => $location,
            'price' => $price,
            'category_id' => $category_id,
            'status' => $status
        ];

        // Handle image upload
        if (!empty($_FILES['image']['tmp_name'])) {
            $uploadDir = 'images/events/';
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('event_') . '.' . $ext;
            $targetPath = $uploadDir . $filename;

            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($ext), $allowed)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $updateData['image_url'] = $targetPath;
                }
            }
        }

        if (updateEvent($eventId, $updateData)) {
            $success = 'Event updated successfully.';
            $event = getEventById($eventId); // Refresh
        } else {
            $error = 'Failed to update event.';
        }
    }
}

$categories = getCategories();
$pageTitle = 'Edit Event - Eventify';
?>

<style>
.edit-container {
  padding: 120px 0 80px;
  min-height: 100vh;
}
.edit-card {
  max-width: 700px;
  background: rgba(17, 24, 39, 0.7);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 24px;
  padding: 48px;
  backdrop-filter: blur(20px);
  margin: 0 auto;
}
.edit-title {
  font-size: 1.8rem;
  font-weight: 800;
  margin-bottom: 8px;
}
.edit-subtitle {
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
.form-textarea {
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
  resize: vertical;
  min-height: 120px;
}
.form-textarea:focus {
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
  appearance: none;
  background-image: url("data:image/svg+xml;utf8,<svg fill='%2364748b' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 40px;
}
.form-select:focus {
  border-color: rgba(37, 99, 235, 0.5);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}
.form-select option { background: #111827; color: #fff; }
.current-image {
  width: 100%;
  max-width: 300px;
  border-radius: 12px;
  margin-bottom: 16px;
}
.delete-section {
  margin-top: 40px;
  padding-top: 32px;
  border-top: 1px solid rgba(239, 68, 68, 0.2);
}
@media (max-width: 768px) {
  .edit-card { padding: 32px 24px; }
}
</style>

<div class="edit-container">
  <div class="container">
    <div class="edit-card">
      <a href="dashboard.php#myevents" style="display:inline-flex; align-items:center; gap:8px; color:var(--text-muted); font-size:0.9rem; margin-bottom:16px; text-decoration:none;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Back to Dashboard
      </a>
      <h1 class="edit-title">Edit Event</h1>
      <p class="edit-subtitle">Update your event details</p>

      <?php if ($success): ?>
      <div class="success-msg"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
      <div class="form-error-msg"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" action="edit-event.php?id=<?php echo $eventId; ?>" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update">

        <div class="form-group">
          <label class="form-label">Current Image</label>
          <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="" class="current-image">
        </div>

        <div class="form-group">
          <label class="form-label" for="title">Event Title *</label>
          <input type="text" id="title" name="title" class="form-input" required value="<?php echo htmlspecialchars($event['title']); ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="description">Description</label>
          <textarea id="description" name="description" class="form-textarea"><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="date">Date *</label>
            <input type="date" id="date" name="date" class="form-input" required value="<?php echo $event['date']; ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="time">Time *</label>
            <input type="time" id="time" name="time" class="form-input" required value="<?php echo $event['time']; ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="location">Location *</label>
            <input type="text" id="location" name="location" class="form-input" required value="<?php echo htmlspecialchars($event['location']); ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="price">Ticket Price (KES) *</label>
            <input type="number" id="price" name="price" class="form-input" min="0" required value="<?php echo $event['price']; ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="category_id">Category *</label>
            <select id="category_id" name="category_id" class="form-select" required>
              <?php foreach ($categories as $cat): ?>
              <option value="<?php echo $cat['id']; ?>" <?php echo $event['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select">
              <option value="active" <?php echo $event['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
              <option value="sold_out" <?php echo $event['status'] === 'sold_out' ? 'selected' : ''; ?>>Sold Out</option>
              <option value="cancelled" <?php echo $event['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="image">Change Image</label>
          <div class="file-input-wrapper">
            <input type="file" id="image" name="image" accept="image/*" onchange="updateFileName(this)">
            <label for="image" class="file-input-label" id="fileLabel" style="padding: 20px;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
              <span>Upload new image (optional)</span>
            </label>
          </div>
        </div>

        <div style="margin-top: 32px; display: flex; gap: 12px;">
          <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            Save Changes
          </button>
          <a href="dashboard.php#myevents" class="btn btn-secondary" style="flex:1; justify-content:center">Cancel</a>
        </div>
      </form>

      <!-- Delete Section -->
      <div class="delete-section">
        <h3 style="color: #EF4444; font-size: 1.1rem; margin-bottom: 8px;">Danger Zone</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 16px;">Once deleted, this event cannot be recovered.</p>
        <form method="POST" action="edit-event.php?id=<?php echo $eventId; ?>" onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
          <input type="hidden" name="action" value="delete">
          <button type="submit" class="btn" style="background: rgba(239,68,68,0.1); color: #EF4444; border: 1px solid rgba(239,68,68,0.2);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            Delete Event
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function updateFileName(input) {
  const label = document.getElementById('fileLabel');
  if (input.files && input.files[0]) {
    label.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg><span>' + input.files[0].name + '</span>';
    label.style.borderColor = 'rgba(16, 185, 129, 0.4)';
    label.style.color = '#34D399';
  }
}
</script>

<?php require_once 'includes/footer.php'; ?>