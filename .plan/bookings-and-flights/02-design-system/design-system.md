# Design System: Bookings and Flights

**Version:** 2.0
**Last Updated:** 2026-05-09

> **How to use this template:** Replace all `{{placeholder}}` values with client-specific choices.
> Items marked `{{client_configurable}}` are expected to change per project.
> Everything else is the universal system that stays consistent across all builds.

---

## Brand Overview

**Brand Personality:**
- {{personality_trait_1}}
- {{personality_trait_2}}
- {{personality_trait_3}}

**Design Direction:** {{design_direction}}

---

## Theme System

This design system supports **light mode** (default) and **dark mode** via a theme class on the root element.

| Mode | Trigger | Notes |
|------|---------|-------|
| Light (default) | `:root` / `gh.theme-lit` | Default for all projects |
| Dark | `.theme-dark` | Inverts surface/text colors, keeps brand colors |
| Inverted section | `.theme-inverted` | Flips current theme within a section |

Dark mode overrides only surface, text, border, and shadow tokens. Brand colors (primary, secondary, accent) remain the same across both modes.

---

## Color Palette

### Primary Colors — `{{client_configurable}}`

**Base hue:** `212` (e.g. `21` for warm orange, `220` for blue)

| Token | Value | Usage |
|-------|-------|-------|
| `--primary` | `hsla(212, 55%, 23%, 1)` | Main brand color |
| `--primary-d-1` | Saturation −40%, lightness −13% | Hover states |
| `--primary-d-2` | Saturation −43%, lightness −26% | Active/pressed |
| `--primary-d-3` | Saturation −47%, lightness −37% | Deep accents |
| `--primary-d-4` | Saturation −55%, lightness −48% | Darkest tint |
| `--primary-l-1` | Lightness +8% | Light accent |
| `--primary-l-2` | Lightness +15% | Lighter accent |
| `--primary-l-3` | Lightness +22% | Focus rings |
| `--primary-l-4` | Lightness +30% | Subtle backgrounds |

**Opacity scale** (auto-generated from base): `--primary-5` through `--primary-90` in 10% steps.

### Secondary Colors — `{{client_configurable}}`

| Token | Value | Usage |
|-------|-------|-------|
| `--secondary` | `hsl(199, 29%, 58%)` | Accents, decorative elements |
| `--secondary-d-1` through `--secondary-d-4` | Darker shades | Hover/active states |
| `--secondary-l-1` through `--secondary-l-4` | Lighter shades | Backgrounds, highlights |

Opacity scale: `--secondary-5` through `--secondary-90`.

### Accent Colors — `{{client_configurable}}`

| Token | Value | Usage |
|-------|-------|-------|
| `--accent` | `hsl(36, 31%, 53%)` | CTAs, highlights |
| `--accent-d-1` through `--accent-d-4` | Darker shades | Hover/active states |
| `--accent-l-1` through `--accent-l-4` | Lighter shades | Focus rings, backgrounds |

Opacity scale: `--accent-5` through `--accent-90`.

### Surface & Text Colors

These swap automatically between light and dark mode.

| Token | Light Mode | Dark Mode | Usage |
|-------|------------|-----------|-------|
| `--bg-body` | `hsl(0, 0%, 90%)` | `hsl(0, 0%, 5%)` | Page background |
| `--bg-surface` | `hsl(0, 0%, 100%)` | `hsl(0, 0%, 15%)` | Cards, panels |
| `--text-body` | `hsl(0, 0%, 25%)` | `hsl(0, 0%, 75%)` | Body copy |
| `--text-title` | `hsl(0, 0%, 0%)` | `hsl(0, 0%, 100%)` | Headings |
| `--bg-subtle` | `hsl(0, 0%, 95%)` | `hsl(0, 0%, 10%)` | Alternating section backgrounds |
| `--bg-dark` | `hsl(0, 0%, 12%)` | `hsl(0, 0%, 3%)` | Dark sections (footer, overlays) |
| `--text-muted` | `var(--text-body-l-3)` | `var(--text-body-l-3)` | De-emphasized text |

Each text token includes an 8-step lighter ramp (`--text-body-l-1` through `--text-body-l-8`) for fine-grained contrast control.

### Border Colors

| Token | Light Mode | Dark Mode | Usage |
|-------|------------|-----------|-------|
| `--border-primary` | `hsla(0, 0%, 50%, 0.25)` | `hsla(0, 0%, 75%, 0.1)` | Default borders |

Includes opacity scale (`--border-primary-5` through `--border-primary-90`) and darker ramp (`--border-primary-d-1` through `--border-primary-d-8`).

