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
    'adults',
    'children',
    'infants',
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

  function clampInteger(value, min, max) {
    const parsed = Number.parseInt(value, 10);

    if (!Number.isFinite(parsed)) {
      return min;
    }

    return Math.min(max, Math.max(min, parsed));
  }

  function compactDate(value) {
    const match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})$/);

    return match ? `${match[3]}${match[2]}` : '';
  }

  function fieldValue(fields, key) {
    const value = fields.get(key);

    return typeof value === 'string' ? value.trim() : '';
  }

  function cabinLabel(value) {
    return {
      economy: 'Economy',
      premium_economy: 'Premium economy',
      business: 'Business',
      first: 'First'
    }[value] || 'Economy';
  }

  function updatePassengerSummary(form, adults, children, infants) {
    const summary = form.querySelector('[data-baf-passenger-summary]');

    if (!summary) {
      return;
    }

    const parts = [`${adults} ${adults === 1 ? 'adult' : 'adults'}`];

    if (children > 0) {
      parts.push(`${children} ${children === 1 ? 'child' : 'children'}`);
    }

    if (infants > 0) {
      parts.push(`${infants} ${infants === 1 ? 'infant' : 'infants'}`);
    }

    summary.textContent = `${parts.join(', ')}, ${cabinLabel(form.elements.cabin?.value || 'economy')}`;
  }

  function buildFlightSearch(fields) {
    const origin = fieldValue(fields, 'origin').toUpperCase();
    const destination = fieldValue(fields, 'destination').toUpperCase();
    const departDate = compactDate(fieldValue(fields, 'depart_date'));

    if (!/^[A-Z]{3}$/.test(origin) || !/^[A-Z]{3}$/.test(destination) || '' === departDate) {
      return '';
    }

    const returnDate = compactDate(fieldValue(fields, 'return_date'));
    const cabinPrefix = {
      business: 'c',
      premium_economy: 'w',
      first: 'f'
    }[fieldValue(fields, 'cabin')] || '';
    const legacyTravelers = clampInteger(fieldValue(fields, 'travelers') || '1', 1, 9);
    const adults = clampInteger(fieldValue(fields, 'adults') || String(legacyTravelers), 1, 9);
    let children = clampInteger(fieldValue(fields, 'children') || '0', 0, 8);
    let infants = clampInteger(fieldValue(fields, 'infants') || '0', 0, adults);

    if (adults + children > 9) {
      children = 9 - adults;
    }

    infants = Math.min(infants, adults);

    const passengerSuffix = `${cabinPrefix}${adults}${children > 0 || infants > 0 ? children : ''}${infants > 0 ? infants : ''}`;

    fields.set('adults', String(adults));
    fields.set('children', String(children));
    fields.set('infants', String(infants));
    fields.set('travelers', String(adults + children + infants));

    return `${origin}${departDate}${destination}${returnDate}${passengerSuffix}`;
  }

  function syncPassengerTotal(form) {
    const totalField = form.querySelector('[data-baf-passenger-total]');

    if (!totalField) {
      return;
    }

    const adults = clampInteger(form.elements.adults?.value || '1', 1, 9);
    let children = clampInteger(form.elements.children?.value || '0', 0, 8);
    let infants = clampInteger(form.elements.infants?.value || '0', 0, adults);

    if (adults + children > 9) {
      children = 9 - adults;
    }

    infants = Math.min(infants, adults);
    totalField.value = String(adults + children + infants);

    if (form.elements.children) {
      form.elements.children.value = String(children);
    }

    if (form.elements.infants) {
      form.elements.infants.max = String(adults);
      form.elements.infants.value = String(infants);
    }

    updatePassengerSummary(form, adults, children, infants);
  }

  function syncFlightSearch(form) {
    const flightSearchField = form.querySelector('[data-baf-flight-search]');

    if (!flightSearchField) {
      return '';
    }

    const fields = new FormData(form);
    const flightSearch = buildFlightSearch(fields);

    flightSearchField.value = flightSearch;
    syncPassengerTotal(form);

    return flightSearch;
  }

  function setupSearchForms(root) {
    root.querySelectorAll('form[data-baf-placement-key]').forEach((form) => {
      const submitCanonicalSearch = (event) => {
        if (!window.URL) {
          return;
        }

        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
          if ('click' === event.type && typeof form.reportValidity === 'function') {
            form.reportValidity();
          }

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
        const flightSearch = form.dataset.bafPlacementKey === 'flights_white_label_search' ? syncFlightSearch(form) : '';

        if ('' !== flightSearch) {
          fields.set('flightSearch', flightSearch);
        }

        fields.forEach((value, key) => {
          const stringValue = typeof value === 'string' ? value.trim() : '';

          if ('' !== key && '' !== stringValue) {
            params.set(key, stringValue);
          }
        });

        if ('' !== flightSearch) {
          params.delete('origin');
          params.delete('destination');
          params.delete('depart_date');
          params.delete('return_date');
          params.delete('adults');
          params.delete('children');
          params.delete('infants');
          params.delete('travelers');
          params.delete('cabin');
          params.set('flightSearch', flightSearch);
        }

        action.search = params.toString();
        form.setAttribute('action', action.toString());

        event.preventDefault();
        event.stopImmediatePropagation();
        window.location.assign(action.toString());
      };

      form.addEventListener('submit', submitCanonicalSearch, true);

      form.querySelectorAll('input[name="origin"], input[name="destination"], input[name="depart_date"], input[name="return_date"], input[name="adults"], input[name="children"], input[name="infants"], select[name="cabin"]').forEach((field) => {
        field.addEventListener('input', () => syncFlightSearch(form));
        field.addEventListener('change', () => syncFlightSearch(form));
      });

      syncFlightSearch(form);

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
