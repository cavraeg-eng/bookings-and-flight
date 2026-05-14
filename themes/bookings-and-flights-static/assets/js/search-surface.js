/**
 * Search Surface Interactions
 * Bookings and Flights Static Theme
 */

(function() {
  'use strict';

  const intentKeys = [
    'origin',
    'destination',
    'depart_date',
    'return_date',
    'travelers',
    'cabin',
    'travel_mode',
    'travel_focus',
    'travel_destination',
    'check_in',
    'check_out',
    'guests',
    'rooms',
    'stay_focus',
    'baf_surface'
  ];
  const placementTargets = {
    flights_white_label_search: 'flights-provider-search',
    hotels_partner_search: 'hotels-provider-search'
  };
  const initialUrl = window.URL ? new URL(window.location.href) : null;
  const initialIntentLocation = initialUrl && hasIntent(initialUrl)
    ? initialUrl.pathname + initialUrl.search + initialUrl.hash
    : '';

  function hasIntent(url) {
    return intentKeys.some((key) => url.searchParams.has(key));
  }

  function setupCodeFields(root) {
    root.querySelectorAll('input[name="origin"], input[name="destination"]').forEach((field) => {
      field.addEventListener('input', () => {
        field.value = field.value.toUpperCase().replace(/[^A-Z]/g, '').slice(0, 3);
      });
    });
  }

  function setupSearchForms(root) {
    root.querySelectorAll('form[data-baf-placement-key]').forEach((form) => {
      form.addEventListener('submit', () => {
        if (!window.URL) {
          return;
        }

        const target = placementTargets[form.dataset.bafPlacementKey];

        if (!target) {
          return;
        }

        const action = new URL(form.getAttribute('action') || window.location.href, window.location.href);
        action.hash = target;
        form.setAttribute('action', action.toString());
      });
    });
  }

  function restoreIntentUrl() {
    if (!initialIntentLocation || !window.URL || !window.history || !window.history.replaceState) {
      return;
    }

    const url = new URL(window.location.href);
    const currentLocation = url.pathname + url.search + url.hash;

    if (currentLocation !== initialIntentLocation && !hasIntent(url)) {
      window.history.replaceState({}, document.title, initialIntentLocation);
    }
  }

  function highlightTarget() {
    if (!window.URL || !window.location.hash) {
      return;
    }

    const url = new URL(window.location.href);

    if (!hasIntent(url)) {
      return;
    }

    const target = document.getElementById(window.location.hash.slice(1));

    if (!target) {
      return;
    }

    target.classList.add('is-intent-target');
    window.setTimeout(() => {
      const reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      target.scrollIntoView({ block: 'start', behavior: reducedMotion ? 'auto' : 'smooth' });
    }, 80);
  }

  function init() {
    setupCodeFields(document);
    setupSearchForms(document);
    restoreIntentUrl();
    highlightTarget();
    window.setTimeout(restoreIntentUrl, 250);
    window.setTimeout(restoreIntentUrl, 1200);
  }

  if ('loading' === document.readyState) {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