### Shadow Colors

| Token | Light Mode | Dark Mode | Usage |
|-------|------------|-----------|-------|
| `--shadow-primary` | `hsla(0, 0%, 0%, 0.15)` | `hsla(0, 0%, 0%, 0.4)` | Base shadow color |

Includes opacity scale and darker ramp, same pattern as borders.

### Utility Colors

| Token | Light Mode | Dark Mode | Usage |
|-------|------------|-----------|-------|
| `--light` | `hsl(0, 0%, 100%)` | `hsl(0, 0%, 0%)` | Contextual "light" |
| `--dark` | `hsl(0, 0%, 0%)` | `hsl(0, 0%, 100%)` | Contextual "dark" |

Both include 10-step opacity scales. These **invert** in dark mode so `--dark-10` always means "a subtle hint of the contrasting color."

### Semantic Colors

| Token | Value | Usage |
|-------|-------|-------|
| `--success` | `hsl(136, 95%, 56%)` | Success messages, confirmations |
| `--warning` | `hsl(38, 92%, 50%)` | Warning messages |
| `--error` | `hsl(351, 95%, 56%)` | Error messages, required fields |
| `--info` | `hsl(199, 89%, 48%)` | Informational messages |

Each semantic color includes a full opacity scale (`-5` through `-90`).

---

## Typography

### Font Families — `{{client_configurable}}`

| Role | Font | Fallback Stack | Weight Range |
|------|------|----------------|--------------|
| Display | `Inter` | `Georgia, 'Times New Roman', serif` | 400, 500, 600, 700 |
| Body | `Inter` | `-apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif` | 400, 500, 700 |
| Mono | `JetBrains Mono` | `'Courier New', monospace` | 400 |

```css
:root {
  --font-display: 'Inter', Georgia, serif;
  --font-body: 'Inter', -apple-system, sans-serif;
  --font-mono: 'JetBrains Mono', 'Courier New', monospace;
}
```

### Fluid Type Scale — `{{client_configurable}}`

Uses `clamp()` for smooth scaling between `--min-screen-width` (320px) and `--max-screen-width` (1400px).

| Token | Clamp Value | Line Height | Usage |
|-------|-------------|-------------|-------|
| `--text-xs` | `clamp(0.89rem, calc(-0.01vw + 0.89rem), 0.88rem)` | 1.5 | Captions, labels |
| `--text-s` | `clamp(1rem, calc(0.26vw + 0.95rem), 1.17rem)` | 1.5 | Small text, metadata |
| `--text-m` | `clamp(1.13rem, calc(0.65vw + 1rem), 1.56rem)` | 1.4 | Body text (base) |
| `--text-l` | `clamp(1.27rem, calc(1.21vw + 1.02rem), 2.08rem)` | 1.3 | Lead paragraphs, H5 |
| `--text-xl` | `clamp(1.42rem, calc(2vw + 1.02rem), 2.78rem)` | 1.3 | H4 headings |
| `--text-2xl` | `clamp(1.6rem, calc(3.11vw + 0.98rem), 3.7rem)` | 1.2 | H3 headings |
| `--text-3xl` | `clamp(1.8rem, calc(4.64vw + 0.87rem), 4.93rem)` | 1.2 | H2 headings |
| `--text-4xl` | `clamp(2.03rem, calc(6.74vw + 0.68rem), 6.58rem)` | 1.1 | H1 / hero headings |

### Heading Defaults

| Element | Size Token | Line Height |
|---------|-----------|-------------|
| H1 | `--text-4xl` | 1.1 |
| H2 | `--text-3xl` | 1.2 |
| H3 | `--text-2xl` | 1.3 |
| H4 | `--text-xl` | 1.3 |
| H5 | `--text-l` | 1.3 |
| H6 | `--text-m` | 1.4 |

### Semantic Size Aliases

| Token | Maps To | Usage |
|-------|---------|-------|
| `--hero-title-size` | `var(--text-4xl)` | Hero section headings |
| `--post-title-size` | `var(--text-2xl)` | Blog post titles |
| `--nav-link-size` | `var(--text-s)` | Navigation links |

### Font Weights

| Name | Value | Usage |
|------|-------|-------|
| Regular | 400 | Body text |
| Medium | 500 | Emphasis, subheadings |
| Semibold | 600 | Buttons, labels |
| Bold | 700 | Headings, strong emphasis |

### Line Height Utilities

| Class | Value |
|-------|-------|
| `.line-height-xs` | 1.0 |
| `.line-height-s` | 1.2 |
| `.line-height-m` | 1.3 |
| `.line-height-l` | 1.4 |
| `.line-height-xl` | 1.5 |

