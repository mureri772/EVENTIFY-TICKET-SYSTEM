import React, { useState } from 'react';
import { Calendar, MapPin, Heart } from 'lucide-react';
import type { Event } from '../types';
import styles from './EventCard.module.css';

interface EventCardProps {
  event: Event;
  onViewDetails?: (event: Event) => void;
}

export const EventCard: React.FC<EventCardProps> = ({ event, onViewDetails }) => {
  const [isLiked, setIsLiked] = useState(false);

  const formatPrice = (price: number) => {
    return price === 0 ? 'Free' : `$${price.toFixed(2)}`;
  };

  const handleLikeToggle = (e: React.MouseEvent) => {
    e.stopPropagation(); // Avoid triggering card click
    setIsLiked(!isLiked);
  };

  return (
    <article className={styles.card} onClick={() => onViewDetails && onViewDetails(event)}>
      <div className={styles.imageContainer}>
        <img 
          src={event.imageUrl} 
          alt={event.title} 
          className={styles.image}
          loading="lazy"
        />
        <span className={styles.categoryTag}>{event.category}</span>
        
        <button 
          className={`${styles.likeBtn} ${isLiked ? styles.liked : ''}`} 
          onClick={handleLikeToggle}
          aria-label={isLiked ? "Unlike event" : "Like event"}
        >
          <Heart size={18} fill={isLiked ? "currentColor" : "none"} />
        </button>
      </div>

      <div className={styles.content}>
        <div className={styles.metaRow}>
          <div className={styles.metaItem}>
            <Calendar size={14} className={styles.icon} />
            <span>{event.date}</span>
          </div>
        </div>

        <h3 className={styles.title}>{event.title}</h3>

        <div className={styles.metaItem}>
          <MapPin size={14} className={styles.icon} />
          <span className={styles.locationText}>{event.location}</span>
        </div>

        <div className={styles.footerRow}>
          <div className={styles.priceContainer}>
            <span className={styles.priceLabel}>Tickets from</span>
            <span className={styles.priceValue}>{formatPrice(event.price)}</span>
          </div>
          
          <button 
            className={`${styles.detailsBtn} btn btn-primary btn-sm`}
            onClick={(e) => {
              e.stopPropagation();
              onViewDetails && onViewDetails(event);
            }}
          >
            View Details
          </button>
        </div>
      </div>
    </article>
  );
};
