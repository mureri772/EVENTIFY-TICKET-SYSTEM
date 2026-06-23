import React, { useState } from 'react';
import { Navbar } from '../components/Navbar';
import { Hero } from '../components/Hero';
import { SearchBar } from '../components/SearchBar';
import { CategoryCard } from '../components/CategoryCard';
import { EventCard } from '../components/EventCard';
import { HowItWorks } from '../components/HowItWorks';
import { OrganizerCTA } from '../components/OrganizerCTA';
import { TestimonialCard } from '../components/TestimonialCard';
import { Statistics } from '../components/Statistics';
import { Newsletter } from '../components/Newsletter';
import { Footer } from '../components/Footer';

import { mockEvents, mockCategories, mockTestimonials } from '../data/mockData';
import styles from './Home.module.css';

export const Home: React.FC = () => {
  const [selectedCategory, setSelectedCategory] = useState<string | null>(null);
  const [searchParams, setSearchParams] = useState({ query: '', location: '', category: '' });

  // Handle search submission
  const handleSearch = (query: string, location: string, category: string) => {
    setSearchParams({ query, location, category });
    // Sync category selection with search dropdown if chosen
    setSelectedCategory(category || null);
  };

  // Handle category card click
  const handleCategoryClick = (categoryName: string) => {
    if (selectedCategory === categoryName) {
      setSelectedCategory(null); // Deselect
      setSearchParams(prev => ({ ...prev, category: '' }));
    } else {
      setSelectedCategory(categoryName);
      setSearchParams(prev => ({ ...prev, category: categoryName }));
    }
  };

  // Filter events based on active category card and search parameters
  const filteredEvents = mockEvents.filter((event) => {
    // 1. Filter by category (from category card or search dropdown)
    const activeCategoryFilter = selectedCategory || searchParams.category;
    if (activeCategoryFilter && event.category.toLowerCase() !== activeCategoryFilter.toLowerCase()) {
      return false;
    }
    
    // 2. Filter by search query (title or description)
    if (searchParams.query) {
      const q = searchParams.query.toLowerCase();
      const matchTitle = event.title.toLowerCase().includes(q);
      const matchDesc = event.description.toLowerCase().includes(q);
      if (!matchTitle && !matchDesc) return false;
    }
    
    // 3. Filter by location
    if (searchParams.location) {
      const loc = searchParams.location.toLowerCase();
      if (!event.location.toLowerCase().includes(loc)) {
        return false;
      }
    }

    return true;
  });

  return (
    <div className={styles.page}>
      <Navbar />
      
      <main className={styles.mainContent}>
        {/* Hero Section */}
        <Hero />
        
        {/* Search Bar Section */}
        <SearchBar onSearch={handleSearch} />
        
        {/* Category Browsing Section */}
        <section className="section" id="categories">
          <div className="container">
            <div className="section-header">
              <span 
                className="btn btn-sm btn-secondary" 
                style={{ 
                  pointerEvents: 'none', 
                  width: 'fit-content', 
                  margin: '0 auto', 
                  color: 'var(--primary)', 
                  borderColor: 'var(--primary-light)', 
                  backgroundColor: 'var(--primary-light)' 
                }}
              >
                Categories
              </span>
              <h2 className="section-title">Browse by Category</h2>
              <p className="section-subtitle">
                Select a category to discover tailored events, concerts, or workshops.
              </p>
            </div>
            
            <div className={styles.categoryGrid}>
              {mockCategories.map((category) => (
                <CategoryCard 
                  key={category.id} 
                  category={category} 
                  isActive={selectedCategory === category.name}
                  onClick={() => handleCategoryClick(category.name)}
                />
              ))}
            </div>
          </div>
        </section>

        {/* Featured Events Section */}
        <section className="section section-bg-white" id="events">
          <div className="container">
            <div className={styles.eventsHeader}>
              <div className="section-header" style={{ margin: 0, alignItems: 'flex-start', textAlign: 'left' }}>
                <span 
                  className="btn btn-sm btn-secondary" 
                  style={{ 
                    pointerEvents: 'none', 
                    width: 'fit-content', 
                    color: 'var(--primary)', 
                    borderColor: 'var(--primary-light)', 
                    backgroundColor: 'var(--primary-light)' 
                  }}
                >
                  Featured Events
                </span>
                <h2 className="section-title">Discover Featured Events</h2>
                <p className="section-subtitle">
                  Handpicked trending events near you. Secure your ticket today.
                </p>
              </div>

              {/* Clear filters trigger */}
              {(selectedCategory || searchParams.query || searchParams.location) && (
                <button 
                  onClick={() => {
                    setSelectedCategory(null);
                    setSearchParams({ query: '', location: '', category: '' });
                  }} 
                  className={styles.clearBtn}
                >
                  Clear Filters
                </button>
              )}
            </div>

            {filteredEvents.length > 0 ? (
              <div className={styles.eventsGrid}>
                {filteredEvents.map((event) => (
                  <EventCard 
                    key={event.id} 
                    event={event} 
                    onViewDetails={(evt) => alert(`Mock details: Opening booking page for "${evt.title}"`)}
                  />
                ))}
              </div>
            ) : (
              <div className={styles.noResults}>
                <p className={styles.noResultsText}>No events found matching your current filter settings.</p>
                <button 
                  onClick={() => {
                    setSelectedCategory(null);
                    setSearchParams({ query: '', location: '', category: '' });
                  }}
                  className="btn btn-primary"
                >
                  Show All Events
                </button>
              </div>
            )}
          </div>
        </section>

        {/* How It Works Section */}
        <HowItWorks />

        {/* Organizer CTA Section */}
        <OrganizerCTA />

        {/* Testimonials Section */}
        <section className="section" id="testimonials">
          <div className="container">
            <div className="section-header">
              <span 
                className="btn btn-sm btn-secondary" 
                style={{ 
                  pointerEvents: 'none', 
                  width: 'fit-content', 
                  margin: '0 auto', 
                  color: 'var(--primary)', 
                  borderColor: 'var(--primary-light)', 
                  backgroundColor: 'var(--primary-light)' 
                }}
              >
                Testimonials
              </span>
              <h2 className="section-title">What Our Users Say</h2>
              <p className="section-subtitle">
                Thousands of fans and event hosts trust Eventify for their ticketing experiences.
              </p>
            </div>

            <div className={styles.testimonialsGrid}>
              {mockTestimonials.map((testimonial) => (
                <TestimonialCard 
                  key={testimonial.id} 
                  testimonial={testimonial} 
                />
              ))}
            </div>
          </div>
        </section>

        {/* Statistics Section */}
        <Statistics />

        {/* Newsletter Section */}
        <Newsletter />
      </main>

      <Footer />
    </div>
  );
};
