/**
 * Search Surface URL Cleanup
 * Bookings and Flights Static Theme
 */

(function() {
  'use strict';

  if (!window.history || !window.URL) {
    return;
  }

  const keys = [
    'origin',
    'destination',
    'depart_date',
    'return_date',
    'travelers',
    'cabin',
    'travel_mode',
    'travel_focus',
    'baf_surface'
  ];
  const url = new URL(window.location.href);
  let changed = false;

  keys.forEach((key) => {
    if (url.searchParams.has(key)) {
      url.searchParams.delete(key);
      changed = true;
    }
  });

  if (changed) {
    window.history.replaceState({}, document.title, url.pathname + url.search + url.hash);
  }
})();
