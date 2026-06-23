import React from 'react';
import { Calendar, Heart } from 'lucide-react';
import styles from './Footer.module.css';

const FacebookIcon = (props: React.SVGProps<SVGSVGElement>) => (
  <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round" {...props}>
    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
  </svg>
);

const TwitterIcon = (props: React.SVGProps<SVGSVGElement>) => (
  <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round" {...props}>
    <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z" />
  </svg>
);

const InstagramIcon = (props: React.SVGProps<SVGSVGElement>) => (
  <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round" {...props}>
    <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
  </svg>
);

const LinkedinIcon = (props: React.SVGProps<SVGSVGElement>) => (
  <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round" {...props}>
    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
    <rect x="2" y="9" width="4" height="12" />
    <circle cx="4" cy="4" r="2" />
  </svg>
);

export const Footer: React.FC = () => {
  return (
    <footer className={styles.footer}>
      <div className={`${styles.container} container`}>
        <div className={styles.topRow}>
          {/* Logo & Brand description */}
          <div className={styles.brandCol}>
            <a href="#" className={styles.logo}>
              <Calendar className={styles.logoIcon} size={28} />
              <span>Eventify</span>
            </a>
            <p className={styles.brandDescription}>
              Discover amazing experiences, book tickets effortlessly, and make memories that last. 
              The ultimate ticketing platform for event lovers.
            </p>
            <div className={styles.socials}>
              <a href="#" className={styles.socialLink} aria-label="Facebook"><FacebookIcon /></a>
              <a href="#" className={styles.socialLink} aria-label="Twitter"><TwitterIcon /></a>
              <a href="#" className={styles.socialLink} aria-label="Instagram"><InstagramIcon /></a>
              <a href="#" className={styles.socialLink} aria-label="LinkedIn"><LinkedinIcon /></a>
            </div>
          </div>

          {/* Links Column 1: Platform */}
          <div className={styles.linksCol}>
            <h4 className={styles.colTitle}>Platform</h4>
            <ul className={styles.linksList}>
              <li><a href="#">Home</a></li>
              <li><a href="#events">Events</a></li>
              <li><a href="#categories">Categories</a></li>
            </ul>
          </div>

          {/* Links Column 2: Company */}
          <div className={styles.linksCol}>
            <h4 className={styles.colTitle}>Company</h4>
            <ul className={styles.linksList}>
              <li><a href="#about">About Us</a></li>
              <li><a href="#">Contact</a></li>
              <li><a href="#">Careers</a></li>
            </ul>
          </div>

          {/* Links Column 3: Legal */}
          <div className={styles.linksCol}>
            <h4 className={styles.colTitle}>Legal</h4>
            <ul className={styles.linksList}>
              <li><a href="#">Privacy Policy</a></li>
              <li><a href="#">Terms of Service</a></li>
            </ul>
          </div>
        </div>

        {/* Bottom row: Copyright */}
        <div className={styles.bottomRow}>
          <p className={styles.copyright}>
            © 2026 Eventify. All rights reserved.
          </p>
          <p className={styles.credit}>
            Made with <Heart size={12} className={styles.heartIcon} fill="currentColor" /> for experiences.
          </p>
        </div>
      </div>
    </footer>
  );
};
