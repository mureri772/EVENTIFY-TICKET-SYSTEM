import React, { useState, useEffect } from 'react';
import { Menu, X, Calendar } from 'lucide-react';
import styles from './Navbar.module.css';

export const Navbar: React.FC = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [isScrolled, setIsScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 20) {
        setIsScrolled(true);
      } else {
        setIsScrolled(false);
      }
    };

    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const toggleMenu = () => {
    setIsOpen(!isOpen);
    // Prevent scrolling when mobile menu is open
    if (!isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
  };

  return (
    <header className={`${styles.navbar} ${isScrolled ? styles.scrolled : ''}`}>
      <div className={`${styles.container} container`}>
        <a href="#" className={styles.logo}>
          <Calendar className={styles.logoIcon} size={28} />
          <span>Eventify</span>
        </a>

        {/* Desktop Nav */}
        <nav className={styles.desktopNav}>
          <ul className={styles.navLinks}>
            <li><a href="#" className={styles.active}>Home</a></li>
            <li><a href="#events">Events</a></li>
            <li><a href="#categories">Categories</a></li>
            <li><a href="#about">About</a></li>
          </ul>
        </nav>

        <div className={styles.authButtons}>
          <button className={`${styles.btnLink} btn`}>Login</button>
          <button className="btn btn-primary btn-sm">Sign Up</button>
        </div>

        {/* Mobile Toggle */}
        <button 
          className={styles.hamburger} 
          onClick={toggleMenu} 
          aria-label="Toggle navigation menu"
        >
          {isOpen ? <X size={24} /> : <Menu size={24} />}
        </button>
      </div>

      {/* Mobile Nav Overlay */}
      {isOpen && (
        <div className={styles.mobileOverlay}>
          <nav className={styles.mobileNav}>
            <ul className={styles.mobileLinks}>
              <li><a href="#" onClick={toggleMenu} className={styles.mobileActive}>Home</a></li>
              <li><a href="#events" onClick={toggleMenu}>Events</a></li>
              <li><a href="#categories" onClick={toggleMenu}>Categories</a></li>
              <li><a href="#about" onClick={toggleMenu}>About</a></li>
              <li className={styles.mobileAuthDivider}></li>
              <li>
                <button className={styles.mobileBtnLink} onClick={toggleMenu}>Login</button>
              </li>
              <li>
                <button className="btn btn-primary btn-sm" style={{ width: '100%', padding: '14px 0' }} onClick={toggleMenu}>Sign Up</button>
              </li>
            </ul>
          </nav>
        </div>
      )}
    </header>
  );
};
