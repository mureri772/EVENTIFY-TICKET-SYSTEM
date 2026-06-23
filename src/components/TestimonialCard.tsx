import React from 'react';
import { Star, Quote } from 'lucide-react';
import type { Testimonial } from '../types';
import styles from './TestimonialCard.module.css';

interface TestimonialCardProps {
  testimonial: Testimonial;
}

export const TestimonialCard: React.FC<TestimonialCardProps> = ({ testimonial }) => {
  const renderStars = (rating: number) => {
    const stars = [];
    const floorRating = Math.floor(rating);
    
    for (let i = 1; i <= 5; i++) {
      if (i <= floorRating) {
        stars.push(
          <Star key={i} size={16} className={styles.starFilled} fill="currentColor" />
        );
      } else {
        stars.push(
          <Star key={i} size={16} className={styles.starEmpty} />
        );
      }
    }
    return stars;
  };

  return (
    <div className={styles.card}>
      <div className={styles.quoteWrapper}>
        <Quote className={styles.quoteIcon} size={32} />
      </div>
      
      <p className={styles.quoteText}>"{testimonial.quote}"</p>
      
      <div className={styles.ratingRow}>
        <div className={styles.stars}>
          {renderStars(testimonial.rating)}
        </div>
        <span className={styles.ratingValue}>{testimonial.rating}</span>
      </div>
      
      <div className={styles.userRow}>
        <img 
          src={testimonial.avatarUrl} 
          alt={testimonial.name} 
          className={styles.avatar}
          loading="lazy"
        />
        <div className={styles.userInfo}>
          <h4 className={styles.userName}>{testimonial.name}</h4>
          <p className={styles.userRole}>
            {testimonial.role}
            {testimonial.company && ` at ${testimonial.company}`}
          </p>
        </div>
      </div>
    </div>
  );
};
