<?php
/**
 * Eventify - Homepage
 */
require_once 'includes/header.php';

// Load data from database
$categories = getCategories();
$events = getFeaturedEvents();
$testimonials = getTestimonials();
$stats = getStats();

// Get user's likes if logged in
$userLikes = isLoggedIn() ? getUserLikes($_SESSION['user_id']) : [];
?>

<!-- ==================== HERO ==================== -->
<section class="hero">
  <div class="glow-blob-1"></div>
  <div class="glow-blob-2"></div>

  <div class="container hero-container">
    <div class="hero-content">
      <div class="hero-badge animate-fade-in">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path></svg>
        <span>Nairobi's Ticketing Platform</span>
      </div>

      <h1 class="hero-title animate-fade-in-up">
        Discover <span class="glow-text-blue">Nairobi's</span><br>
        <span class="glow-text-orange">Best</span> Experiences
      </h1>

      <p class="hero-description animate-fade-in-up">
        Concerts, festivals, networking events, fashion shows, sports tournaments
        and unforgettable nights all in one place.
      </p>

      <div class="hero-actions">
        <a href="explore-events.php" class="btn btn-primary">
          Explore Events
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </a>
        <a href="#organizer-cta" class="btn btn-secondary">Become an Organizer</a>
      </div>
    </div>

    <div class="hero-visual">
      <div class="scene-container">
        <div class="neon-backlight"></div>

        <!-- Floating Card 1 -->
        <div class="floating-card card-1 animate-float">
          <div class="card-img-container">
            <img src="images/hero/concert.jpg" alt="Live Concert" class="card-img">
            <span class="tag-music">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
              Music
            </span>
          </div>
          <div class="floating-card-body">
            <h3 class="floating-card-title">Nairobi Summer Fest</h3>
            <div class="floating-card-meta">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <span>July 18, 2026</span>
            </div>
          </div>
        </div>

        <!-- Floating Card 2 -->
        <div class="floating-card card-2 animate-float-reverse">
          <div class="card-img-container">
            <img src="images/hero/gaming.jpg" alt="FIFA Tournament" class="card-img">
            <span class="tag-gaming">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
              Gaming
            </span>
          </div>
          <div class="floating-card-body">
            <h3 class="floating-card-title">FC26 FIFA Tournament</h3>
            <div class="floating-card-meta">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
              <span>KES 1,000</span>
            </div>
          </div>
        </div>

        <div class="accent-orb"></div>
      </div>
    </div>
  </div>
</section>

