<?php
/**
 * Eventify - Event Detail Page
 */
require_once 'includes/header.php';

$eventId = intval($_GET['id'] ?? 0);
$event = $eventId > 0 ? getEventById($eventId) : null;

if (!$event) {
    setFlash('error', 'Event not found.');
    header('Location: index.php');
    exit;
}

$userLikes = isLoggedIn() ? getUserLikes($_SESSION['user_id']) : [];
$isLiked = in_array($event['id'], $userLikes);

$pageTitle = htmlspecialchars($event['title']) . ' - Eventify';

// Get related events
$db = getDB();
$stmt = $db->prepare("SELECT * FROM events WHERE category_id = ? AND id != ? AND status = 'active' LIMIT 3");
$stmt->execute([$event['category_id'], $event['id']]);
$relatedEvents = $stmt->fetchAll();
?>

<style>
.event-detail-hero {
  position: relative;
  padding: 140px 0 60px;
  overflow: hidden;
}
.event-detail-hero::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, var(--bg-body) 100%);
  z-index: 1;
}
.event-detail-bg {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background-size: cover;
  background-position: center;
  filter: blur(20px);
  opacity: 0.4;
  transform: scale(1.1);
}
.event-detail-container {
  position: relative;
  z-index: 2;
}
.event-detail-grid {
  display: grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap: 40px;
  align-items: start;
}
.event-detail-image {
  width: 100%;
  aspect-ratio: 16/10;
  object-fit: cover;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.5);
}
.event-detail-info {
  display: flex;
  flex-direction: column;
  gap: 24px;
}
.event-detail-category {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--primary-light);
  color: var(--primary);
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 700;
  width: fit-content;
  border: 1px solid rgba(37, 99, 235, 0.2);
}
.event-detail-title {
  font-size: 2.5rem;
  font-weight: 800;
  line-height: 1.2;
}
.event-detail-meta {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.meta-item {
  display: flex;
  align-items: center;
  gap: 12px;
  color: var(--text-secondary);
  font-size: 1rem;
}
.meta-item svg {
  color: var(--primary);
  flex-shrink: 0;
}
.event-detail-price {
  display: flex;
  align-items: baseline;
  gap: 12px;
  margin-top: 8px;
}
.price-main {
  font-size: 2rem;
  font-weight: 800;
  color: var(--accent);
  text-shadow: 0 0 15px rgba(249, 115, 22, 0.3);
}
.price-label-detail {
  color: var(--text-muted);
  font-size: 0.9rem;
}
.event-detail-actions {
  display: flex;
  gap: 12px;
  margin-top: 8px;
}
.event-detail-description {
  padding: 60px 0;
}
.desc-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 16px;
}
.desc-text {
  font-size: 1.05rem;
  line-height: 1.7;
  color: var(--text-secondary);
  max-width: 700px;
}
.related-section {
  padding: 60px 0 100px;
}
.related-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 32px;
}
.booking-card {
  background: rgba(17, 24, 39, 0.8);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 20px;
  padding: 32px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  backdrop-filter: blur(20px);
  position: sticky;
  top: 100px;
}
.booking-card-title {
  font-size: 1.1rem;
  font-weight: 700;
}
.qty-selector {
  display: flex;
  align-items: center;
  gap: 16px;
}
.qty-btn {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  color: var(--text-primary);
  font-size: 1.2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}
.qty-btn:hover { background: rgba(255,255,255,0.1); }
.qty-value {
  font-size: 1.2rem;
  font-weight: 700;
  min-width: 30px;
  text-align: center;
}
.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 16px;
  border-top: 1px solid rgba(255,255,255,0.06);
}
.total-label { color: var(--text-muted); font-size: 0.9rem; }
.total-value { font-size: 1.4rem; font-weight: 800; color: var(--accent); }
@media (max-width: 1024px) {
  .event-detail-grid { grid-template-columns: 1fr; }
  .booking-card { position: static; }
  .event-detail-title { font-size: 2rem; }
}
@media (max-width: 768px) {
  .event-detail-title { font-size: 1.6rem; }
  .event-detail-hero { padding: 120px 0 40px; }
}
</style>

