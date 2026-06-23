import React from 'react';
import { mockStats } from '../data/mockData';
import styles from './Statistics.module.css';

export const Statistics: React.FC = () => {
  return (
    <section className="section section-bg-dark">
      {/* Background gradients */}
      <div className={styles.glowBlob1}></div>
      <div className={styles.glowBlob2}></div>

      <div className={`${styles.container} container`}>
        <div className={styles.grid}>
          {mockStats.map((stat) => (
            <div key={stat.id} className={styles.statCard}>
              <span className={styles.value}>{stat.value}</span>
              <h3 className={styles.label}>{stat.label}</h3>
              <p className={styles.description}>{stat.description}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};
