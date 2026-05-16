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
    'flightSearch',
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
      const submitCanonicalSearch = (event) => {
        if (!window.URL) {
          return;
        }

        const target = placementTargets[form.dataset.bafPlacementKey];

        if (!target) {
          return;
        }

        const action = new URL(form.getAttribute('action') || window.location.href, window.location.href);
        action.hash = target;

        const params = new URLSearchParams();
        const fields = new FormData(form);

        fields.forEach((value, key) => {
          const stringValue = typeof value === 'string' ? value.trim() : '';

          if ('' !== key && '' !== stringValue) {
            params.set(key, stringValue);
          }
        });

        action.search = params.toString();
        form.setAttribute('action', action.toString());

        event.preventDefault();
        event.stopImmediatePropagation();
        window.location.assign(action.toString());
      };

      form.addEventListener('submit', submitCanonicalSearch, true);

      form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
        button.addEventListener('click', submitCanonicalSearch, true);
      });
    });
  }

  function setupHomeSearchTabs(root) {
    root.querySelectorAll('[data-home-search-tabs]').forEach((tabsRoot) => {
      const tabs = Array.from(tabsRoot.querySelectorAll('[data-home-search-tab]'));
      const panels = Array.from(tabsRoot.querySelectorAll('[data-home-search-panel]'));

      if (!tabs.length || !panels.length) {
        return;
      }

      function activate(target) {
        tabs.forEach((tab) => {
          const isActive = tab.dataset.homeSearchTab === target;
          tab.classList.toggle('is-active', isActive);
          tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
          tab.setAttribute('tabindex', isActive ? '0' : '-1');
        });

        panels.forEach((panel) => {
          const isActive = panel.dataset.homeSearchPanel === target;
          panel.classList.toggle('is-active', isActive);
          panel.hidden = !isActive;
        });
      }

      tabsRoot.setAttribute('data-tabs-ready', 'true');
      activate(tabs.find((tab) => tab.classList.contains('is-active'))?.dataset.homeSearchTab || tabs[0].dataset.homeSearchTab);

      tabs.forEach((tab) => {
        tab.addEventListener('click', () => activate(tab.dataset.homeSearchTab));
        tab.addEventListener('keydown', (event) => {
          if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) {
            return;
          }

          event.preventDefault();
          const currentIndex = tabs.indexOf(tab);
          let nextIndex = currentIndex;

          if ('Home' === event.key) {
            nextIndex = 0;
          } else if ('End' === event.key) {
            nextIndex = tabs.length - 1;
          } else if ('ArrowRight' === event.key) {
            nextIndex = (currentIndex + 1) % tabs.length;
          } else if ('ArrowLeft' === event.key) {
            nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
          }

          tabs[nextIndex].focus();
          activate(tabs[nextIndex].dataset.homeSearchTab);
        });
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
    setupHomeSearchTabs(document);
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
