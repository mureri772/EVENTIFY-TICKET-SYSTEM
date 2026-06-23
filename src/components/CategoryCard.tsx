import React from 'react';
import { Music, Briefcase, Cpu, Trophy, BookOpen, Sparkles } from 'lucide-react';
import type { Category } from '../types';
import styles from './CategoryCard.module.css';

interface CategoryCardProps {
  category: Category;
  isActive?: boolean;
  onClick?: () => void;
}

const iconMap: Record<string, React.ComponentType<{ size?: number; className?: string }>> = {
  Music,
  Briefcase,
  Cpu,
  Trophy,
  BookOpen,
  Sparkles,
};

export const CategoryCard: React.FC<CategoryCardProps> = ({ category, isActive = false, onClick }) => {
  const IconComponent = iconMap[category.iconName] || Sparkles;

  const getCategoryTheme = () => {
    switch (category.name) {
      case 'Music':
        return styles.music;
      case 'Business':
        return styles.business;
      case 'Technology':
        return styles.technology;
      case 'Sports':
        return styles.sports;
      case 'Education':
        return styles.education;
      case 'Festivals':
        return styles.festivals;
      default:
        return styles.default;
    }
  };

  return (
    <button 
      onClick={onClick} 
      className={`${styles.card} ${getCategoryTheme()} ${isActive ? styles.active : ''}`}
      aria-pressed={isActive}
    >
      <div className={styles.iconWrapper}>
        <IconComponent size={24} className={styles.icon} />
      </div>
      <div className={styles.info}>
        <h3 className={styles.name}>{category.name}</h3>
        <p className={styles.count}>{category.eventCount} Events</p>
      </div>
    </button>
  );
};
