import type { Event, Category, Testimonial } from '../types';

export const mockCategories: Category[] = [
  {
    id: 'cat-1',
    name: 'Music',
    iconName: 'Music',
    eventCount: 124,
    slug: 'music'
  },
  {
    id: 'cat-2',
    name: 'Business',
    iconName: 'Briefcase',
    eventCount: 85,
    slug: 'business'
  },
  {
    id: 'cat-3',
    name: 'Technology',
    iconName: 'Cpu',
    eventCount: 96,
    slug: 'technology'
  },
  {
    id: 'cat-4',
    name: 'Sports',
    iconName: 'Trophy',
    eventCount: 54,
    slug: 'sports'
  },
  {
    id: 'cat-5',
    name: 'Education',
    iconName: 'BookOpen',
    eventCount: 72,
    slug: 'education'
  },
  {
    id: 'cat-6',
    name: 'Festivals',
    iconName: 'Sparkles',
    eventCount: 48,
    slug: 'festivals'
  }
];

export const mockEvents: Event[] = [
  {
    id: 'evt-1',
    title: 'Rock the City Festival 2026',
    description: 'Experience an unforgettable night featuring top international rock bands performing live on stage under the stars.',
    date: 'July 24, 2026',
    time: '18:00',
    location: 'Central Park Arena, New York',
    price: 89.99,
    category: 'Music',
    imageUrl: 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=800&q=80',
    organizer: 'Live Nation Entertainment',
    isFeatured: true
  },
  {
    id: 'evt-2',
    title: 'Global Tech Innovators Summit',
    description: 'Join developers, product managers, and venture capitalists as they explore the next generation of AI, Web3, and cloud platforms.',
    date: 'August 12, 2026',
    time: '09:00',
    location: 'Silicon Valley Center, San Jose',
    price: 299.00,
    category: 'Technology',
    imageUrl: 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&w=800&q=80',
    organizer: 'Tech Alliance',
    isFeatured: true
  },
  {
    id: 'evt-3',
    title: 'Ultimate Wellness & Yoga Retreat',
    description: 'Unwind and reconnect with your inner self in this intensive weekend workshop featuring world-renowned meditation instructors.',
    date: 'September 05, 2026',
    time: '07:30',
    location: 'Serenity Springs, Colorado',
    price: 45.00,
    category: 'Education',
    imageUrl: 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=800&q=80',
    organizer: 'Mind & Body Institute',
    isFeatured: true
  },
  {
    id: 'evt-4',
    title: 'Championship Basketball Finals',
    description: 'Witness the ultimate showdown between the top two teams of the league as they battle for the national cup championship.',
    date: 'October 18, 2026',
    time: '19:30',
    location: 'Metro Dome Stadium, Chicago',
    price: 120.00,
    category: 'Sports',
    imageUrl: 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&w=800&q=80',
    organizer: 'National Sports League',
    isFeatured: true
  },
  {
    id: 'evt-5',
    title: 'Startup Leadership Workshop',
    description: 'A focused, interactive boot camp designed to help early-stage founders refine their pitch, build teams, and raise capital.',
    date: 'November 03, 2026',
    time: '10:00',
    location: 'Venture Hub, San Francisco',
    price: 150.00,
    category: 'Business',
    imageUrl: 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=800&q=80',
    organizer: 'Venture Hub Academy',
    isFeatured: true
  },
  {
    id: 'evt-6',
    title: 'Summer Food & Wine Festival',
    description: 'Indulge in award-winning culinary creations and fine wines curated by master chefs and sommeliers from around the globe.',
    date: 'December 10, 2026',
    time: '12:00',
    location: 'Sunnyvale Vineyards, Napa Valley',
    price: 65.00,
    category: 'Festivals',
    imageUrl: 'https://images.unsplash.com/photo-1485872224824-94e2226e3f09?auto=format&fit=crop&w=800&q=80',
    organizer: 'Gourmet Guild',
    isFeatured: true
  }
];

export const mockTestimonials: Testimonial[] = [
  {
    id: 'tst-1',
    name: 'Sarah Jenkins',
    role: 'Music Enthusiast',
    avatarUrl: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&h=150&q=80',
    quote: 'Booking tickets through Eventify was an absolute breeze. The mobile app interface is super clean, and I got my tickets within seconds. Will definitely use it for all future events!',
    rating: 5
  },
  {
    id: 'tst-2',
    name: 'Marcus Chen',
    role: 'Tech Summit Lead Organizer',
    company: 'DevCon Global',
    avatarUrl: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&h=150&q=80',
    quote: 'As an event organizer, Eventify has completely transformed our booking and attendee tracking. The payouts are fast, and the dashboard provides incredible insights into ticket sales.',
    rating: 5
  },
  {
    id: 'tst-3',
    name: 'Elena Rostova',
    role: 'Freelance Designer',
    avatarUrl: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=150&h=150&q=80',
    quote: 'I love how easy it is to search for workshops and festivals by category. The maps integration and clear price transparency make planning my weekends so much fun!',
    rating: 4.8
  }
];

export const mockStats = [
  { id: 'stat-1', value: '10,000+', label: 'Tickets Sold', description: 'Across worldwide shows' },
  { id: 'stat-2', value: '500+', label: 'Events Hosted', description: 'Concerts, conferences & more' },
  { id: 'stat-3', value: '50+', label: 'Cities Covered', description: 'Growing network globally' },
  { id: 'stat-4', value: '20,000+', label: 'Happy Users', description: 'Active monthly members' }
];