<!-- ==================== SEARCH BAR ==================== -->
<div class="search-wrapper container">
  <form class="search-form" id="searchForm" action="index.php" method="GET">
    <div class="input-group">
      <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <div class="input-field">
        <label for="searchQuery">Search Event</label>
        <input type="text" id="searchQuery" name="search" placeholder="What experience are you looking for?" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
      </div>
    </div>

    <div class="divider"></div>

    <div class="input-group">
      <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
      <div class="input-field">
        <label for="location">Location</label>
        <select id="location" name="location">
          <option value="">Anywhere in Nairobi</option>
          <option value="Westlands" <?php echo ($_GET['location'] ?? '') === 'Westlands' ? 'selected' : ''; ?>>Westlands</option>
          <option value="Lang'ata" <?php echo ($_GET['location'] ?? '') === "Lang'ata" ? 'selected' : ''; ?>>Lang'ata</option>
          <option value="Rongai" <?php echo ($_GET['location'] ?? '') === 'Rongai' ? 'selected' : ''; ?>>Rongai</option>
          <option value="Karen" <?php echo ($_GET['location'] ?? '') === 'Karen' ? 'selected' : ''; ?>>Karen</option>
          <option value="Roysambu" <?php echo ($_GET['location'] ?? '') === 'Roysambu' ? 'selected' : ''; ?>>Roysambu</option>
          <option value="Thika" <?php echo ($_GET['location'] ?? '') === 'Thika' ? 'selected' : ''; ?>>Thika</option>
          <option value="Ruiru" <?php echo ($_GET['location'] ?? '') === 'Ruiru' ? 'selected' : ''; ?>>Ruiru</option>
          <option value="Ruaka" <?php echo ($_GET['location'] ?? '') === 'Ruaka' ? 'selected' : ''; ?>>Ruaka</option>
          <option value="Umoja" <?php echo ($_GET['location'] ?? '') === 'Umoja' ? 'selected' : ''; ?>>Umoja</option>
        </select>
      </div>
    </div>

    <div class="divider"></div>

    <div class="input-group">
      <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
      <div class="input-field">
        <label for="category">Category</label>
        <select id="category" name="category">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
          <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($_GET['category'] ?? '') === $cat['name'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <button type="submit" class="search-submit-btn" aria-label="Search events">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <span>Search</span>
    </button>
  </form>
</div>

<!-- ==================== CATEGORIES ==================== -->
<section class="section" id="categories">
  <div class="container">
    <div class="section-header">
      <span class="badge-glow">Categories</span>
      <h2 class="section-title">Browse by Category</h2>
      <p class="section-subtitle">Pick your vibe &mdash; sports, music, fashion, comedy and more.</p>
    </div>

    <div class="category-grid" id="categoryGrid">
      <?php foreach ($categories as $cat):
        $isActive = ($_GET['category'] ?? '') === $cat['name'];
      ?>
      <a href="explore-events.php#<?php echo htmlspecialchars($cat['slug']); ?>" class="category-card <?php echo $cat['theme']; ?> <?php echo $isActive ? 'active' : ''; ?>" data-category="<?php echo htmlspecialchars($cat['name']); ?>">
        <div class="category-icon-wrapper">
          <?php
          // Output SVG icon based on icon name
          $icons = [
            'gamepad' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="12" x2="10" y2="12"></line><line x1="8" y1="10" x2="8" y2="14"></line><line x1="15" y1="13" x2="15.01" y2="13"></line><line x1="18" y1="11" x2="18.01" y2="11"></line><rect x="2" y="6" width="20" height="12" rx="2"></rect></svg>',
            'music' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>',
            'sparkles' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path></svg>',
            'shirt' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"></path></svg>',
            'laugh' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M18 13a6 6 0 0 1-12 0"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>',
            'utensils' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>'
          ];
          echo $icons[$cat['icon']] ?? $icons['sparkles'];
          ?>
        </div>
        <div class="category-info">
          <h3 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h3>
          <p class="category-count"><?php echo $cat['event_count']; ?> Events</p>
        </div>
        <div class="category-glow"></div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==================== FEATURED EVENTS ==================== -->
<section class="section section-bg-dark" id="events">
  <div class="container">
    <div class="events-header" id="eventsHeader">
      <div class="events-heading-group">
        <span class="badge-glow">Featured Events</span>
        <h2 class="section-title" id="eventsTitle" style="text-align:left">Hot in Nairobi &#128293;</h2>
        <p class="section-subtitle" id="eventsSubtitle" style="text-align:left">Handpicked trending events this season. Book fast &mdash; spots fill up!</p>
      </div>
      <?php if (!empty($_GET['category']) || !empty($_GET['search']) || !empty($_GET['location'])): ?>
      <a href="index.php#events" class="clear-btn">Clear Filters</a>
      <?php endif; ?>
    </div>

    <div class="events-grid" id="eventsGrid">
      <?php
      // Apply filters if present
      $filters = [];
      if (!empty($_GET['category'])) $filters['category'] = $_GET['category'];
      if (!empty($_GET['location'])) $filters['location'] = $_GET['location'];
      if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];

      $displayEvents = !empty($filters) ? getAllEvents($filters) : $events;

      if (empty($displayEvents)):
      ?>
      <div class="no-results" style="display:flex; grid-column: 1/-1;">
        <span class="no-results-emoji">&#127908;</span>
        <p class="no-results-text">No events found for your current filters.</p>
        <a href="index.php#events" class="btn btn-primary">Show All Events</a>
      </div>
      <?php
      else:
        foreach ($displayEvents as $index => $event):
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
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>

