import React from 'react';
import { Sparkles, Calendar, TrendingUp, ShieldCheck } from 'lucide-react';
import styles from './OrganizerCTA.module.css';

export const OrganizerCTA: React.FC = () => {
  return (
    <section className={styles.section} id="organizer-cta">
      {/* Background glow radial blob */}
      <div className={styles.glowBlob}></div>
      
      <div className={`${styles.container} container`}>
        <div className={styles.card}>
          <div className={styles.content}>
            <div className={styles.badge}>
              <Sparkles size={16} />
              <span>Organizer Dashboard</span>
            </div>
            
            <h2 className={styles.title}>Ready to Host Your Next Event?</h2>
            
            <p className={styles.text}>
              Create events, sell tickets, track attendees and grow your audience with Eventify. 
              Our powerful suite of management tools gives you complete control over your ticketing experience.
            </p>
            
            <div className={styles.features}>
              <div className={styles.featureItem}>
                <TrendingUp size={18} className={styles.featureIcon} />
                <span>Real-time Sales Insights</span>
              </div>
              <div className={styles.featureItem}>
                <Calendar size={18} className={styles.featureIcon} />
                <span>Flexible Ticket Tiers</span>
              </div>
              <div className={styles.featureItem}>
                <ShieldCheck size={18} className={styles.featureIcon} />
                <span>Secure Entry Scanning</span>
              </div>
            </div>

            <button className="btn btn-accent" style={{ marginTop: '8px' }}>
              Start Organizing
            </button>
          </div>

          <div className={styles.visualSide}>
            {/* Visual organizer dashboard mockup */}
            <div className={styles.dashboardMock}>
              <div className={styles.mockHeader}>
                <div className={styles.mockDot}></div>
                <div className={styles.mockDot}></div>
                <div className={styles.mockDot}></div>
                <span className={styles.mockTitle}>eventify.com/dashboard</span>
              </div>
              
              <div className={styles.mockContent}>
                <p className={styles.mockLabel}>Total Ticket Sales</p>
                <div className={styles.mockSalesRow}>
                  <span className={styles.mockSalesValue}>$18,450.00</span>
                  <span className={styles.mockGrowth}>+24% this week</span>
                </div>
                
                {/* Visual bar chart items */}
                <div className={styles.mockChart}>
                  <div className={styles.chartBar} style={{ height: '40%' }}></div>
                  <div className={styles.chartBar} style={{ height: '65%' }}></div>
                  <div className={styles.chartBar} style={{ height: '55%' }}></div>
                  <div className={styles.chartBar} style={{ height: '85%' }}></div>
                  <div className={styles.chartBar} style={{ height: '95%' }}></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};
