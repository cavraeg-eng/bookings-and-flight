/**
 * Reveal Animations
 * Bookings and Flights Static Theme
 *
 * Features:
 * - Intersection Observer for scroll-based reveals
 * - CSS transition delays from data attributes
 * - Respects prefers-reduced-motion
 * - Memory leak prevention (cleanup on page unload)
 */

(function() {
  'use strict';

  // Reduced motion check - if user prefers reduced motion, skip animations
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // If reduced motion, immediately reveal all elements and exit
  if (prefersReducedMotion) {
    function revealAll() {
      const revealElements = document.querySelectorAll('.reveal, .fade-in, .slide-up, [data-reveal]');
      revealElements.forEach(el => {
        el.style.opacity = '1';
        el.style.transform = 'none';
        el.classList.add('revealed');
      });
    }

    // Run immediately and on DOM ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', revealAll);
    } else {
      revealAll();
    }
    return;
  }

  // Intersection Observer for reveal animations
  const observerOptions = {
    root: null,
    rootMargin: '0px 0px -50px 0px',
    threshold: 0.1
  };

  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;

        // Apply delay from data attribute if present
        const delay = el.dataset.revealDelay || '0ms';
        el.style.transitionDelay = delay;

        el.classList.add('revealed');

        // Stop observing once revealed
        revealObserver.unobserve(el);
      }
    });
  }, observerOptions);

  // Observe all reveal elements
  function init() {
    const revealSelectors = [
      '.reveal',
      '.fade-in',
      '.slide-up',
      '[data-reveal]'
    ];

    revealSelectors.forEach(selector => {
      document.querySelectorAll(selector).forEach(el => {
        // Only observe if not already revealed
        if (!el.classList.contains('revealed')) {
          revealObserver.observe(el);
        }
      });
    });
  }

  // Cleanup
  function cleanup() {
    revealObserver.disconnect();
  }

  window.addEventListener('DOMContentLoaded', init);
  window.addEventListener('beforeunload', cleanup);

})();
