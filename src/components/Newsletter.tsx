import React, { useState } from 'react';
import { Send, CheckCircle } from 'lucide-react';
import styles from './Newsletter.module.css';

export const Newsletter: React.FC = () => {
  const [email, setEmail] = useState('');
  const [isSubmitted, setIsSubmitted] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!email) {
      setError('Please enter your email address.');
      return;
    }
    // Simple email validation regex
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      setError('Please enter a valid email address.');
      return;
    }

    setError('');
    setIsSubmitted(true);
    setEmail('');
  };

  return (
    <section className="section section-bg-white">
      <div className={`${styles.container} container`}>
        <div className={styles.card}>
          <div className={styles.content}>
            <h2 className={styles.title}>Stay Updated</h2>
            <p className={styles.description}>
              Get notified about upcoming events, curated festivals, and exclusive ticket pre-sales near you.
            </p>
          </div>

          <div className={styles.formWrapper}>
            {isSubmitted ? (
              <div className={styles.successMessage}>
                <CheckCircle className={styles.successIcon} size={24} />
                <div>
                  <h4 className={styles.successTitle}>Subscription Confirmed!</h4>
                  <p className={styles.successText}>Thank you for subscribing to our weekly events digest.</p>
                </div>
              </div>
            ) : (
              <form onSubmit={handleSubmit} className={styles.form} noValidate>
                <div className={styles.inputWrapper}>
                  <input 
                    type="email" 
                    placeholder="Enter your email address" 
                    className={`${styles.input} ${error ? styles.inputError : ''}`}
                    value={email}
                    onChange={(e) => {
                      setEmail(e.target.value);
                      if (error) setError('');
                    }}
                    aria-label="Email address"
                  />
                  {error && <span className={styles.errorText}>{error}</span>}
                </div>
                
                <button type="submit" className={`${styles.submitBtn} btn btn-primary`}>
                  <span>Subscribe</span>
                  <Send size={16} />
                </button>
              </form>
            )}
          </div>
        </div>
      </div>
    </section>
  );
};