### Text Utilities

| Class | Property |
|-------|----------|
| `.italic` | `font-style: italic` |
| `.bold` | `font-weight: bold` |
| `.lowercase` | `text-transform: lowercase` |
| `.uppercase` | `text-transform: uppercase` |
| `.underline` | `text-decoration: underline` |
| `.font-100` through `.font-900` | `font-weight: 100–900` |
| `.text-left` / `.text-center` / `.text-right` | `text-align` |

---

## Spacing — `{{client_configurable}}`

### Fluid Spacing Scale

Uses `clamp()` for responsive spacing. Scale values can be adjusted per-project.

| Token | Clamp Value | Usage |
|-------|-------------|-------|
| `--space-4xs` | `clamp(0.33rem, calc(-0.03vw + 0.33rem), 0.31rem)` | Micro gaps |
| `--space-3xs` | `clamp(0.41rem, calc(0.04vw + 0.4rem), 0.44rem)` | Tight gaps |
| `--space-2xs` | `clamp(0.51rem, calc(0.16vw + 0.48rem), 0.62rem)` | Small gaps |
| `--space-xs` | `clamp(0.64rem, calc(0.35vw + 0.57rem), 0.88rem)` | Compact spacing |
| `--space-s` | `clamp(0.8rem, calc(0.65vw + 0.67rem), 1.24rem)` | Small sections |
| `--space-m` | `clamp(1rem, calc(1.11vw + 0.78rem), 1.75rem)` | Default spacing |
| `--space-l` | `clamp(1.25rem, calc(1.81vw + 0.89rem), 2.47rem)` | Section padding |
| `--space-xl` | `clamp(1.56rem, calc(2.87vw + 0.99rem), 3.5rem)` | Large sections |
| `--space-2xl` | `clamp(1.95rem, calc(4.44vw + 1.07rem), 4.95rem)` | Major sections |
| `--space-3xl` | `clamp(2.44rem, calc(6.75vw + 1.09rem), 7rem)` | Hero sections |
| `--space-4xl` | `clamp(3.05rem, calc(10.13vw + 1.02rem), 9.89rem)` | Maximum spacing |

### Semantic Spacing Aliases

| Token | Maps To | Usage |
|-------|---------|-------|
| `--header-space` | `var(--space-s)` | Header padding |
| `--btn-space` | `var(--space-xs) var(--space-s)` | Button padding |
| `--card-space` | `var(--space-s)` | Card internal padding |
| `--footer-space` | `var(--space-s) var(--space-m)` | Footer padding |

---

## Layout

### Screen Width Range

| Token | Value | Usage |
|-------|-------|-------|
| `--min-screen-width` | `320px` | Minimum supported viewport |
| `--max-screen-width` | `1400px` | Fluid scaling stops here |

### Container Widths

| Name | Max Width | Usage |
|------|-----------|-------|
| container-sm | 640px | Narrow content, forms |
| container-md | 768px | Blog posts, articles |
| container-lg | 1024px | Standard content |
| container-xl | 1280px | Wide content |
| container-2xl | 1440px (140rem) | Full-width sections |

### Grid System

**Column variables** (defined as custom properties for reuse):

| Token | Value |
|-------|-------|
| `--columns-1` through `--columns-8` | `repeat(N, minmax(0, 1fr))` |

**Grid utilities:**

| Class | Behavior |
|-------|----------|
| `.row` | Grid, auto-flow column, justify start |
| `.column` | Grid, auto-flow row, align start |
| `.columns-2` through `.columns-8` | Fixed N-column grid |
| `.columns-min-5` through `.columns-min-70` | Auto-fit with `minmax(Nrem, 1fr)` |

**Flex utilities:**

| Class | Behavior |
|-------|----------|
| `.flex-row` / `.flex-column` | Flex direction |
| `.flex-1` / `.flex-2` / `.flex-3` | Flex grow |
| `.flex-wrap` / `.flex-nowrap` | Flex wrap |

**Grid span/start utilities:** `.col-span-2` through `.col-span-8`, `.col-start-1` through `.col-start-8`, `.row-span-2` through `.row-span-8`, `.row-start-1` through `.row-start-8`.

### Breakpoints

| Suffix | Max Width | Target |
|--------|-----------|--------|
| `--on-s` | 480px | Small phones |
| `--on-m` | 768px | Tablets |
| `--on-l` | 992px | Small laptops |
| `--on-xl` | 1400px | Desktops |

### Responsive Grid Utilities

Every grid utility has responsive variants using the `--on-{breakpoint}` suffix pattern:

