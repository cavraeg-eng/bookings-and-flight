/**
 * Header & Mobile Navigation
 * Bookings and Flights Static Theme
 *
 * Features:
 * - Smart hero background detection (light/dark header mode)
 * - Scroll event handling for header styling
 * - Mobile menu toggle with accessibility
 * - Focus trap for mobile menu
 * - Escape key handling
 * - Body scroll prevention when menu open
 * - RequestAnimationFrame throttling
 * - Respects prefers-reduced-motion
 */

(function() {
  'use strict';

  // Configuration
  const CONFIG = {
    scrollThreshold: 50,
    menuBreakpoint: 1024,
  };

  // State
  let ticking = false;
  let isMenuOpen = false;

  // Elements (cached)
  let header, menuToggleLight, menuToggleDark, mobileNav;

  // Throttle helper using requestAnimationFrame
  function throttle(fn) {
    return function(...args) {
      if (!ticking) {
        requestAnimationFrame(() => {
          fn.apply(this, args);
          ticking = false;
        });
        ticking = true;
      }
    };
  }

  // Smart hero background detection
  function detectHeroBackground() {
    const firstSection = document.querySelector('#main-content > section, #main-content > div > section, section:first-of-type, .hero');
    if (!firstSection) return 'light';

    const styles = window.getComputedStyle(firstSection);
    const bgColor = styles.backgroundColor;
    const bgImage = styles.backgroundImage;

    if (bgImage && bgImage !== 'none') {
      if (bgImage.includes('gradient')) {
        return getGradientBrightness(bgImage);
      }
      const img = firstSection.querySelector('img[src]');
      if (img && img.complete) {
        return getImageBrightness(img) < 128 ? 'dark' : 'light';
      }
      return 'dark';
    }

    const brightness = getColorBrightness(bgColor);
    return brightness < 128 ? 'dark' : 'light';
  }

  function getGradientBrightness(gradient) {
    const colorRegex = /rgba?\((\d+),\s*(\d+),\s*(\d+)/g;
    let match;
    let totalBrightness = 0;
    let colorCount = 0;

    while ((match = colorRegex.exec(gradient)) !== null) {
      const r = parseInt(match[1]);
      const g = parseInt(match[2]);
      const b = parseInt(match[3]);
      totalBrightness += (r * 299 + g * 587 + b * 114) / 1000;
      colorCount++;
    }

    if (colorCount > 0) {
      const avgBrightness = totalBrightness / colorCount;
      return avgBrightness < 128 ? 'dark' : 'light';
    }

    return 'dark';
  }

  function getColorBrightness(color) {
    color = color.trim();

    const rgbMatch = color.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
    if (rgbMatch) {
      const r = parseInt(rgbMatch[1]);
      const g = parseInt(rgbMatch[2]);
      const b = parseInt(rgbMatch[3]);
      return (r * 299 + g * 587 + b * 114) / 1000;
    }

    const hexMatch = color.match(/^#([a-f0-9]{3}|[a-f0-9]{6})$/i);
    if (hexMatch) {
      let hex = hexMatch[1];
      if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
      }
      const r = parseInt(hex.substr(0, 2), 16);
      const g = parseInt(hex.substr(2, 2), 16);
      const b = parseInt(hex.substr(4, 2), 16);
      return (r * 299 + g * 587 + b * 114) / 1000;
    }

    return 0;
  }

  function getImageBrightness(imgElement) {
    try {
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      canvas.width = 1;
      canvas.height = 1;
      ctx.drawImage(imgElement, 0, 0, 1, 1);
      const data = ctx.getImageData(0, 0, 1, 1).data;
      return (data[0] * 299 + data[1] * 587 + data[2] * 114) / 1000;
    } catch (e) {
      return 0;
    }
  }

  function updateHeaderVariant() {
    const heroType = detectHeroBackground();

    if (heroType === 'light') {
      header.classList.add('on-light');
    } else {
      header.classList.remove('on-light');
    }
  }

  // Scroll handler with throttling
  const handleScroll = throttle(() => {
    if (window.scrollY > CONFIG.scrollThreshold) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  });

  // Mobile menu with focus trap
  function toggleMobileMenu() {
    isMenuOpen = !isMenuOpen;

    const wasExpanded = !isMenuOpen;

    menuToggleLight.setAttribute('aria-expanded', String(isMenuOpen));
    menuToggleDark.setAttribute('aria-expanded', String(isMenuOpen));

    menuToggleLight.classList.toggle('active', isMenuOpen);
    menuToggleDark.classList.toggle('active', isMenuOpen);

    mobileNav.classList.toggle('active', isMenuOpen);
    mobileNav.setAttribute('aria-hidden', String(wasExpanded));

    document.body.style.overflow = wasExpanded ? '' : 'hidden';

    // Focus management
    if (isMenuOpen) {
      const firstLink = mobileNav.querySelector('a');
      if (firstLink) {
        setTimeout(() => firstLink.focus(), 100);
      }
    } else {
      const activeToggle = document.activeElement;
      if (activeToggle === menuToggleLight || activeToggle === menuToggleDark) {
        // Already on a toggle
      } else {
        menuToggleLight.focus();
      }
    }
  }

  function closeMobileMenu() {
    if (isMenuOpen) {
      isMenuOpen = false;

      menuToggleLight.setAttribute('aria-expanded', 'false');
      menuToggleDark.setAttribute('aria-expanded', 'false');

      menuToggleLight.classList.remove('active');
      menuToggleDark.classList.remove('active');

      mobileNav.classList.remove('active');
      mobileNav.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';

      menuToggleLight.focus();
    }
  }

  // Close all open sub-menus
  function closeAllSubMenus() {
    document.querySelectorAll('[aria-haspopup="true"][aria-expanded="true"]').forEach(link => {
      link.setAttribute('aria-expanded', 'false');
    });
  }

  // Keyboard-accessible sub-menus
  function setupSubMenuKeyboard() {
    const parentLinks = document.querySelectorAll('[aria-haspopup="true"]');

    parentLinks.forEach(link => {
      link.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          const isExpanded = link.getAttribute('aria-expanded') === 'true';

          closeAllSubMenus();

          if (!isExpanded) {
            link.setAttribute('aria-expanded', 'true');
            const subMenu = link.closest('.header__nav-item')?.querySelector('.header__sub-menu');
            if (subMenu) {
              const firstChild = subMenu.querySelector('a');
              if (firstChild) firstChild.focus();
            }
          }
        }
      });

      const parentItem = link.closest('.header__nav-item');
      if (!parentItem) return;

      parentItem.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          const wasExpanded = link.getAttribute('aria-expanded') === 'true';
          if (wasExpanded) {
            e.stopPropagation();
            link.setAttribute('aria-expanded', 'false');
            link.focus();
          }
        }
      });

      const subMenu = parentItem.querySelector('.header__sub-menu');
      if (!subMenu) return;

      const subLinks = subMenu.querySelectorAll('a');
      const lastSubLink = subLinks[subLinks.length - 1];

      if (lastSubLink) {
        lastSubLink.addEventListener('keydown', (e) => {
          if (e.key === 'Tab' && !e.shiftKey) {
            link.setAttribute('aria-expanded', 'false');
          }
        });
      }
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('.header__nav-item')) {
        closeAllSubMenus();
      }
    });
  }

  // Focus trap for mobile menu
  function setupFocusTrap() {
    if (!mobileNav) return;

    const focusableElements = mobileNav.querySelectorAll(
      'a, button, [tabindex]:not([tabindex="-1"])'
    );
    const firstFocusable = focusableElements[0];
    const lastFocusable = focusableElements[focusableElements.length - 1];

    mobileNav.addEventListener('keydown', (e) => {
      if (e.key === 'Tab' && isMenuOpen) {
        if (e.shiftKey && document.activeElement === firstFocusable) {
          e.preventDefault();
          lastFocusable.focus();
        } else if (!e.shiftKey && document.activeElement === lastFocusable) {
          e.preventDefault();
          firstFocusable.focus();
        }
      }
    });
  }

  // Initialize
  function init() {
    header = document.querySelector('.header');
    menuToggleLight = document.querySelector('[data-menu-toggle="light"]');
    menuToggleDark = document.querySelector('[data-menu-toggle="dark"]');
    mobileNav = document.querySelector('[data-mobile-nav]');

    if (!header) return;

    // Setup scroll handler
    window.addEventListener('scroll', handleScroll, { passive: true });

    // Initial header variant detection
    updateHeaderVariant();
    window.addEventListener('load', updateHeaderVariant);
    setTimeout(updateHeaderVariant, 100);

    // Setup menu toggles
    if (menuToggleLight) {
      menuToggleLight.addEventListener('click', toggleMobileMenu);
    }
    if (menuToggleDark) {
      menuToggleDark.addEventListener('click', toggleMobileMenu);
    }

    // Close menu on link click
    if (mobileNav) {
      mobileNav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', closeMobileMenu);
      });
    }

    // Setup keyboard-accessible sub-menus
    setupSubMenuKeyboard();

    // Setup focus trap
    setupFocusTrap();

    // Escape key handler
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && isMenuOpen) {
        closeMobileMenu();
      }
    });

    // Initial scroll check
    handleScroll();
  }

  // Cleanup on page unload
  function cleanup() {
    window.removeEventListener('scroll', handleScroll);
  }

  window.addEventListener('beforeunload', cleanup);
  window.addEventListener('DOMContentLoaded', init);

})();
