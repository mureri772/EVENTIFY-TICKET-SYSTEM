/**
 * Eventify — Pure JavaScript Functionality
 * No frameworks, no build tools. Just clean vanilla JS.
 */

// ═══════════════════════════════════════════════
// DATA
// ═══════════════════════════════════════════════

const categories = [
  { id: 'cat-1', name: 'Sports & Gaming', icon: 'gamepad', eventCount: 42, slug: 'sports-gaming', theme: 'cat-sports' },
  { id: 'cat-2', name: 'Music & Live Concerts', icon: 'music', eventCount: 120, slug: 'music', theme: 'cat-music' },
  { id: 'cat-3', name: 'Dance & After Party', icon: 'sparkles', eventCount: 68, slug: 'dance-party', theme: 'cat-dance' },
  { id: 'cat-4', name: 'Fashion', icon: 'shirt', eventCount: 35, slug: 'fashion', theme: 'cat-fashion' },
  { id: 'cat-5', name: 'Comedy', icon: 'laugh', eventCount: 29, slug: 'comedy', theme: 'cat-comedy' },
  { id: 'cat-6', name: 'Food & Drinks', icon: 'utensils', eventCount: 54, slug: 'food-drinks', theme: 'cat-food' }
];

const events = [
  {
    id: 'evt-1', title: 'Nairobi Summer Fest',
    description: "Nairobi's biggest summer music event featuring the best of Afrobeats, Amapiano, and live band performances under the stars.",
    date: 'July 18, 2026', time: '14:00', location: "Ngong Racecourse, Lang'ata",
    price: 2500, category: 'Music & Live Concerts',
    imageUrl: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=800&q=80',
    organizer: 'Summer Fest Kenya', isFeatured: true
  },
  {
    id: 'evt-2', title: 'FC26 FIFA Tournament',
    description: "Compete with Nairobi's top FIFA players for a grand prize pool. Experience a premium pro gaming setup, food, and massive screens.",
    date: 'July 25, 2026', time: '10:00', location: 'Triclub Arena, Westlands',
    price: 1000, category: 'Sports & Gaming',
    imageUrl: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=800&q=80',
    organizer: 'Kenya Gaming Association', isFeatured: true
  },
  {
    id: 'evt-3', title: 'Nairobi Fashion Night',
    description: "A stunning showcase of contemporary African fashion, runway designs, and local designer exhibitions featuring Kenya's top models.",
    date: 'August 08, 2026', time: '18:00', location: 'The Alchemist, Westlands',
    price: 2000, category: 'Fashion',
    imageUrl: 'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=800&q=80',
    organizer: 'Vanguard Fashion House', isFeatured: true
  },
  {
    id: 'evt-4', title: 'Stand Up Fridays',
    description: "Prepare for a night of rib-cracking comedy featuring Kenya's top stand-up comedians and exciting open mic talents.",
    date: 'August 14, 2026', time: '19:30', location: "Carnivore Grounds, Lang'ata",
    price: 800, category: 'Comedy',
    imageUrl: 'https://images.unsplash.com/photo-1585699324551-f6c309eed262?auto=format&fit=crop&w=800&q=80',
    organizer: 'Nairobi Comedy Club', isFeatured: true
  },
  {
    id: 'evt-5', title: 'Food & Vibes Festival',
    description: 'Sample diverse local and international culinary delights, craft drinks, and specialty grills with live DJ sets in a beautiful outdoor venue.',
    date: 'August 29, 2026', time: '12:00', location: 'Waterfront Mall, Karen',
    price: 1500, category: 'Food & Drinks',
    imageUrl: 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=800&q=80',
    organizer: 'Gourmet Vibes Group', isFeatured: true
  },
  {
    id: 'evt-6', title: 'Battle Zone Kenya Hiphop & After Party',
    description: "The ultimate street cypher, epic hiphop dance battle, followed by an explosive after-party featuring Nairobi's top hiphop DJs.",
    date: 'September 12, 2026', time: '16:00', location: 'Siron Hotel, Rongai',
    price: 3000, category: 'Dance & After Party',
    imageUrl: 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?auto=format&fit=crop&w=800&q=80',
    organizer: 'Battle Zone Entertainment', isFeatured: true
  }
];

