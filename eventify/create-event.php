<?php
/**
 * Eventify - Create Event Page
 */
require_once 'includes/header.php';
requireOrganizer();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $price = intval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);

    if (empty($title) || empty($date) || empty($time) || empty($location) || $price < 0 || $category_id <= 0) {
        $error = 'Please fill in all required fields.';
    } else {
        $imageUrl = 'images/events/default.jpg';

        // Handle image upload
        if (!empty($_FILES['image']['tmp_name'])) {
            $uploadDir = 'images/events/';
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('event_') . '.' . $ext;
            $targetPath = $uploadDir . $filename;

            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($ext), $allowed)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imageUrl = $targetPath;
                }
            }
        }

        if (createEvent([
            'title' => $title,
            'description' => $description,
            'date' => $date,
            'time' => $time,
            'location' => $location,
            'price' => $price,
            'category_id' => $category_id,
            'image_url' => $imageUrl,
            'organizer_id' => $_SESSION['user_id']
        ])) {
            setFlash('success', 'Event created successfully!');
            header('Location: dashboard.php#myevents');
            exit;
        } else {
            $error = 'Failed to create event. Please try again.';
        }
    }
}

$categories = getCategories();
$pageTitle = 'Create Event - Eventify';
?>

<style>
.create-container {
  padding: 120px 0 80px;
  min-height: 100vh;
}
.create-card {
  max-width: 700px;
  background: rgba(17, 24, 39, 0.7);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 24px;
  padding: 48px;
  backdrop-filter: blur(20px);
  margin: 0 auto;
}
.create-title {
  font-size: 1.8rem;
  font-weight: 800;
  margin-bottom: 8px;
}
.create-subtitle {
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
.file-input-wrapper {
  position: relative;
  overflow: hidden;
  display: inline-block;
  width: 100%;
}
.file-input-wrapper input[type=file] {
  position: absolute;
  left: -9999px;
}
.file-input-label {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 40px;
  background: rgba(255,255,255,0.02);
  border: 2px dashed rgba(255,255,255,0.1);
  border-radius: 16px;
  color: var(--text-muted);
  cursor: pointer;
  transition: all 0.25s;
  text-align: center;
}
.file-input-label:hover {
  border-color: rgba(37, 99, 235, 0.4);
  background: rgba(37, 99, 235, 0.03);
}
@media (max-width: 768px) {
  .create-card { padding: 32px 24px; }
}
</style>

<div class="create-container">
  <div class="container">
    <div class="create-card">
      <a href="dashboard.php" style="display:inline-flex; align-items:center; gap:8px; color:var(--text-muted); font-size:0.9rem; margin-bottom:16px; text-decoration:none;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Back to Dashboard
      </a>
      <h1 class="create-title">Create New Event</h1>
      <p class="create-subtitle">Fill in the details to publish your event</p>

      <?php if ($error): ?>
      <div class="form-error-msg"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" action="create-event.php" enctype="multipart/form-data">
        <div class="form-group">
          <label class="form-label" for="title">Event Title *</label>
          <input type="text" id="title" name="title" class="form-input" placeholder="e.g. Nairobi Summer Fest" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
        </div>

        <div class="form-group">
          <label class="form-label" for="description">Description</label>
          <textarea id="description" name="description" class="form-textarea" placeholder="Describe your event..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="date">Date *</label>
            <input type="date" id="date" name="date" class="form-input" required value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="time">Time *</label>
            <input type="time" id="time" name="time" class="form-input" required value="<?php echo htmlspecialchars($_POST['time'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="location">Location *</label>
            <input type="text" id="location" name="location" class="form-input" placeholder="e.g. Ngong Racecourse, Lang'ata" required value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
          </div>
          <div class="form-group">
            <label class="form-label" for="price">Ticket Price (KES) *</label>
            <input type="number" id="price" name="price" class="form-input" placeholder="e.g. 2500" min="0" required value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="category_id">Category *</label>
            <select id="category_id" name="category_id" class="form-select" required>
              <option value="">Select a category</option>
              <?php foreach ($categories as $cat): ?>
              <option value="<?php echo $cat['id']; ?>" <?php echo ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="image">Event Image</label>
            <div class="file-input-wrapper">
              <input type="file" id="image" name="image" accept="image/*" onchange="updateFileName(this)">
              <label for="image" class="file-input-label" id="fileLabel">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                <span>Click to upload event image</span>
              </label>
            </div>
          </div>
        </div>

        <div style="margin-top: 32px; display: flex; gap: 12px;">
          <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Create Event
          </button>
          <a href="dashboard.php" class="btn btn-secondary" style="flex:1; justify-content:center">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function updateFileName(input) {
  const label = document.getElementById('fileLabel');
  if (input.files && input.files[0]) {
    label.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg><span>' + input.files[0].name + '</span>';
    label.style.borderColor = 'rgba(16, 185, 129, 0.4)';
    label.style.color = '#34D399';
  }
}
</script>

<?php require_once 'includes/footer.php'; ?>
