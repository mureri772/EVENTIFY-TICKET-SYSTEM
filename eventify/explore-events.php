<?php
/**
 * Eventify - Explore All Events Page
 * Displays all events organized by category in rows
 */
require_once 'includes/header.php';

// Load all categories and events
$categories = getCategories();
$allEvents = getAllEvents();

// Get user's likes if logged in
$userLikes = isLoggedIn() ? getUserLikes($_SESSION['user_id']) : [];

// Group events by category
$eventsByCategory = [];
foreach ($categories as $cat) {
    $eventsByCategory[$cat['name']] = [];
}
foreach ($allEvents as $event) {
    $catName = $event['category_name'] ?? $event['category'] ?? 'Uncategorized';
    if (isset($eventsByCategory[$catName])) {
        $eventsByCategory[$catName][] = $event;
    }
}

$pageTitle = 'Explore Events - Eventify';
$activePage = 'explore';
?>

<style>
.explore-container {
  padding: 140px 0 80px;
  min-height: 100vh;
}
.explore-header {
  text-align: center;
  max-width: 700px;
  margin: 0 auto 60px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}
.explore-title {
  font-size: 2.75rem;
  font-weight: 800;
  letter-spacing: -0.03em;
  color: var(--text-primary);
}
.explore-subtitle {
  font-size: 1.125rem;
  color: var(--text-secondary);
}

/* Category Row Section */
.category-row-section {
  margin-bottom: 64px;
  position: relative;
}
.category-row-section:last-child {
  margin-bottom: 0;
}
.category-row-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
  gap: 16px;
  flex-wrap: wrap;
}
.category-row-title {
  display: flex;
  align-items: center;
  gap: 14px;
  font-size: 1.5rem;
  font-weight: 800;
  color: var(--text-primary);
}
.category-row-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.category-row-count {
  font-size: 0.85rem;
  color: var(--text-muted);
  font-weight: 600;
  background: rgba(255,255,255,0.04);
  padding: 6px 14px;
  border-radius: 20px;
  border: 1px solid rgba(255,255,255,0.06);
}
.category-row-events {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

/* Horizontal scroll for mobile */
@media (max-width: 1024px) {
  .category-row-events {
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }
}
@media (max-width: 768px) {
  .category-row-events {
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }
  .explore-title { font-size: 2rem; }
  .category-row-title { font-size: 1.2rem; }
}
@media (max-width: 480px) {
  .category-row-events {
    display: flex;
    overflow-x: auto;
    gap: 16px;
    padding-bottom: 16px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
  }
  .category-row-events .event-card {
    flex: 0 0 280px;
    scroll-snap-align: start;
  }
}

/* Empty state per category */
.category-empty {
  grid-column: 1 / -1;
  text-align: center;
  padding: 40px 20px;
  color: var(--text-muted);
  background: rgba(255,255,255,0.02);
  border: 1px dashed rgba(255,255,255,0.06);
  border-radius: 16px;
}
.category-empty svg {
  margin: 0 auto 12px;
  opacity: 0.3;
}
.category-empty p {
  font-size: 0.95rem;
}

/* Divider between category rows */
.category-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.06) 20%, rgba(255,255,255,0.06) 80%, transparent 100%);
  margin: 48px 0;
}

/* Back to top button */
.back-to-top {
  position: fixed;
  bottom: 32px;
  right: 32px;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--primary);
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: var(--glow-blue);
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s;
  z-index: 100;
}
.back-to-top.visible {
  opacity: 1;
  visibility: visible;
}
.back-to-top:hover {
  transform: translateY(-4px);
  background: var(--primary-hover);
}
</style>

