import React from 'react';
import { Search, Ticket, Sparkles } from 'lucide-react';
import styles from './HowItWorks.module.css';

export const HowItWorks: React.FC = () => {
  const steps = [
    {
      number: '01',
      icon: Search,
      title: 'Discover Events',
      description: 'Filter events by category, name, or location to find exactly what you are interested in.'
    },
    {
      number: '02',
      icon: Ticket,
      title: 'Book Tickets',
      description: 'Choose your tier, secure your seats, and checkout instantly with our safe, fast system.'
    },
    {
      number: '03',
      icon: Sparkles,
      title: 'Enjoy the Experience',
      description: 'Receive your mobile passes instantly and show them at the venue. Enjoy the show!'
    }
  ];

  return (
    <section className="section section-bg-white" id="about">
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
            Easy Steps
          </span>
          <h2 className="section-title">How Eventify Works</h2>
          <p className="section-subtitle">
            Get from discovering concerts to entering the venue in three simple steps.
          </p>
        </div>

        <div className={styles.stepsGrid}>
          {steps.map((step, index) => {
            const IconComponent = step.icon;
            return (
              <div key={index} className={styles.stepCard}>
                <div className={styles.iconWrapperOuter}>
                  <div className={styles.stepNumber}>{step.number}</div>
                  <div className={styles.iconWrapperInner}>
                    <IconComponent size={28} className={styles.stepIcon} />
                  </div>
                </div>
                <h3 className={styles.stepTitle}>{step.title}</h3>
                <p className={styles.stepDescription}>{step.description}</p>
                
                {/* Connecting arrow/line for desktop layouts (except last item) */}
                {index < 2 && <div className={styles.connectorLine}></div>}
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
};
