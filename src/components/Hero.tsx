import React from 'react';
import { ArrowRight, Calendar, Users, Sparkles } from 'lucide-react';
import styles from './Hero.module.css';

export const Hero: React.FC = () => {
  return (
    <section className={styles.hero}>
      {/* Background radial blobs for visual depth */}
      <div className={styles.glowBlob1}></div>
      <div className={styles.glowBlob2}></div>
      
      <div className={`${styles.container} container`}>
        <div className={styles.content}>
          <div className={`${styles.badge} animate-fade-in`}>
            <Sparkles size={16} className={styles.badgeIcon} />
            <span>Discover your next experience</span>
          </div>
          <h1 className={`${styles.title} animate-fade-in-up`}>
            Discover Amazing <span className={styles.highlight}>Events</span> Near You
          </h1>
          <p className={`${styles.description} animate-fade-in-up`}>
            Find concerts, conferences, festivals, networking events, workshops and more. 
            Book tickets instantly with Eventify.
          </p>
          <div className={styles.actions}>
            <a href="#events" className="btn btn-primary">
              Browse Events
              <ArrowRight size={18} />
            </a>
            <a href="#organizer-cta" className="btn btn-secondary">
              Become an Organizer
            </a>
          </div>
        </div>

        <div className={styles.visualWrapper}>
          <div className={styles.imageCard}>
            <img 
              src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=800&q=80" 
              alt="Live concert event crowd" 
              className={styles.heroImage}
            />
            
            {/* Floating glassmorphic badges */}
            <div className={`${styles.floatingBadge} ${styles.badge1} animate-float`}>
              <div className={styles.badgeIconWrapper}>
                <Calendar size={20} />
              </div>
              <div>
                <p className={styles.badgeTitle}>Tomorrow, 19:30</p>
                <p className={styles.badgeSubtitle}>Neon Beats Festival</p>
              </div>
            </div>

            <div className={`${styles.floatingBadge} ${styles.badge2}`}>
              <div className={styles.badgeIconWrapper2}>
                <Users size={20} />
              </div>
              <div>
                <p className={styles.badgeTitle}>12,450+</p>
                <p className={styles.badgeSubtitle}>Tickets Sold Today</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};
