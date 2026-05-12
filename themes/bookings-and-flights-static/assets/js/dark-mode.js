/**
 * Dark Mode Toggle
 * Bookings and Flights Static Theme
 *
 * Features:
 * - Reads localStorage for persisted preference
 * - Toggles theme-dark / theme-light class on <html>
 * - Persists choice to localStorage
 * - Uses event delegation on [data-theme-toggle]
 */

(function() {
  'use strict';

  const STORAGE_KEY = 'bookings_and_flights_theme';
  const root = document.documentElement;

  function isDark() {
    return root.classList.contains('theme-dark') ||
      (window.matchMedia('(prefers-color-scheme: dark)').matches &&
       !root.classList.contains('theme-light'));
  }

  function setTheme(mode) {
    root.classList.remove('theme-dark', 'theme-light');
    root.classList.add(mode === 'dark' ? 'theme-dark' : 'theme-light');
    localStorage.setItem(STORAGE_KEY, mode);
  }

  function handleToggle() {
    setTheme(isDark() ? 'light' : 'dark');
  }

  function init() {
    document.addEventListener('click', function(e) {
      if (e.target.closest('[data-theme-toggle]')) {
        handleToggle();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
