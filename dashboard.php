<?php
/**
 * Eventify - Dashboard (User & Organizer)
 */
require_once 'includes/header.php';
requireAuth();

$currentUser = getCurrentUser();
$userEvents = isOrganizer() ? getUserEvents($_SESSION['user_id']) : [];
$userTickets = getUserTickets($_SESSION['user_id']);
$stats = getStats();

$pageTitle = 'Dashboard - Eventify';
$activePage = 'dashboard';

// Get upcoming events for users
$upcomingEvents = getFeaturedEvents();

// Get user likes
$userLikes = getUserLikes($_SESSION['user_id']);
?>

<style>
.dashboard-container {
  padding: 120px 0 60px;
  min-height: 100vh;
}
.dashboard-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 40px;
  gap: 20px;
  flex-wrap: wrap;
}
.dashboard-welcome {
  display: flex;
  align-items: center;
  gap: 16px;
}
.dashboard-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid rgba(37, 99, 235, 0.3);
}
.dashboard-welcome-text h1 {
  font-size: 1.6rem;
  font-weight: 700;
}
.dashboard-welcome-text p {
  color: var(--text-muted);
  font-size: 0.95rem;
  margin-top: 2px;
}
.dashboard-grid {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 32px;
}
.dashboard-sidebar {
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
  cursor: pointer;
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
.dashboard-content {
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
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}
.stat-box {
  background: rgba(255,255,255,0.02);
  border: 1px solid rgba(255,255,255,0.05);
  border-radius: 16px;
  padding: 24px;
  text-align: center;
  transition: all 0.3s;
}
.stat-box:hover {
  transform: translateY(-4px);
  border-color: rgba(37, 99, 235, 0.2);
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}
.stat-box-value {
  font-size: 1.8rem;
  font-weight: 800;
  color: var(--primary);
  text-shadow: 0 0 15px rgba(37, 99, 235, 0.3);
}
.stat-box-label {
  font-size: 0.8rem;
  color: var(--text-muted);
  margin-top: 4px;
  font-weight: 500;
}
.events-table {
  width: 100%;
  border-collapse: collapse;
}
.events-table th {
  text-align: left;
  padding: 12px 16px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--text-muted);
  border-bottom: 1px solid rgba(255,255,255,0.06);
}
.events-table td {
  padding: 16px;
  font-size: 0.9rem;
  border-bottom: 1px solid rgba(255,255,255,0.04);
}
.events-table tr:hover td {
  background: rgba(255,255,255,0.02);
}
.event-table-img {
  width: 48px;
  height: 32px;
  object-fit: cover;
  border-radius: 8px;
  margin-right: 12px;
  vertical-align: middle;
}
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
}
.status-active { background: rgba(16, 185, 129, 0.1); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.2); }
.status-sold_out { background: rgba(239, 68, 68, 0.1); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.2); }
.status-cancelled { background: rgba(107, 114, 128, 0.1); color: #9CA3AF; border: 1px solid rgba(107, 114, 128, 0.2); }
.status-pending { background: rgba(245, 158, 11, 0.1); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.2); }
.status-completed { background: rgba(16, 185, 129, 0.1); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.2); }
.action-btn {
  background: none;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
  padding: 6px;
  border-radius: 8px;
  transition: all 0.2s;
}
.action-btn:hover { color: var(--text-primary); background: rgba(255,255,255,0.05); }
.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--text-muted);
}
.empty-state svg {
  margin: 0 auto 16px;
  opacity: 0.3;
}
.empty-state h3 {
  font-size: 1.1rem;
  margin-bottom: 8px;
  color: var(--text-secondary);
}
.empty-state p {
  font-size: 0.9rem;
  margin-bottom: 20px;
}
.ticket-card {
  display: flex;
  gap: 20px;
  padding: 20px;
  background: rgba(255,255,255,0.02);
  border: 1px solid rgba(255,255,255,0.05);
  border-radius: 16px;
  margin-bottom: 16px;
  transition: all 0.3s;
}
.ticket-card:hover {
  border-color: rgba(37, 99, 235, 0.2);
  transform: translateY(-2px);
}
.ticket-img {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border-radius: 12px;
  flex-shrink: 0;
}
.ticket-info { flex: 1; }
.ticket-title {
  font-weight: 700;
  font-size: 1rem;
  margin-bottom: 4px;
}
.ticket-meta {
  font-size: 0.85rem;
  color: var(--text-muted);
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.ticket-code {
  font-family: monospace;
  background: rgba(37, 99, 235, 0.1);
  color: var(--primary);
  padding: 4px 12px;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 700;
}
.tab-panel { display: none; }
.tab-panel.active { display: block; }
@media (max-width: 1024px) {
  .dashboard-grid { grid-template-columns: 1fr; }
  .dashboard-sidebar { display: none; }
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
  .events-table { display: block; overflow-x: auto; }
  .stats-row { grid-template-columns: 1fr 1fr; }
  .ticket-card { flex-direction: column; }
}
</style>

<div class="dashboard-container">
  <div class="container">
    <!-- Header -->
    <div class="dashboard-header">
      <div class="dashboard-welcome">
        <img src="<?php echo htmlspecialchars($currentUser['avatar_url'] ?? 'images/avatars/default.jpg'); ?>" alt="" class="dashboard-avatar">
        <div class="dashboard-welcome-text">
          <h1>Welcome, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>!</h1>
          <p><?php echo isOrganizer() ? 'Organizer Dashboard &mdash; Manage your events' : 'Your Eventify Account'; ?></p>
        </div>
      </div>
      <?php if (isOrganizer()): ?>
      <a href="create-event.php" class="btn btn-primary">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Create Event
      </a>
      <?php endif; ?>
    </div>

    <div class="dashboard-grid">
      <!-- Sidebar -->
      <aside class="dashboard-sidebar">
        <div class="sidebar-card">
          <ul class="sidebar-nav">
            <li><a href="#overview" class="active" onclick="showTab('overview', this)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
              Overview
            </a></li>
            <li><a href="#tickets" onclick="showTab('tickets', this)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
              My Tickets
            </a></li>
            <?php if (isOrganizer()): ?>
            <li><a href="#myevents" onclick="showTab('myevents', this)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              My Events
            </a></li>
            <?php endif; ?>
            <?php if (isAdmin()): ?>
            <li><a href="admin.php">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>
              Admin Panel
            </a></li>
            <?php endif; ?>
            <li><a href="#liked\" onclick="showTab('liked', this)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
              Liked Events
            </a></li>
            <li class="sidebar-section-title">Account</li>
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

      <!-- Content -->
      <div class="dashboard-content">

        <!-- Overview Tab -->
        <div id="overview" class="tab-panel active">
          <!-- Stats -->
          <div class="stats-row">
            <div class="stat-box">
              <div class="stat-box-value"><?php echo count($userTickets); ?></div>
              <div class="stat-box-label">My Tickets</div>
            </div>
            <div class="stat-box">
              <div class="stat-box-value"><?php echo count($userLikes); ?></div>
              <div class="stat-box-label">Liked</div>
            </div>
            <div class="stat-box">
              <div class="stat-box-value"><?php echo count($userEvents); ?></div>
              <div class="stat-box-label">My Events</div>
            </div>
            <div class="stat-box">
              <div class="stat-box-value"><?php echo isOrganizer() ? 'Org' : 'User'; ?></div>
              <div class="stat-box-label">Account Type</div>
            </div>
          </div>

          <!-- Upcoming Events -->
          <div class="content-card">
            <h2 class="content-card-title">
              Upcoming Events
              <a href="index.php#events" class="btn btn-primary btn-sm" style="font-size: 0.8rem;">Browse All</a>
            </h2>
            <?php if (empty($upcomingEvents)): ?>
            <div class="empty-state">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <h3>No upcoming events</h3>
              <p>Check back later for new events</p>
            </div>
            <?php else: ?>
            <div class="events-grid" style="grid-template-columns: repeat(3, 1fr); gap: 20px;">
              <?php foreach (array_slice($upcomingEvents, 0, 3) as $index => $evt):
                $glowClass = $index % 2 === 0 ? 'glow-blue' : 'glow-orange';
              ?>
              <article class="event-card <?php echo $glowClass; ?>" style="cursor: pointer;" onclick="location.href='event.php?id=<?php echo $evt['id']; ?>'">
                <div class="event-image-container">
                  <img src="<?php echo htmlspecialchars($evt['image_url']); ?>" alt="" class="event-image" loading="lazy">
                  <span class="event-category-tag"><?php echo htmlspecialchars($evt['category_name'] ?? $evt['category']); ?></span>
                </div>
                <div class="event-content" style="padding: 16px;">
                  <div class="event-meta-row">
                    <div class="event-meta-item">
                      <svg class="event-meta-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                      <span style="font-size: 0.8rem;"><?php echo date('M j', strtotime($evt['date'])); ?></span>
                    </div>
                  </div>
                  <h3 class="event-title" style="font-size: 1rem;"><?php echo htmlspecialchars($evt['title']); ?></h3>
                  <div class="event-footer" style="padding-top: 12px;">
                    <span class="price-value" style="font-size: 0.95rem;">KES <?php echo number_format($evt['price']); ?></span>
                  </div>
                </div>
              </article>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- My Tickets Tab -->
        <div id="tickets" class="tab-panel">
          <div class="content-card">
            <h2 class="content-card-title">My Tickets</h2>
            <?php if (empty($userTickets)): ?>
            <div class="empty-state">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
              <h3>No tickets yet</h3>
              <p>Browse events and book your first ticket</p>
              <a href="index.php#events" class="btn btn-primary btn-sm">Browse Events</a>
            </div>
            <?php else: ?>
            <?php foreach ($userTickets as $ticket): ?>
            <div class="ticket-card">
              <img src="<?php echo htmlspecialchars($ticket['image_url']); ?>" alt="" class="ticket-img">
              <div class="ticket-info">
                <div class="ticket-title"><?php echo htmlspecialchars($ticket['event_title']); ?></div>
                <div class="ticket-meta">
                  <span><?php echo date('F j, Y', strtotime($ticket['event_date'])); ?> &middot; <?php echo htmlspecialchars($ticket['event_location']); ?></span>
                  <span><?php echo $ticket['quantity']; ?> ticket(s) &middot; KES <?php echo number_format($ticket['total_price']); ?></span>
                </div>
              </div>
              <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
                <span class="status-badge status-<?php echo $ticket['payment_status']; ?>"><?php echo ucfirst($ticket['payment_status']); ?></span>
                <?php if ($ticket['ticket_code']): ?>
                <span class="ticket-code"><?php echo htmlspecialchars($ticket['ticket_code']); ?></span>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <?php if (isOrganizer()): ?>
        <!-- My Events Tab -->
        <div id="myevents" class="tab-panel">
          <div class="content-card">
            <h2 class="content-card-title">
              My Events
              <a href="create-event.php" class="btn btn-primary btn-sm" style="font-size: 0.8rem;">+ New Event</a>
            </h2>
            <?php if (empty($userEvents)): ?>
            <div class="empty-state">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <h3>No events created yet</h3>
              <p>Create your first event and start selling tickets</p>
              <a href="create-event.php" class="btn btn-primary btn-sm">Create Event</a>
            </div>
            <?php else: ?>
            <table class="events-table">
              <thead>
                <tr>
                  <th>Event</th>
                  <th>Date</th>
                  <th>Price</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($userEvents as $evt): ?>
                <tr>
                  <td>
                    <img src="<?php echo htmlspecialchars($evt['image_url']); ?>" alt="" class="event-table-img">
                    <strong><?php echo htmlspecialchars($evt['title']); ?></strong>
                  </td>
                  <td><?php echo date('M j, Y', strtotime($evt['date'])); ?></td>
                  <td>KES <?php echo number_format($evt['price']); ?></td>
                  <td><span class="status-badge status-<?php echo $evt['status']; ?>"><?php echo ucfirst($evt['status']); ?></span></td>
                  <td>
                    <a href="event.php?id=<?php echo $evt['id']; ?>" class="action-btn" title="View">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </a>
                    <a href="edit-event.php?id=<?php echo $evt['id']; ?>" class="action-btn" title="Edit">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Liked Events Tab -->
        <div id="liked" class="tab-panel">
          <div class="content-card">
            <h2 class="content-card-title">Liked Events</h2>
            <?php
            $likedEvents = [];
            foreach ($userLikes as $likeId) {
              $evt = getEventById($likeId);
              if ($evt) $likedEvents[] = $evt;
            }
            if (empty($likedEvents)):
            ?>
            <div class="empty-state">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
              <h3>No liked events</h3>
              <p>Browse events and click the heart to save them here</p>
              <a href="index.php#events" class="btn btn-primary btn-sm">Browse Events</a>
            </div>
            <?php else: ?>
            <div class="events-grid" style="grid-template-columns: repeat(3, 1fr); gap: 20px;">
              <?php foreach ($likedEvents as $index => $evt):
                $glowClass = $index % 2 === 0 ? 'glow-blue' : 'glow-orange';
              ?>
              <article class="event-card <?php echo $glowClass; ?>" style="cursor: pointer;" onclick="location.href='event.php?id=<?php echo $evt['id']; ?>'">
                <div class="event-image-container">
                  <img src="<?php echo htmlspecialchars($evt['image_url']); ?>" alt="" class="event-image" loading="lazy">
                </div>
                <div class="event-content" style="padding: 16px;">
                  <h3 class="event-title" style="font-size: 1rem;"><?php echo htmlspecialchars($evt['title']); ?></h3>
                  <div class="event-footer" style="padding-top: 12px;">
                    <span class="price-value" style="font-size: 0.95rem;">KES <?php echo number_format($evt['price']); ?></span>
                  </div>
                </div>
              </article>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
function showTab(tabId, linkEl) {
  // Hide all panels
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  // Show selected
  document.getElementById(tabId).classList.add('active');
  // Update sidebar active
  document.querySelectorAll('.sidebar-nav a').forEach(a => a.classList.remove('active'));
  if (linkEl) linkEl.classList.add('active');
  // Update URL hash
  window.location.hash = tabId;
}
// Auto-open tab from hash
if (window.location.hash) {
  const tab = window.location.hash.substring(1);
  const link = document.querySelector('a[href="#' + tab + '"]');
  if (link) showTab(tab, link);
}
</script>

<?php require_once 'includes/footer.php'; ?>