```css
/* Example: 3 columns on desktop, 1 column on tablet */
<div class="columns-3 column--on-m">
```

Available at each breakpoint: `.column--on-{bp}`, `.row--on-{bp}`, `.columns-2--on-{bp}` through `.columns-6--on-{bp}`, `.col-span-1--on-{bp}` through `.col-span-6--on-{bp}`, `.col-start-1--on-{bp}` through `.col-start-6--on-{bp}`, `.row-span-1--on-{bp}` through `.row-span-6--on-{bp}`, `.row-start-1--on-{bp}` through `.row-start-6--on-{bp}`.

### Alignment Utilities

| Class | Property |
|-------|----------|
| `.items-left` / `.items-center` / `.items-right` | `justify-items` |
| `.content-left` / `.content-center` / `.content-right` | `justify-content` |
| `.items-top` / `.items-middle` / `.items-bottom` | `align-items` |
| `.content-top` / `.content-middle` / `.content-bottom` | `align-content` |
| `.items-stretch` / `.content-stretch` | Stretch |
| `.space-between` / `.space-around` | Distribution |
| `.self-left` / `.self-center` / `.self-right` | Horizontal self-placement |
| `.self-top` / `.self-middle` / `.self-bottom` | Vertical self-placement |
| `.self-stretch` | `align-self: stretch` |

### Sizing Utilities

| Class | Value |
|-------|-------|
| `.full-width` / `.full-height` | 100% |
| `.screen-width` / `.screen-height` | 100vw / 100vh |
| `.auto-width` / `.auto-height` | auto |
| `.width-10` through `.width-90` | Percentage widths (10% steps) |
| `.max-site-width` | `max-width: 140rem; width: 100%` |
| `.max-width-10` through `.max-width-140` | Max-width in rem (10rem steps) |

---

## Navigation Styling

> Decisions here prevent the 4+ correction rounds seen on the DJFuse nav links
> (weight, case, hover effect, CTA consistency all had to be corrected individually).

| Element | Decision | Notes |
|---------|----------|-------|
| **Nav link font weight** | {{nav_weight}} | e.g. 500, 600 |
| **Nav link text transform** | {{nav_transform}} | uppercase / none / capitalize |
| **Nav link letter-spacing** | {{nav_spacing}} | e.g. 0.1em for uppercase |
| **Nav link hover effect** | {{nav_hover}} | underline / color shift / custom (describe) |
| **CTA button font matches nav?** | {{nav_cta_match}} | If yes, same family + weight |
| **Mobile menu link style** | {{nav_mobile}} | Display font / body font, size |
| **Active page indicator** | {{nav_active}} | underline / bold / color / accent bar |

---

## Responsive Breakpoint Flow

> Standard column progression. Prevents the DJFuse testimonials issue where
> cards jumped from 1-column to 3-column with no intermediate 2-column step.

| Breakpoint | Width | Columns | Notes |
|------------|-------|---------|-------|
| Mobile | < 48em (768px) | 1 column | Stack everything |
| Tablet | 48em – 61.99em | 2 columns | Cards, testimonials side-by-side |
| Desktop | ≥ 62em (992px) | 3+ columns | Full grid layout |
| Large | ≥ 80em (1280px) | Max-width container | Content doesn't stretch further |

**Grid-to-stack pattern:**
```css
.card-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--space-m);
}

@media (min-width: 48em) {
  .card-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (min-width: 62em) {
  .card-grid { grid-template-columns: repeat(3, 1fr); }
}
```

---

## Image Conventions

> Prevents the DJFuse issue where small icon files were mixed with press photos
> and the GlenSea issue where image naming was inconsistent.

### Naming Convention

| Pattern | Example | Usage |
|---------|---------|-------|
| `{category}-{descriptor}-{size}.{ext}` | `hero-stage-lg.avif` | Responsive variants |
| `icon-{name}.svg` | `icon-vinyl.svg` | UI icons |
| `logo-{variant}.svg` | `logo-light.svg` | Brand logos |
| `bg-{section}.{ext}` | `bg-cta-gradient.webp` | Background images |

### Size Variants

| Suffix | Width | Usage |
|--------|-------|-------|
| `-sm` | 375px | Mobile |
| `-md` | 768px | Tablet |
| `-lg` | 1200px+ | Desktop |

### Format Priority

1. **AVIF** — best compression (if browser support sufficient)
2. **WebP** — wide support, good compression
3. **JPG** — fallback for older browsers
4. **SVG** — icons and logos only

---

## Components

### Buttons

