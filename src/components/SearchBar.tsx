import React, { useState } from 'react';
import { Search, MapPin, Grid } from 'lucide-react';
import styles from './SearchBar.module.css';

interface SearchBarProps {
  onSearch?: (searchQuery: string, location: string, category: string) => void;
}

export const SearchBar: React.FC<SearchBarProps> = ({ onSearch }) => {
  const [searchQuery, setSearchQuery] = useState('');
  const [location, setLocation] = useState('');
  const [category, setCategory] = useState('');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (onSearch) {
      onSearch(searchQuery, location, category);
    }
  };

  return (
    <div className={`${styles.searchWrapper} container`}>
      <form onSubmit={handleSubmit} className={styles.searchForm}>
        <div className={styles.inputGroup}>
          <Search className={styles.icon} size={20} />
          <div className={styles.inputField}>
            <label htmlFor="searchQuery">Search Event</label>
            <input 
              type="text" 
              id="searchQuery" 
              placeholder="What event are you looking for?"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
            />
          </div>
        </div>

        <div className={styles.divider}></div>

        <div className={styles.inputGroup}>
          <MapPin className={styles.icon} size={20} />
          <div className={styles.inputField}>
            <label htmlFor="location">Location</label>
            <select 
              id="location"
              value={location}
              onChange={(e) => setLocation(e.target.value)}
            >
              <option value="">Anywhere</option>
              <option value="New York">New York</option>
              <option value="San Jose">San Jose</option>
              <option value="Colorado">Colorado</option>
              <option value="Chicago">Chicago</option>
              <option value="San Francisco">San Francisco</option>
              <option value="Napa Valley">Napa Valley</option>
            </select>
          </div>
        </div>

        <div className={styles.divider}></div>

        <div className={styles.inputGroup}>
          <Grid className={styles.icon} size={20} />
          <div className={styles.inputField}>
            <label htmlFor="category">Category</label>
            <select 
              id="category"
              value={category}
              onChange={(e) => setCategory(e.target.value)}
            >
              <option value="">All Categories</option>
              <option value="Music">Music</option>
              <option value="Business">Business</option>
              <option value="Technology">Technology</option>
              <option value="Sports">Sports</option>
              <option value="Education">Education</option>
              <option value="Festivals">Festivals</option>
            </select>
          </div>
        </div>

        <button type="submit" className={styles.submitBtn} aria-label="Search events">
          <Search size={20} />
          <span>Search</span>
        </button>
      </form>
    </div>
  );
};