const testimonials = [
  {
    id: 'tst-1', name: 'Brian Omondi', role: 'University Student & Gamer',
    avatarUrl: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&h=150&q=80',
    quote: 'Eventify makes it super easy for me to find local FIFA tournaments and hiphop gigs. Booking is lightning fast via M-Pesa, and I never miss out on weekend vibes!',
    rating: 5
  },
  {
    id: 'tst-2', name: 'Amani Mwangi', role: 'Young Marketing Professional',
    avatarUrl: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&h=150&q=80',
    quote: 'I love the new dark theme and glowing interface! It makes browsing upcoming fashion events and food festivals feel so exciting. The ticket checkout process is flawless.',
    rating: 4.9
  },
  {
    id: 'tst-3', name: 'David Kilonzo', role: 'Lead Coordinator', company: 'Nairobi Creative Group',
    avatarUrl: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&h=150&q=80',
    quote: 'Hosting the Nairobi Summer Fest on Eventify was a game-changer. We sold out in record time, and the live ticket verification at Ngong Racecourse worked flawlessly.',
    rating: 5
  }
];

const stats = [
  { id: 'stat-1', value: '500+', label: 'Active Users', description: 'Joining the network' },
  { id: 'stat-2', value: '100+', label: 'Events Hosted', description: 'Concerts, comedy, gaming & more' },
  { id: 'stat-3', value: '5000+', label: 'Tickets Sold', description: 'Fast digital M-Pesa bookings' },
  { id: 'stat-4', value: '10+', label: 'Locations', description: 'Covering key Nairobi estates' }
];

// SVG Icons for categories
const categoryIcons = {
  gamepad: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="12" x2="10" y2="12"></line><line x1="8" y1="10" x2="8" y2="14"></line><line x1="15" y1="13" x2="15.01" y2="13"></line><line x1="18" y1="11" x2="18.01" y2="11"></line><rect x="2" y="6" width="20" height="12" rx="2"></rect></svg>',
  music: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>',
  sparkles: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path></svg>',
  shirt: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"></path></svg>',
  laugh: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M18 13a6 6 0 0 1-12 0"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>',
  utensils: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path><path d="M7 2v20"></path><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path></svg>'
};