| Variant | Background | Text | Border | Usage |
|---------|------------|------|--------|-------|
| Primary (default) | `var(--primary)` | `#fff` | `var(--primary-d-1)` | Main CTAs |
| Secondary | `var(--secondary)` | `#fff` | `var(--secondary-d-1)` | Secondary actions |
| Tertiary | `var(--tertiary)` | `#fff` | `var(--tertiary-d-1)` | Tertiary brand actions |
| Ghost | `transparent` | `var(--dark-80)` | `transparent` | Minimal actions |
| Slight | `var(--bg-surface)` | `var(--dark-80)` | `var(--border-primary)` | Subtle actions |
| No-bg | `transparent` | `var(--dark-80)` | `transparent` | Text-only actions |

**Button Sizes:**

| Size | Class | Padding | Font Size |
|------|-------|---------|-----------|
| Small | `.btn.small` | `var(--space-xs) var(--space-s)` | `var(--text-s)` |
| Default | `.btn` | `var(--space-xs) var(--space-s)` | `var(--text-m)` |
| Large | `.btn.large` | `var(--space-s) var(--space-m)` | `var(--text-l)` |

**Button states:** Hover darkens background (`--primary-d-1`) and lifts (`translateY(-0.1rem)`). Focus shows `4px solid var(--primary-l-3)` outline with `2px` offset.

```css
.btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-3xs);
  padding: var(--space-xs) var(--space-s);
  background: var(--primary);
  color: #fff;
  font-size: var(--text-m);
  font-weight: 600;
  border-radius: var(--radius-m);
  border: 1px solid var(--primary-d-1);
  box-shadow: var(--shadow-m);
  transition: all 0.25s ease-in-out;
  outline: 0;
  cursor: pointer;
}
```

### Badges

| Property | Value |
|----------|-------|
| Padding | `var(--space-2xs) var(--space-s)` |
| Background | `var(--dark-10)` |
| Color | `var(--primary)` (or `var(--secondary)` for `.badge.secondary`) |
| Font size | `var(--text-s)` |
| Border radius | `var(--radius-full)` |

### Links

| Property | Value |
|----------|-------|
| Color | `var(--primary)` |
| Font size | `var(--text-m)` |
| Font weight | 600 |
| Underline | `box-shadow: 0 2px 0 var(--primary-20)` |
| Hover | Underline darkens to `var(--primary-40)` |
| Focus | Background `var(--primary-10)` |

Variants: `.link.secondary`, `.link.tertiary`.

### Cards

| Property | Value |
|----------|-------|
| Display | `grid` |
| Gap | `var(--space-xs)` |
| Padding | `var(--space-m)` |
| Background | `var(--bg-surface)` |
| Color | `var(--text-body)` |
| Border radius | `var(--radius-m)` |
| Shadow | `var(--shadow-m)` |
| Line height | 1.3 |

Variants: `.card.primary` (brand bg), `.card.secondary` (secondary bg).

### Forms — Inputs

| Property | Value |
|----------|-------|
| Padding | `var(--space-xs) var(--space-s)` |
| Background | `var(--dark-5)` |
| Color | `var(--text-title)` |
| Font size | `var(--text-m)` |
| Border | `1px solid var(--border-primary)` |
| Border radius | `var(--radius-m)` |
| Shadow | `var(--shadow-xs)` |

**States:**
- **Focus:** `background: var(--primary-20)`, `border-color: var(--primary)`, `box-shadow: var(--shadow-l)`
- **Hover:** `border-color: var(--primary)`, `box-shadow: var(--shadow-l)`
- **Invalid:** `border-color: var(--error)`, `background: var(--error-10)`
- **Disabled:** `opacity: 0.75`, `cursor: not-allowed`, `background: var(--dark-10)`

### Forms — Select

Same styling as inputs. Inherits all states.

### Forms — Checkbox & Radio

| Property | Checkbox | Radio |
|----------|----------|-------|
| Size | `clamp(1.13rem, calc(-0.37vw + 1.45rem), 1.38rem)` | Same |
| Border | `2px solid var(--dark-40)` | Same |
| Border radius | `var(--radius-s)` | `var(--radius-full)` |
| Checked bg | `var(--primary)` | Transparent (dot is `var(--primary)`) |
| Hover | `border-color: var(--primary)` | Same |
| Focus | `3px solid var(--primary-l-3)`, `2px` offset | Same |

### Icons

| Size | Class | Width |
|------|-------|-------|
| Small | `.icon.small` | `var(--space-l)` |
| Default | `.icon` | `var(--space-2xl)` |
| Large | `.icon.large` | `var(--space-3xl)` |

Variants: `.icon.secondary`, `.icon.tertiary`, `.icon.outline` (border circle), `.icon.filled` (background circle).