<!-- ==================== HOW IT WORKS ==================== -->
<section class="section section-bg-dark" id="about">
  <div class="howitworks-glow"></div>
  <div class="container" style="position:relative;z-index:1">
    <div class="section-header">
      <span class="vibe-flow-badge">Vibe Flow</span>
      <h2 class="section-title">How It Works</h2>
      <p class="section-subtitle">Get from finding experiences to scanning tickets in three fast steps.</p>
    </div>

    <div class="steps-grid">
      <div class="step-card">
        <div class="icon-wrapper-outer">
          <div class="step-number">01</div>
          <div class="icon-wrapper-inner">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </div>
        </div>
        <div class="connector-line"></div>
        <h3 class="step-title">Discover Events</h3>
        <p class="step-description">Filter events by category, location, or name to find what excites you.</p>
      </div>

      <div class="step-card">
        <div class="icon-wrapper-outer">
          <div class="step-number">02</div>
          <div class="icon-wrapper-inner">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
          </div>
        </div>
        <div class="connector-line"></div>
        <h3 class="step-title">Book Tickets</h3>
        <p class="step-description">Choose your tickets, pay securely via M-Pesa or card, and checkout instantly.</p>
      </div>

      <div class="step-card">
        <div class="icon-wrapper-outer">
          <div class="step-number">03</div>
          <div class="icon-wrapper-inner">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path></svg>
          </div>
        </div>
        <h3 class="step-title">Enjoy the Experience</h3>
        <p class="step-description">Get your digital passes sent to your phone. Scan at the gate and experience the vibe.</p>
      </div>
    </div>
  </div>
</section>

<!-- ==================== ORGANIZER CTA ==================== -->
<section class="section organizer-section" id="organizer-cta">
  <div class="organizer-glow"></div>
  <div class="container" style="position:relative;z-index:1">
    <div class="organizer-card">
      <div class="organizer-content">
        <div class="organizer-badge">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path></svg>
          <span>Organizer Dashboard</span>
        </div>

        <h2 class="organizer-title">Ready to Host Your Next Event?</h2>
        <p class="organizer-text">Create events, sell tickets, track attendees and grow your audience with Eventify. Our powerful tools give you full control over your ticketing experience.</p>

        <div class="organizer-features">
          <div class="feature-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline><polyline points="16 7 22 7 22 13"></polyline></svg>
            <span>Real-time Sales Insights</span>
          </div>
          <div class="feature-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"></path><path d="M13 5v2"></path><path d="M13 17v2"></path><path d="M13 11v2"></path></svg>
            <span>Flexible Ticket Tiers</span>
          </div>
          <div class="feature-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>
            <span>Secure Entry Scanning</span>
          </div>
        </div>

        <?php if (isLoggedIn()): ?>
        <a href="dashboard.php" class="btn btn-accent" style="margin-top:8px">Go to Dashboard</a>
        <?php else: ?>
        <a href="register.php?role=organizer" class="btn btn-accent" style="margin-top:8px">Start Organizing</a>
        <?php endif; ?>
      </div>

      <div class="organizer-visual">
        <div class="dashboard-mock">
          <div class="mock-header">
            <div class="mock-dot" style="background:#EF4444"></div>
            <div class="mock-dot" style="background:#F59E0B"></div>
            <div class="mock-dot" style="background:#10B981"></div>
            <span class="mock-title">eventify.co.ke/dashboard</span>
          </div>
          <div class="mock-content">
            <p class="mock-label">Total Ticket Sales</p>
            <div class="mock-sales-row">
              <span class="mock-sales-value">KES 184,500</span>
              <span class="mock-growth">&#8593; 24% this week</span>
            </div>
            <div class="mock-chart">
              <div class="chart-bar" style="height:40%"></div>
              <div class="chart-bar" style="height:65%"></div>
              <div class="chart-bar" style="height:55%"></div>
              <div class="chart-bar" style="height:85%"></div>
              <div class="chart-bar" style="height:95%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==================== TESTIMONIALS ==================== -->
