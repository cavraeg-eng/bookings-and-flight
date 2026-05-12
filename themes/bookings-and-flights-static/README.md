# Bookings and Flights Static Theme

A minimal static WordPress theme with page templates, CSS design tokens, and vanilla JavaScript.

## Quick Start

1. **Replace placeholders** in all files:
   - `Bookings and Flights` → Your project name (e.g., "Acme Corp")
   - `bookings_and_flights` → Lowercase with underscores (e.g., "acme_corp")
   - `http://bookings-and-flights.local` → Your website URL
   - `Cav` → Your name or company
   - `` → Your website

2. **Add your fonts** to `assets/fonts/`:
   - Replace placeholder font files with your actual .woff2 files
   - Update `assets/css/fonts.css` with correct font family names and file paths
   - Update `assets/css/tokens.css` with your font family names

3. **Customize design tokens** in `assets/css/tokens.css`:
   - Colors (primary, secondary, accent, neutrals)
   - Typography (font families, sizes)
   - Spacing scale
   - Shadows, borders, transitions

4. **Update navigation** in `header.php` and `footer.php`:
   - Add/remove menu items
   - Update CTA button text and link

5. **Create page templates** by copying `page-home.php`:
   - Create `page-about.php`, `page-contact.php`, etc.
   - Add corresponding CSS files in `assets/css/`
   - Update `functions.php` CSS/JS mappings

## File Structure

```
bookings_and_flights-static/
├── assets/
│   ├── css/
│   │   ├── fonts.css          # @font-face declarations
│   │   ├── tokens.css         # CSS custom properties
│   │   ├── base.css           # Reset, base styles, utilities
│   │   ├── header.css         # Header, navigation, buttons
│   │   ├── mobile-nav.css     # Mobile navigation overlay
│   │   ├── footer.css         # Footer layouts
│   │   ├── home.css           # Home page styles
│   │   ├── legal.css          # Legal pages (privacy/terms)
│   │   └── 404.css            # 404 error page
│   ├── fonts/                 # .woff2 font files
│   ├── images/                # Theme images
│   └── js/
│       ├── header.js          # Header scroll, mobile menu, focus trap
│       ├── reveal.js          # Scroll reveal animations
│       ├── legal.js           # Legal page TOC
│       └── 404.js             # 404 page animations
├── functions.php              # Theme setup, asset enqueuing
├── header.php                 # Site header (skip link, font preloads)
├── footer.php                 # Site footer (dynamic via global options)
├── index.php                  # Fallback template
├── page-home.php              # Home page template
├── page-legal.php             # Legal pages (privacy/terms shared template)
├── 404.php                    # Custom 404 page
├── style.css                  # Theme metadata
└── README.md                  # This file
```

## CSS Load Order

1. `fonts.css` - Font declarations (no dependencies)
2. `tokens.css` - CSS variables (depends on fonts)
3. `base.css` - Reset and base styles (depends on tokens)
4. `header.css` - Header, navigation, buttons (depends on base)
5. `mobile-nav.css` - Mobile navigation overlay (depends on header)
6. `footer.css` - Footer layouts (depends on mobile-nav)
7. `[page].css` - Page-specific styles (depends on tokens, base)

## JavaScript

- **header.js** - Loaded on all pages
  - Smart hero background detection (light/dark header mode)
  - Scroll-based header styling
  - Mobile menu with focus trap
  - Accessibility: escape key, aria attributes

- **reveal.js** - Loaded on all pages
  - Intersection Observer for scroll reveals
  - Supports `data-reveal-delay` attribute
  - Respects `prefers-reduced-motion`

## Adding New Pages

1. Create `page-{slug}.php` template
2. Create `assets/css/{slug}.css` stylesheet
3. Add to `$css_map` in `functions.php`
4. (Optional) Add page-specific JS to `$js_map`

## Content Manager Integration

This theme includes fallback functions for all content manager helpers:

### Page-Level Fields
- `bookings_and_flights_field($key, $default)` - Get field value
- `bookings_and_flights_image_url($id, $size)` - Get image URL
- `bookings_and_flights_image($id, $size, $attrs)` - Get image HTML
- `bookings_and_flights_gallery($key)` - Get gallery image IDs
- `bookings_and_flights_repeater($key)` - Get repeater rows
- `bookings_and_flights_the_field($key, $default)` - Echo escaped text
- `bookings_and_flights_the_content($key, $default)` - Echo HTML (wysiwyg)
- `bookings_and_flights_the_attr($key, $default)` - Echo attribute-safe value
- `bookings_and_flights_the_url($key, $default)` - Echo URL