const heartIcon = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>`;
const heartIconFilled = `<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>`;
const quoteIcon = `<svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V21M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"></path></svg>`;
const calendarIcon = `<svg class="event-meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>`;
const mapPinIcon = `<svg class="event-meta-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>`;
const starFilled = `<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="star-filled"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>`;
const starEmpty = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="star-empty"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>`;


// ═══════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════

let selectedCategory = null;
let likedEvents = new Set();


// ═══════════════════════════════════════════════
// DOM ELEMENTS
// ═══════════════════════════════════════════════

const navbar = document.getElementById('navbar');
const hamburger = document.getElementById('hamburger');
const mobileOverlay = document.getElementById('mobileOverlay');
const searchForm = document.getElementById('searchForm');
const categoryGrid = document.getElementById('categoryGrid');
const eventsGrid = document.getElementById('eventsGrid');
const eventsTitle = document.getElementById('eventsTitle');
const eventsSubtitle = document.getElementById('eventsSubtitle');
const clearBtn = document.getElementById('clearBtn');
const noResults = document.getElementById('noResults');
const showAllBtn = document.getElementById('showAllBtn');
const testimonialsGrid = document.getElementById('testimonialsGrid');
const statsGrid = document.getElementById('statsGrid');
const newsletterForm = document.getElementById('newsletterForm');
const newsletterEmail = document.getElementById('newsletterEmail');
const newsletterError = document.getElementById('newsletterError');
const newsletterSuccess = document.getElementById('newsletterSuccess');


// ═══════════════════════════════════════════════
// NAVBAR
// ═══════════════════════════════════════════════

function handleNavbarScroll() {
  if (window.scrollY > 20) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
}

function toggleMobileMenu() {
  const isOpen = mobileOverlay.classList.toggle('active');
  const menuIcon = hamburger.querySelector('.menu-icon');
  const closeIcon = hamburger.querySelector('.close-icon');
  menuIcon.style.display = isOpen ? 'none' : 'block';
  closeIcon.style.display = isOpen ? 'block' : 'none';
  document.body.style.overflow = isOpen ? 'hidden' : '';
}

// Close mobile menu when clicking links
mobileOverlay.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    mobileOverlay.classList.remove('active');
    hamburger.querySelector('.menu-icon').style.display = 'block';
    hamburger.querySelector('.close-icon').style.display = 'none';
    document.body.style.overflow = '';
  });
});


// ═══════════════════════════════════════════════
// RENDER CATEGORIES
// ═══════════════════════════════════════════════

function renderCategories() {
  categoryGrid.innerHTML = categories.map(cat => `
    <button class="category-card ${cat.theme}" data-category="${cat.name}" aria-pressed="false">
      <div class="category-icon-wrapper">
        ${categoryIcons[cat.icon]}
      </div>
      <div class="category-info">
        <h3 class="category-name">${cat.name}</h3>
        <p class="category-count">${cat.eventCount} Events</p>
      </div>
      <div class="category-glow"></div>
    </button>
  `).join('');

  // Add click handlers
  categoryGrid.querySelectorAll('.category-card').forEach(card => {
    card.addEventListener('click', () => handleCategoryClick(card.dataset.category));
  });
}

function handleCategoryClick(categoryName) {
  if (selectedCategory === categoryName) {
    selectedCategory = null;
  } else {
    selectedCategory = categoryName;
  }

  // Update active state visually
  categoryGrid.querySelectorAll('.category-card').forEach(card => {
    const isActive = card.dataset.category === selectedCategory;
    card.classList.toggle('active', isActive);
    card.setAttribute('aria-pressed', isActive);
  });

  filterAndRenderEvents();
}


// ═══════════════════════════════════════════════
// RENDER EVENTS
// ═══════════════════════════════════════════════

function formatPrice(price) {
  return `KES ${price.toLocaleString()}`;
}

function renderStars(rating) {
  const floorRating = Math.floor(rating);
  let stars = '';
  for (let i = 1; i <= 5; i++) {
    stars += i <= floorRating ? starFilled : starEmpty;
  }
  return stars;
}

function filterAndRenderEvents() {
  const searchQuery = document.getElementById('searchQuery').value.toLowerCase().trim();
  const location = document.getElementById('location').value;
  const category = document.getElementById('category').value;

  const activeCategory = selectedCategory || category;

  const filtered = events.filter(event => {
    if (activeCategory && event.category.toLowerCase() !== activeCategory.toLowerCase()) return false;
    if (searchQuery) {
      const q = searchQuery;
      if (!event.title.toLowerCase().includes(q) && !event.description.toLowerCase().includes(q)) return false;
    }
    if (location) {
      if (!event.location.toLowerCase().includes(location.toLowerCase())) return false;
    }
    return true;
  });

  // Update header
  const isFiltered = !!(activeCategory || searchQuery || location);
  eventsTitle.textContent = isFiltered ? 'Filtered Results' : 'Hot in Nairobi 🔥';
  eventsSubtitle.textContent = isFiltered
    ? `${filtered.length} event${filtered.length !== 1 ? 's' : ''} match your search`
    : 'Handpicked trending events this season. Book fast — spots fill up!';
  clearBtn.style.display = isFiltered ? 'inline-flex' : 'none';

  // Render
  if (filtered.length > 0) {
    eventsGrid.style.display = 'grid';
    noResults.style.display = 'none';
    eventsGrid.innerHTML = filtered.map((event, index) => renderEventCard(event, index)).join('');

    // Add like button handlers
    eventsGrid.querySelectorAll('.like-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleLike(btn.dataset.eventId);
      });
    });

    // Add view details handlers
    eventsGrid.querySelectorAll('.details-btn').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const eventId = btn.dataset.eventId;
        const evt = events.find(ev => ev.id === eventId);
        if (evt) alert(`🎫 Booking opens for: "${evt.title}"`);
      });
    });
  } else {
    eventsGrid.style.display = 'none';
    noResults.style.display = 'flex';
  }
}

function renderEventCard(event, index) {
  const glowClass = index % 2 === 0 ? 'glow-blue' : 'glow-orange';
  const isLiked = likedEvents.has(event.id);

  return `
    <article class="event-card ${glowClass}">
      <div class="event-image-container">
        <img src="${event.imageUrl}" alt="${event.title}" class="event-image" loading="lazy">
        <span class="event-category-tag">${event.category}</span>
        <button class="like-btn ${isLiked ? 'liked' : ''}" data-event-id="${event.id}" aria-label="${isLiked ? 'Unlike' : 'Like'} event">
          ${isLiked ? heartIconFilled : heartIcon}
        </button>
      </div>
      <div class="event-content">
        <div class="event-meta-row">
          <div class="event-meta-item">
            ${calendarIcon}
            <span>${event.date}</span>
          </div>
        </div>
        <h3 class="event-title">${event.title}</h3>
        <div class="event-meta-item">
          ${mapPinIcon}
          <span class="event-location">${event.location}</span>
        </div>
        <div class="event-footer">
          <div style="display:flex;flex-direction:column">
            <span class="price-label">Tickets from</span>
            <span class="price-value">${formatPrice(event.price)}</span>
          </div>
          <button class="details-btn btn btn-primary btn-sm" data-event-id="${event.id}">View Details</button>
        </div>
      </div>
    </article>
  `;
}

function toggleLike(eventId) {
  if (likedEvents.has(eventId)) {
    likedEvents.delete(eventId);
  } else {
    likedEvents.add(eventId);
  }
  filterAndRenderEvents();
}


// ═══════════════════════════════════════════════
// RENDER TESTIMONIALS
// ═══════════════════════════════════════════════

function renderTestimonials() {
  testimonialsGrid.innerHTML = testimonials.map(t => `
    <div class="testimonial-card">
      <div class="quote-wrapper">
        <span class="quote-icon">${quoteIcon}</span>
      </div>
      <p class="quote-text">"${t.quote}"</p>
      <div class="rating-row">
        <div class="stars">${renderStars(t.rating)}</div>
        <span class="rating-value">${t.rating}</span>
      </div>
      <div class="user-row">
        <img src="${t.avatarUrl}" alt="${t.name}" class="user-avatar" loading="lazy">
        <div class="user-info">
          <h4 class="user-name">${t.name}</h4>
          <p class="user-role">${t.role}${t.company ? ` at ${t.company}` : ''}</p>
        </div>
      </div>
    </div>
  `).join('');
}


// ═══════════════════════════════════════════════
// RENDER STATISTICS with Animated Counters
// ═══════════════════════════════════════════════

function renderStats() {
  statsGrid.innerHTML = stats.map(stat => {
    const num = parseInt(stat.value.replace(/[^\d]/g, ''), 10);
    const suffix = stat.value.replace(/[\d,]/g, '');
    return `
      <div class="stat-card" data-target="${num}" data-suffix="${suffix}">
        <span class="stat-value">0${suffix}</span>
        <h3 class="stat-label">${stat.label}</h3>
        <p class="stat-description">${stat.description}</p>
      </div>
    `;
  }).join('');
}

function animateCounters() {
  const statCards = document.querySelectorAll('.stat-card');
  const duration = 1500;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const card = entry.target;
        const target = parseInt(card.dataset.target, 10);
        const suffix = card.dataset.suffix;
        const valueEl = card.querySelector('.stat-value');
        let startTimestamp = null;

        function step(timestamp) {
          if (!startTimestamp) startTimestamp = timestamp;
          const progress = Math.min((timestamp - startTimestamp) / duration, 1);
          const current = Math.floor(progress * target);
          valueEl.textContent = current.toLocaleString() + suffix;
          if (progress < 1) {
            window.requestAnimationFrame(step);
          }
        }

        window.requestAnimationFrame(step);
        observer.unobserve(card);
      }
    });
  }, { threshold: 0.5 });

  statCards.forEach(card => observer.observe(card));
}


// ═══════════════════════════════════════════════
// SEARCH
// ═══════════════════════════════════════════════

function handleSearch(e) {
  e.preventDefault();
  filterAndRenderEvents();
  document.getElementById('events').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function clearFilters() {
  selectedCategory = null;
  document.getElementById('searchQuery').value = '';
  document.getElementById('location').value = '';
  document.getElementById('category').value = '';

  categoryGrid.querySelectorAll('.category-card').forEach(card => {
    card.classList.remove('active');
    card.setAttribute('aria-pressed', 'false');
  });

  filterAndRenderEvents();
}


// ═══════════════════════════════════════════════
// NEWSLETTER
// ═══════════════════════════════════════════════

function handleNewsletterSubmit(e) {
  e.preventDefault();
  const email = newsletterEmail.value.trim();
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  newsletterEmail.classList.remove('error');
  newsletterError.textContent = '';

  if (!email) {
    newsletterError.textContent = 'Please enter your email address.';
    newsletterEmail.classList.add('error');
    return;
  }

  if (!emailRegex.test(email)) {
    newsletterError.textContent = 'Please enter a valid email address.';
    newsletterEmail.classList.add('error');
    return;
  }

  newsletterForm.style.display = 'none';
  newsletterSuccess.style.display = 'flex';
}

newsletterEmail.addEventListener('input', () => {
  if (newsletterEmail.classList.contains('error')) {
    newsletterEmail.classList.remove('error');
    newsletterError.textContent = '';
  }
});


// ═══════════════════════════════════════════════
// EVENT LISTENERS
// ═══════════════════════════════════════════════

window.addEventListener('scroll', handleNavbarScroll);
hamburger.addEventListener('click', toggleMobileMenu);
searchForm.addEventListener('submit', handleSearch);
clearBtn.addEventListener('click', clearFilters);
showAllBtn.addEventListener('click', clearFilters);
newsletterForm.addEventListener('submit', handleNewsletterSubmit);


// ═══════════════════════════════════════════════
// INITIALIZATION
// ═══════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
  renderCategories();
  filterAndRenderEvents();
  renderTestimonials();
  renderStats();
  animateCounters();
  handleNavbarScroll();
});