<section class="section section-bg-dark" id="testimonials">
  <div class="container">
    <div class="section-header">
      <span class="badge-glow">Reviews</span>
      <h2 class="section-title">What Nairobi Says &#128483;&#65039;</h2>
      <p class="section-subtitle">Real stories from event goers, students, and organizers across Nairobi.</p>
    </div>

    <div class="testimonials-grid" id="testimonialsGrid">
      <?php foreach ($testimonials as $t): ?>
      <div class="testimonial-card">
        <div class="quote-wrapper">
          <span class="quote-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V21M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"></path></svg></span>
        </div>
        <p class="quote-text">"<?php echo htmlspecialchars($t['quote']); ?>"</p>
        <div class="rating-row">
          <div class="stars">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="<?php echo $i <= floor($t['rating']) ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" class="<?php echo $i <= floor($t['rating']) ? 'star-filled' : 'star-empty'; ?>"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            <?php endfor; ?>
          </div>
          <span class="rating-value"><?php echo $t['rating']; ?></span>
        </div>
        <div class="user-row">
          <img src="<?php echo htmlspecialchars($t['avatar_url']); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" class="user-avatar" loading="lazy">
          <div class="user-info">
            <h4 class="user-name"><?php echo htmlspecialchars($t['name']); ?></h4>
            <p class="user-role"><?php echo htmlspecialchars($t['role']); ?><?php echo $t['company'] ? ' at ' . htmlspecialchars($t['company']) : ''; ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==================== STATISTICS ==================== -->
<section class="section section-bg-dark">
  <div class="stats-glow-1"></div>
  <div class="stats-glow-2"></div>
  <div class="container" style="position:relative;z-index:1">
    <div class="stats-grid" id="statsGrid">
      <div class="stat-card" data-target="500" data-suffix="+">
        <span class="stat-value">0+</span>
        <h3 class="stat-label">Active Users</h3>
        <p class="stat-description">Joining the network</p>
      </div>
      <div class="stat-card" data-target="<?php echo $stats['events'] * 10; ?>" data-suffix="+">
        <span class="stat-value">0+</span>
        <h3 class="stat-label">Events Hosted</h3>
        <p class="stat-description">Concerts, comedy, gaming &amp; more</p>
      </div>
      <div class="stat-card" data-target="5000" data-suffix="+">
        <span class="stat-value">0+</span>
        <h3 class="stat-label">Tickets Sold</h3>
        <p class="stat-description">Fast digital M-Pesa bookings</p>
      </div>
      <div class="stat-card" data-target="10" data-suffix="+">
        <span class="stat-value">0+</span>
        <h3 class="stat-label">Locations</h3>
        <p class="stat-description">Covering key Nairobi estates</p>
      </div>
    </div>
  </div>
</section>

<!-- ==================== NEWSLETTER ==================== -->
<section class="newsletter-section">
  <div class="newsletter-glow-1"></div>
  <div class="newsletter-glow-2"></div>
  <div class="container" style="position:relative;z-index:1;display:flex;justify-content:center">
    <div class="newsletter-card">
      <div class="newsletter-icon-badge">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
      </div>
      <h2 class="newsletter-title">Stay in the Loop</h2>
      <p class="newsletter-description">Be the first to know about upcoming events, exclusive pre-sales, and weekend vibes in Nairobi.</p>

      <div class="newsletter-success" id="newsletterSuccess" style="display:none">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="m9 11 3 3L22 4"></path></svg>
        <div>
          <h4 class="success-title">You're in! &#127881;</h4>
          <p class="success-text">We'll notify you about the hottest upcoming events.</p>
        </div>
      </div>

      <form class="newsletter-form" id="newsletterForm" action="api/newsletter.php" method="POST">
        <div class="newsletter-input-wrapper">
          <input type="email" name="email" id="newsletterEmail" class="newsletter-input" placeholder="Enter your email address" aria-label="Email address" required>
          <span class="newsletter-error" id="newsletterError"></span>
        </div>
        <button type="submit" class="btn btn-accent newsletter-submit">
          <span>Subscribe</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
        </button>
      </form>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>