### Global Options (footer, social, contact, CTA)
- `bookings_and_flights_option($key, $default)` - Get global option value
- `bookings_and_flights_the_option($key, $default)` - Echo escaped global option
- `bookings_and_flights_the_option_url($key, $default)` - Echo global option as URL

If the plugin is deactivated, all functions return/output defaults gracefully.

## CSS Utilities

### data-grain
Add `data-grain` attribute to any element for a film grain overlay:
```html
<section class="hero" data-grain>
  <!-- grain overlay auto-applied via ::before -->
</section>
```

### Section Defaults
Use `.section` and `.section__content` for immersive full-viewport sections:
```html
<section class="section">
  <div class="section__content">
    <!-- auto-centered, auto-spaced, 60vh min-height -->
  </div>
</section>
```

### Hidden Attribute
`[hidden]` always works, even when CSS sets `display` explicitly:
```html
<div class="form-success" hidden>Thank you!</div>
<!-- Always hidden, never overridden by display:flex -->
```

### Screen Reader Only
Use `.sr-only` to visually hide content while keeping it accessible to screen readers:
```html
<label class="sr-only" for="email">Email address</label>
<a href="#main" class="sr-only">Skip to main content</a>
```

### Active Menu Items
WordPress auto-generates `.current-menu-item` and `.current-menu-ancestor` classes.
The theme styles these automatically for all three navigation contexts:
- **Dark header** (default): accent color + visible underline
- **Light/scrolled header**: primary color + visible underline
- **Mobile nav**: accent color

No additional markup needed — works out of the box with `wp_nav_menu()`.

## Cache-Busting

All `wp_enqueue_style()` and `wp_enqueue_script()` calls use `filemtime()` instead of
hardcoded version strings. This ensures browsers always load fresh assets after edits
without manual version bumps:
```php
filemtime( get_template_directory() . '/assets/css/tokens.css' )
```

## Security Hardening

The theme includes standard WordPress hardening in `functions.php`:

| Filter/Action | Purpose |
|--------------|---------|
| `comments_open` → `__return_false` | Disable comments (spam vector) |
| `pings_open` → `__return_false` | Disable pingbacks |
| `wp_head` → remove `wp_generator` | Hide WordPress version from source |
| `xmlrpc_enabled` → `__return_false` | Disable XML-RPC (brute-force vector) |

These are appropriate for static/brochure sites that never use comments or XML-RPC.

## SEOPress Compatibility

Two filters for the standard SEO plugin (no-ops if SEOPress is absent):

| Filter | Purpose |
|--------|---------|
| `seopress_pro_breadcrumbs_css` → `__return_empty_string` | Suppress unused breadcrumb CSS |
| `wp_sitemaps_enabled` → `__return_false` | Disable WP core sitemaps (SEOPress provides its own) |

## HTML Comment Stripping

Developer `<!-- comments -->` are automatically stripped from frontend output via
`ob_start()` on `template_redirect`. This reduces page weight and prevents information leakage.

**Preserved comments:**
- IE conditionals (`<!--[if ...]>`)
- `<!--more-->` and `<!--nextpage-->` (WordPress content markers)

**Not affected:** Admin pages, source files (comments remain for developer navigation).

## Placeholder Variables

| Placeholder | Description | Example |
|-------------|-------------|---------|
| `Bookings and Flights` | Display name | Acme Corp |
| `bookings_and_flights` | Function prefix | acme_corp |
| `http://bookings-and-flights.local` | Website URL | https://acme.com |
| `Cav` | Developer name | John Doe |
| `` | Developer URL | https://johndoe.dev |
| `{{hero_*}}` | Hero section content | — |
| `{{features_*}}` | Features section | — |
| `{{about_*}}` | About section | — |
| `{{testimonial_*}}` | Testimonials | — |
| `{{cta_*}}` | CTA section | — |
| `{{contact_*}}` | Contact info | — |
| `{{social_*}}` | Social links | — |
| `{{footer_*}}` | Footer content | — |

**Note:** Footer content (social links, contact info, description) is now dynamic
via the global options page. `{{placeholder}}` tokens in the footer serve as
fallback defaults when the plugin is deactivated.

## Browser Support

- Chrome, Firefox, Safari, Edge (latest 2 versions)
- iOS Safari, Android Chrome
- Respects `prefers-reduced-motion`
- Progressive enhancement for older browsers