### Avatars

| Size | Class | Dimensions |
|------|-------|------------|
| Small | `.avatar.small` | `var(--space-l)` |
| Default | `.avatar` | `var(--space-2xl)` |
| Large | `.avatar.large` | `var(--space-4xl)` |

All avatars: `border-radius: 100%`, `box-shadow: var(--shadow-m)`, `object-fit: cover`.

### Dividers

| Orientation | Class | Behavior |
|-------------|-------|----------|
| Horizontal | `.divider` | Full width, 1px height, `var(--border-primary)` |
| Vertical | `.divider.vertical` | 1px width, full height |

---

## Effects

### Shadows (as CSS custom properties)

| Token | Value | Usage |
|-------|-------|-------|
| `--shadow-xs` | `0 1px 2px var(--shadow-primary)` | Subtle elevation |
| `--shadow-s` | `0 1.5px 3px var(--shadow-primary)` | Inputs |
| `--shadow-m` | `0 2px 6px var(--shadow-primary)` | Cards, buttons |
| `--shadow-l` | `0 3px 12px var(--shadow-primary)` | Dropdowns, focus |
| `--shadow-xl` | `0 6px 48px var(--shadow-primary)` | Modals, popovers |

Shadow color (`--shadow-primary`) auto-adjusts between light and dark mode.

### Border Radius (fluid)

| Token | Value | Usage |
|-------|-------|-------|
| `--radius-xs` | `clamp(0.25rem, 0.25rem, 0.25rem)` | Subtle rounding |
| `--radius-s` | `clamp(0.38rem, calc(-0.19vw + 0.54rem), 0.5rem)` | Checkboxes, small elements |
| `--radius-m` | `clamp(0.63rem, calc(-0.19vw + 0.79rem), 0.75rem)` | Buttons, cards, inputs |
| `--radius-l` | `clamp(1rem, calc(-0.37vw + 1.32rem), 1.25rem)` | Large cards |
| `--radius-xl` | `clamp(1.63rem, calc(-0.56vw + 2.11rem), 2rem)` | Hero elements |
| `--radius-full` | `999rem` | Pills, avatars |

### Transitions

| Name | Value | Usage |
|------|-------|-------|
| transition-fast | 150ms ease | Micro-interactions |
| transition | 200ms ease | Default |
| transition-slow | 300ms ease | Larger elements |
| transition-slower | 500ms ease | Page transitions |
| `.transition-global` | `all 0.3s` | Utility class for general transitions |

### Interaction States

| Token | Value | Usage |
|-------|-------|-------|
| `--hover-lift` | `translateY(-2px)` | Buttons, cards on hover |
| `--hover-opacity` | `0.85` | Images, overlays on hover |
| `--disabled-opacity` | `0.5` | Disabled buttons, inputs |

Never hardcode `translateY(-2px)`, `opacity: 0.85`, or `opacity: 0.5` — always use the token.

### Backdrop Blur

| Class | Value |
|-------|-------|
| `.bg-blur-xs` | `blur(2px)` |
| `.bg-blur-s` | `blur(4px)` |
| `.bg-blur-m` | `blur(8px)` |
| `.bg-blur-l` | `blur(16px)` |
| `.bg-blur-xl` | `blur(32px)` |

### Filters

| Class | Value |
|-------|-------|
| `.grayscale` | `filter: grayscale(1)` |

---

## Utility Classes Reference

### Opacity

`.opacity-0` through `.opacity-100` in 10% steps.

### Aspect Ratio

| Class | Value |
|-------|-------|
| `.aspect-1` | `1` (square) |
| `.aspect-4-3` / `.aspect-3-4` | `4/3` / `3/4` |
| `.aspect-3-2` / `.aspect-2-3` | `3/2` / `2/3` |
| `.aspect-16-9` / `.aspect-9-16` | `16/9` / `9/16` |

### Object Fit

`.fit-contain`, `.fit-cover`, `.fit-fill`

### Border Utilities

`.border`, `.border-left`, `.border-right`, `.border-top`, `.border-bottom` — all `1px solid`.

### Radius Utilities

`.radius-xs`, `.radius-s`, `.radius-m`, `.radius-l`, `.radius-xl`, `.radius-full`

### Shadow Utilities

`.shadow-xs`, `.shadow-s`, `.shadow-m`, `.shadow-l`, `.shadow-xl`

### Positioning

| Class | Value |
|-------|-------|
| `.relative` / `.absolute` / `.sticky` / `.fixed` | Position |
| `.inset-0` | `inset: 0` |
| `.top-0` / `.bottom-0` / `.left-0` / `.right-0` | Edge positioning |