<!-- Event Hero -->
<section class="event-detail-hero">
  <div class="event-detail-bg" style="background-image: url('<?php echo htmlspecialchars($event['image_url']); ?>')"></div>
  <div class="container event-detail-container">
    <div class="event-detail-grid">
      <div>
        <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-detail-image">
      </div>
      <div class="event-detail-info">
        <span class="event-detail-category">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
          <?php echo htmlspecialchars($event['category_name'] ?? $event['category']); ?>
        </span>

        <h1 class="event-detail-title"><?php echo htmlspecialchars($event['title']); ?></h1>

        <div class="event-detail-meta">
          <div class="meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <?php echo date('l, F j, Y', strtotime($event['date'])); ?> at <?php echo date('g:i A', strtotime($event['time'])); ?>
          </div>
          <div class="meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <?php echo htmlspecialchars($event['location']); ?>
          </div>
          <div class="meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            Organized by <?php echo htmlspecialchars($event['organizer_name'] ?? 'Eventify'); ?>
          </div>
        </div>

        <div class="event-detail-price">
          <span class="price-main">KES <?php echo number_format($event['price']); ?></span>
          <span class="price-label-detail">per ticket</span>
        </div>

        <div class="event-detail-actions">
          <a href="#booking" class="btn btn-primary" style="flex:1; justify-content:center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
            Get Tickets
          </a>
          <?php if (isLoggedIn()): ?>
          <button class="btn btn-secondary like-btn-toggle <?php echo $isLiked ? 'liked' : ''; ?>" data-event-id="<?php echo $event['id']; ?>" style="width:48px; padding:0; justify-content:center">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="<?php echo $isLiked ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
          </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Booking Section -->
<section class="event-detail-description" id="booking">
  <div class="container">
    <div class="event-detail-grid">
      <div>
        <h2 class="desc-title">About This Event</h2>
        <p class="desc-text"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>

        <h2 class="desc-title" style="margin-top: 40px;">Location</h2>
        <p class="desc-text"><?php echo htmlspecialchars($event['location']); ?></p>
        <div style="margin-top: 16px; background: var(--bg-card); border-radius: 16px; padding: 40px; text-align: center; color: var(--text-muted);">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 12px; opacity: 0.5;"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
          <p>Interactive map coming soon</p>
        </div>
      </div>

      <div class="booking-card">
        <h3 class="booking-card-title">Book Tickets</h3>

        <div>
          <label style="display:block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px; font-weight: 600;">Quantity</label>
          <div class="qty-selector">
            <button class="qty-btn" onclick="updateQty(-1)">-</button>
            <span class="qty-value" id="qtyValue">1</span>
            <button class="qty-btn" onclick="updateQty(1)">+</button>
          </div>
        </div>

        <div class="total-row">
          <span class="total-label">Total</span>
          <span class="total-value" id="totalPrice">KES <?php echo number_format($event['price']); ?></span>
        </div>

        <button class="btn btn-primary btn-full" onclick="bookNow()">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
          Proceed to Payment
        </button>

        <p style="text-align: center; font-size: 0.8rem; color: var(--text-muted); margin-top: 8px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>
          Secure M-Pesa & Card payments
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Related Events -->
<?php if (!empty($relatedEvents)): ?>
<section class="related-section">
  <div class="container">
    <h2 class="related-title">You Might Also Like</h2>
    <div class="events-grid">
      <?php foreach ($relatedEvents as $index => $evt):
        $glowClass = $index % 2 === 0 ? 'glow-blue' : 'glow-orange';
      ?>
      <article class="event-card <?php echo $glowClass; ?>">
        <div class="event-image-container">
          <img src="<?php echo htmlspecialchars($evt['image_url']); ?>" alt="<?php echo htmlspecialchars($evt['title']); ?>" class="event-image" loading="lazy">
          <span class="event-category-tag"><?php echo htmlspecialchars($evt['category_name'] ?? $evt['category']); ?></span>
        </div>
        <div class="event-content">
          <div class="event-meta-row">
            <div class="event-meta-item">
              <svg class="event-meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <?php echo date('F j, Y', strtotime($evt['date'])); ?>
            </div>
          </div>
          <h3 class="event-title"><?php echo htmlspecialchars($evt['title']); ?></h3>
          <div class="event-meta-item">
            <svg class="event-meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <span class="event-location"><?php echo htmlspecialchars($evt['location']); ?></span>
          </div>
          <div class="event-footer">
            <div style="display:flex;flex-direction:column">
              <span class="price-label">Tickets from</span>
              <span class="price-value">KES <?php echo number_format($evt['price']); ?></span>
            </div>
            <a href="event.php?id=<?php echo $evt['id']; ?>" class="details-btn btn btn-primary btn-sm">View Details</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<script>
const ticketPrice = <?php echo $event['price']; ?>;
let qty = 1;

function updateQty(change) {
  qty = Math.max(1, Math.min(10, qty + change));
  document.getElementById('qtyValue').textContent = qty;
  document.getElementById('totalPrice').textContent = 'KES ' + (ticketPrice * qty).toLocaleString();
}

function bookNow() {
  <?php if (!isLoggedIn()): ?>
  alert('Please login to book tickets.');
  window.location.href = 'login.php';
  <?php else: ?>
  alert('Booking ' + qty + ' ticket(s) for "<?php echo addslashes($event['title']); ?>"\n\nTotal: KES ' + (ticketPrice * qty).toLocaleString() + '\n\nM-Pesa payment integration coming soon!');
  <?php endif; ?>
}
</script>

<?php require_once 'includes/footer.php'; ?>
