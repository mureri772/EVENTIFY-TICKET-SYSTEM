export interface Event {
  id: string;
  title: string;
  description: string;
  date: string;
  time: string;
  location: string;
  price: number;
  category: string;
  imageUrl: string;
  organizer: string;
  isFeatured?: boolean;
}

export interface Category {
  id: string;
  name: string;
  iconName: string; // Dynamic mapping to Lucide React icons
  eventCount: number;
  slug: string;
}

export interface Testimonial {
  id: string;
  name: string;
  role: string;
  company?: string;
  avatarUrl: string;
  quote: string;
  rating: number;
}