### Visibility & Overflow

| Class | Value |
|-------|-------|
| `.display-none` | `display: none` |
| `.visible` / `.hidden` | `visibility` |
| `.overflow-hidden` / `.overflow-auto` | `overflow` |
| `.overflow-x-hidden` / `.overflow-x-auto` | Horizontal overflow |
| `.overflow-y-hidden` / `.overflow-y-auto` | Vertical overflow |

### Miscellaneous

| Class | Value |
|-------|-------|
| `.list-none` | `list-style-type: none` |
| `.white-space-nowrap` | `white-space: nowrap` |
| `.pointer` / `.not-allowed` / `.cursor-auto` | Cursor styles |
| `.no-pointer-events` | `pointer-events: none` |
| `.rotate-90` / `.rotate-180` | Transform rotation |

### Z-Index Scale

| Class | Value |
|-------|-------|
| `.z--1` | -1 |
| `.z-0` | 0 |
| `.z-1` | 1 |
| `.z-10` | 10 |
| `.z-100` | 100 |
| `.z-1000` | 1000 |
| `.z-10000` | 10000 |

---

## Accessibility

### Color Contrast

All text colors must meet WCAG 2.1 AA standards:
- **Normal text:** 4.5:1 minimum
- **Large text (18px+):** 3:1 minimum
- **UI components:** 3:1 minimum

### Focus States

- All interactive elements must have visible focus indicators
- Focus ring: 2px solid with 2px offset
- Never remove focus outlines without replacement

### Motion

- Respect `prefers-reduced-motion` media query
- Provide static alternatives for animations
- Keep animations under 500ms

---

## Implementation Notes

### Complete Token Reference

All tokens should be defined as CSS custom properties in `:root`. Here is the full structure:

```css
:root {
  /* === Screen Range === */
  --min-screen-width: 320px;
  --max-screen-width: 1400px;

  /* === Brand Colors ({{client_configurable}}) === */
  --primary: hsla(212, 55%, 23%, 1);
  /* Auto-generated: --primary-5 through --primary-90 (opacity) */
  /* Auto-generated: --primary-d-1 through --primary-d-4 (darker) */
  /* Auto-generated: --primary-l-1 through --primary-l-4 (lighter) */

  --secondary: hsl(199, 29%, 58%);
  /* Same shade/opacity pattern as primary */

  --accent: hsl(36, 31%, 53%);
  /* Same shade/opacity pattern as primary */

  /* === Surface & Text (auto-swap in dark mode) === */
  --bg-body: hsl(0, 0%, 90%);
  --bg-surface: hsl(0, 0%, 100%);
  --text-body: hsl(0, 0%, 25%);
  --text-title: hsl(0, 0%, 0%);
  --bg-subtle: hsl(0, 0%, 95%);
  --bg-dark: hsl(0, 0%, 12%);
  --text-muted: var(--text-body-l-3);

  /* === Borders, Shadows, Light/Dark === */
  --border-primary: hsla(0, 0%, 50%, 0.25);
  --shadow-primary: hsla(0, 0%, 0%, 0.15);
  --light: hsl(0, 0%, 100%);
  --dark: hsl(0, 0%, 0%);

  /* === Semantic Colors === */
  --success: hsl(136, 95%, 56%);
  --warning: hsl(38, 92%, 50%);
  --error: hsl(351, 95%, 56%);
  --info: hsl(199, 89%, 48%);

  /* === Typography ({{client_configurable}}) === */
  --font-display: 'Inter', Georgia, serif;
  --font-body: 'Inter', -apple-system, sans-serif;
  --font-mono: 'JetBrains Mono', 'Courier New', monospace;

  /* === Fluid Type Scale === */
  --text-xs: clamp(0.89rem, calc(-0.01vw + 0.89rem), 0.88rem);
  --text-s: clamp(1rem, calc(0.26vw + 0.95rem), 1.17rem);
  --text-m: clamp(1.13rem, calc(0.65vw + 1rem), 1.56rem);
  --text-l: clamp(1.27rem, calc(1.21vw + 1.02rem), 2.08rem);
  --text-xl: clamp(1.42rem, calc(2vw + 1.02rem), 2.78rem);
  --text-2xl: clamp(1.6rem, calc(3.11vw + 0.98rem), 3.7rem);
  --text-3xl: clamp(1.8rem, calc(4.64vw + 0.87rem), 4.93rem);
  --text-4xl: clamp(2.03rem, calc(6.74vw + 0.68rem), 6.58rem);

  /* === Semantic Size Aliases === */
  --hero-title-size: var(--text-4xl);
  --post-title-size: var(--text-2xl);
  --nav-link-size: var(--text-s);

  /* === Fluid Spacing Scale === */
  --space-4xs: clamp(0.33rem, calc(-0.03vw + 0.33rem), 0.31rem);
  --space-3xs: clamp(0.41rem, calc(0.04vw + 0.4rem), 0.44rem);
  --space-2xs: clamp(0.51rem, calc(0.16vw + 0.48rem), 0.62rem);
  --space-xs: clamp(0.64rem, calc(0.35vw + 0.57rem), 0.88rem);
  --space-s: clamp(0.8rem, calc(0.65vw + 0.67rem), 1.24rem);
  --space-m: clamp(1rem, calc(1.11vw + 0.78rem), 1.75rem);
  --space-l: clamp(1.25rem, calc(1.81vw + 0.89rem), 2.47rem);
  --space-xl: clamp(1.56rem, calc(2.87vw + 0.99rem), 3.5rem);
  --space-2xl: clamp(1.95rem, calc(4.44vw + 1.07rem), 4.95rem);
  --space-3xl: clamp(2.44rem, calc(6.75vw + 1.09rem), 7rem);
  --space-4xl: clamp(3.05rem, calc(10.13vw + 1.02rem), 9.89rem);

  /* === Semantic Spacing Aliases === */
  --header-space: var(--space-s);
  --btn-space: var(--space-xs) var(--space-s);
  --card-space: var(--space-s);
  --footer-space: var(--space-s) var(--space-m);

  /* === Grid Columns === */
  --columns-1: repeat(1, minmax(0, 1fr));
  --columns-2: repeat(2, minmax(0, 1fr));
  --columns-3: repeat(3, minmax(0, 1fr));
  --columns-4: repeat(4, minmax(0, 1fr));
  --columns-5: repeat(5, minmax(0, 1fr));
  --columns-6: repeat(6, minmax(0, 1fr));
  --columns-7: repeat(7, minmax(0, 1fr));
  --columns-8: repeat(8, minmax(0, 1fr));

  /* === Border Radius (fluid) === */
  --radius-xs: clamp(0.25rem, 0.25rem, 0.25rem);
  --radius-s: clamp(0.38rem, calc(-0.19vw + 0.54rem), 0.5rem);
  --radius-m: clamp(0.63rem, calc(-0.19vw + 0.79rem), 0.75rem);
  --radius-l: clamp(1rem, calc(-0.37vw + 1.32rem), 1.25rem);
  --radius-xl: clamp(1.63rem, calc(-0.56vw + 2.11rem), 2rem);
  --radius-full: 999rem;

  /* === Shadows === */
  --shadow-xs: 0 1px 2px var(--shadow-primary);
  --shadow-s: 0 1.5px 3px var(--shadow-primary);
  --shadow-m: 0 2px 6px var(--shadow-primary);
  --shadow-l: 0 3px 12px var(--shadow-primary);
  --shadow-xl: 0 6px 48px var(--shadow-primary);
}
```

### Dark Mode Override Structure

```css
.theme-dark {
  --bg-body: hsl(0, 0%, 5%);
  --bg-surface: hsl(0, 0%, 15%);
  --text-body: hsl(0, 0%, 75%);
  --text-title: hsl(0, 0%, 100%);
  --bg-subtle: hsl(0, 0%, 10%);
  --bg-dark: hsl(0, 0%, 3%);
  --text-muted: var(--text-body-l-3);
  --border-primary: hsla(0, 0%, 75%, 0.1);
  --shadow-primary: hsla(0, 0%, 0%, 0.4);
  --light: hsl(0, 0%, 0%);
  --dark: hsl(0, 0%, 100%);
  /* All opacity/shade ramps for the above also override */
}
```

### Usage Example

```css
.hero__title {
  font-family: var(--font-display);
  font-size: var(--hero-title-size);
  color: var(--text-title);
  margin-bottom: var(--space-m);
}

.btn-primary {
  background: var(--primary);
  color: #fff;
  padding: var(--btn-space);
  border-radius: var(--radius-m);
  transition: all 0.25s ease-in-out;
}
```

### Per-Project Customization Checklist

When starting a new project, fill in these `{{client_configurable}}` values:

- [ ] **Brand colors:** primary hue/sat/light, secondary, accent
- [ ] **Font pairings:** display, body, mono
- [ ] **Spacing scale:** adjust clamp values if needed
- [ ] **Typography scale:** adjust clamp values if needed
- [ ] **Navigation styling decisions:** weight, transform, hover, etc.
- [ ] **Brand personality & design direction**