<div class="explore-container">
  <div class="container">
    <!-- Page Header -->
    <div class="explore-header">
      <span class="badge-glow">All Events</span>
      <h1 class="explore-title">Explore Events</h1>
      <p class="explore-subtitle">Discover every experience across all categories &mdash; from gaming tournaments to fashion nights and everything in between.</p>
    </div>

    <?php
    $categoryIndex = 0;
    foreach ($categories as $cat):
      $catEvents = $eventsByCategory[$cat['name']] ?? [];
      $catIcon = $cat['icon'] ?? 'sparkles';

      // Icon colors matching category themes from styles.css
      $iconColors = [
        'gamepad'  => ['bg' => 'rgba(16,185,129,0.1)',  'color' => '#10B981'],
        'music'    => ['bg' => 'rgba(139,92,246,0.1)',   'color' => '#8B5CF6'],
        'sparkles' => ['bg' => 'rgba(236,72,153,0.1)',   'color' => '#EC4899'],
        'shirt'    => ['bg' => 'rgba(59,130,246,0.1)',   'color' => '#3B82F6'],
        'laugh'    => ['bg' => 'rgba(6,182,212,0.1)',    'color' => '#06B6D4'],
        'utensils' => ['bg' => 'rgba(249,115,22,0.1)',   'color' => '#F97316'],
      ];
      $colors = $iconColors[$catIcon] ?? $iconColors['sparkles'];

      // SVG icons
      $icons = [
        'gamepad' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="12" x2="10" y2="12"></line><line x1="8" y1="10" x2="8" y2="14"></line><line x1="15" y1="13" x2="15.01" y2="13"></line><line x1="18" y1="11" x2="18.01" y2="11"></line><rect x="2" y="6" width="20" height="12" rx="2"></rect></svg>',
        'music' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>',
        'sparkles' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path></svg>',
        'shirt' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"></path></svg>',
        'laugh' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M18 13a6 6 0 0 1-12 0"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>',
        'utensils' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>',
      ];
      $iconSvg = $icons[$catIcon] ?? $icons['sparkles'];
    ?>

    <!-- Category Row -->
    <section class="category-row-section" id="<?php echo htmlspecialchars($cat['slug']); ?>">
      <div class="category-row-header">
        <div class="category-row-title">
          <div class="category-row-icon" style="background: <?php echo $colors['bg']; ?>; color: <?php echo $colors['color']; ?>">
            <?php echo $iconSvg; ?>
          </div>
          <?php echo htmlspecialchars($cat['name']); ?>
        </div>
        <span class="category-row-count"><?php echo count($catEvents); ?> Event<?php echo count($catEvents) !== 1 ? 's' : ''; ?></span>
      </div>

      <div class="category-row-events">
        <?php if (empty($catEvents)): ?>
        <div class="category-empty">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;opacity:0.4;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
          <p>No events in this category yet. Check back soon!</p>
        </div>
        <?php else: ?>
          <?php foreach ($catEvents as $index => $event): 
            $glowClass = $index % 2 === 0 ? 'glow-blue' : 'glow-orange';
            $isLiked = in_array($event['id'], $userLikes);
          ?>
          <article class="event-card <?php echo $glowClass; ?>">
            <div class="event-image-container">
              <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="event-image" loading="lazy">
              <span class="event-category-tag"><?php echo htmlspecialchars($event['category_name'] ?? $event['category']); ?></span>
              <?php if (isLoggedIn()): ?>
              <button class="like-btn <?php echo $isLiked ? 'liked' : ''; ?>" data-event-id="<?php echo $event['id']; ?>" aria-label="<?php echo $isLiked ? 'Unlike' : 'Like'; ?> event">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="<?php echo $isLiked ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
              </button>
              <?php endif; ?>
            </div>
            <div class="event-content">
              <div class="event-meta-row">
                <div class="event-meta-item">
                  <svg class="event-meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                  <span><?php echo date('F j, Y', strtotime($event['date'])); ?></span>
                </div>
              </div>
              <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
              <div class="event-meta-item">
                <svg class="event-meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <span class="event-location"><?php echo htmlspecialchars($event['location']); ?></span>
              </div>
              <div class="event-footer">
                <div style="display:flex;flex-direction:column">
                  <span class="price-label">Tickets from</span>
                  <span class="price-value">KES <?php echo number_format($event['price']); ?></span>
                </div>
                <a href="event.php?id=<?php echo $event['id']; ?>" class="details-btn btn btn-primary btn-sm">View Details</a>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>

    <?php 
      $categoryIndex++;
      if ($categoryIndex < count($categories)): 
    ?>
    <div class="category-divider"></div>
    <?php endif; ?>

    <?php endforeach; ?>
  </div>
</div>

<!-- Back to Top Button -->
<button class="back-to-top" id="backToTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
</button>

<script>
// Show/hide back to top button
window.addEventListener('scroll', function() {
  const btn = document.getElementById('backToTop');
  if (window.scrollY > 500) {
    btn.classList.add('visible');
  } else {
    btn.classList.remove('visible');
  }
});
</script>

<?php require_once 'includes/footer.php'; ?>