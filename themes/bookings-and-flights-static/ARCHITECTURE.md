# Theme Architecture Diagram

> Auto-generated reference for the WP-Forge boilerplate theme structure.

## File Tree Overview

```mermaid
graph TB
    subgraph THEME["🗂 Theme Root"]
        style_css["style.css<br/><small>Theme metadata only</small>"]
        functions["functions.php<br/><small>Setup, enqueue, security,<br/>content manager fallbacks</small>"]
        header["header.php<br/><small>DOCTYPE → &lt;header&gt; + mobile nav</small>"]
        footer["footer.php<br/><small>&lt;footer&gt; → &lt;/html&gt;</small>"]
        index["index.php<br/><small>Fallback template (WP loop)</small>"]
        page_home["page-home.php<br/><small>Template: Home</small>"]
        page_legal["page-legal.php<br/><small>Template: Legal Page<br/>(Privacy / Terms shared)</small>"]
        four04["404.php<br/><small>Page Not Found</small>"]
        readme["README.md"]
    end

    subgraph ASSETS["📁 assets/"]
        htaccess[".htaccess<br/><small>CORS, caching, compression</small>"]

        subgraph CSS["📁 css/"]
            fonts_css["fonts.css<br/><small>@font-face declarations</small>"]
            tokens_css["tokens.css<br/><small>CSS custom properties<br/>(colors, spacing, type, etc.)</small>"]
            base_css["base.css<br/><small>Reset, base styles, utilities</small>"]
            header_css["header.css<br/><small>Header, nav, buttons</small>"]
            mobile_nav_css["mobile-nav.css<br/><small>Mobile navigation</small>"]
            footer_css["footer.css<br/><small>Footer layouts</small>"]
            home_css["home.css<br/><small>Home page sections</small>"]
        end

        subgraph JS["📁 js/"]
            header_js["header.js<br/><small>Scroll, mobile menu,<br/>hero detection, focus trap</small>"]
            reveal_js["reveal.js<br/><small>IntersectionObserver<br/>scroll animations</small>"]
        end

        subgraph FONTS["📁 fonts/"]
            font_files["*.woff2<br/><small>(empty in boilerplate)</small>"]
        end

        subgraph IMAGES["📁 images/"]
            image_files["Theme images<br/><small>(empty in boilerplate)</small>"]
        end
    end
```

## Template Hierarchy & Page Rendering

```mermaid
flowchart TD
    REQUEST(["🌐 HTTP Request"]) --> WP{"WordPress<br/>Template Resolver"}

    WP -->|"is_front_page() or<br/>Template: Home"| HOME["page-home.php"]
    WP -->|"Template: Legal Page<br/>(privacy / terms slug)"| LEGAL["page-legal.php"]
    WP -->|"is_404()"| FOUR04["404.php"]
    WP -->|"Any other page/post"| INDEX["index.php"]

    HOME --> HEADER["get_header()  →  header.php"]
    LEGAL --> HEADER
    FOUR04 --> HEADER
    INDEX --> HEADER

    HOME --> FOOTER["get_footer()  →  footer.php"]
    LEGAL --> FOOTER
    FOUR04 --> FOOTER
    INDEX --> FOOTER

    subgraph HEADER_OUT["header.php outputs"]
        direction TB
        H1["&lt;!DOCTYPE html&gt; + &lt;head&gt;"]
        H2["Font preloads"]
        H3["wp_head() — enqueues CSS/JS"]
        H4["&lt;header&gt; with dual logo variants"]
        H5["Desktop nav (wp_nav_menu)"]
        H6["Mobile nav overlay"]
        H7["&lt;div id='main-content'&gt;"]
        H1 --> H2 --> H3 --> H4 --> H5 --> H6 --> H7
    end

    subgraph FOOTER_OUT["footer.php outputs"]
        direction TB
        F1["&lt;/div&gt; closes #main-content"]
        F2["About column + social links"]
        F3["Quick Links column"]
        F4["Contact column (address, phone, email)"]
        F5["Copyright + legal links"]
        F6["wp_footer() + &lt;/body&gt;&lt;/html&gt;"]
        F1 --> F2 --> F3 --> F4 --> F5 --> F6
    end

    HEADER --> HEADER_OUT
    FOOTER --> FOOTER_OUT
```

## CSS Load Order & Dependencies

```mermaid
flowchart LR
    FONTS["fonts.css<br/><small>@font-face</small>"] --> TOKENS["tokens.css<br/><small>CSS variables</small>"]
    TOKENS --> BASE["base.css<br/><small>Reset + utilities</small>"]
    BASE --> HEADER["header.css<br/><small>Header, nav, buttons</small>"]
    HEADER --> MOBILE["mobile-nav.css<br/><small>Mobile navigation</small>"]
    MOBILE --> FOOTER["footer.css<br/><small>Footer layouts</small>"]
    TOKENS --> PAGE["[page].css<br/><small>Page-specific styles</small>"]
    BASE --> PAGE

    subgraph CSS_MAP["$css_map in functions.php"]
        direction TB
        M1["page-home.php  → home.css"]
        M2["page-about.php  → about.css"]
        M3["page-contact.php → contact.css"]
        M4["page-legal.php  → legal.css"]
        M5["is_404()  → 404.css"]
    end

    PAGE -.- CSS_MAP
```

## JavaScript Architecture

```mermaid
flowchart TB
    subgraph GLOBAL_JS["Global Scripts (all pages)"]
        direction LR
        HJS["header.js"]
        RJS["reveal.js"]
    end

    subgraph HEADER_JS_DETAIL["header.js responsibilities"]
        direction TB
        HJ1["Hero background sampling<br/>(light/dark header mode)"]
        HJ2["Scroll-based header styling"]
        HJ3["Mobile menu toggle + focus trap"]
        HJ4["Escape key to close menu"]
        HJ5["ARIA attribute management"]
    end

    subgraph REVEAL_JS_DETAIL["reveal.js responsibilities"]
        direction TB
        RJ1["IntersectionObserver on .reveal elements"]
        RJ2["data-reveal-delay support"]
        RJ3["prefers-reduced-motion respect"]
    end

    HJS --> HEADER_JS_DETAIL
    RJS --> REVEAL_JS_DETAIL

    subgraph PAGE_JS["Page-Specific Scripts ($js_map)"]
        direction TB
        PJ1["page-legal.php → legal.js<br/><small>TOC generation from h2 headings</small>"]
        PJ2["is_404() → 404.js<br/><small>404 page animations</small>"]
    end
```

## functions.php Internal Structure

```mermaid
flowchart TB
    subgraph SETUP["Theme Setup (after_setup_theme)"]
        S1["add_theme_support: title-tag, post-thumbnails"]
        S2["register_nav_menus: primary, footer"]
        S3["Block editor support"]
    end

    subgraph NAV["Navigation"]
        N1["Fallback menu function<br/>(Home, About, Services, Contact)"]
        N2["WP_Forge_Menu_Walker<br/>(custom li/a class injection,<br/>sub-menu support)"]
    end

    subgraph ENQUEUE["Asset Enqueuing (wp_enqueue_scripts)"]
        E1["CSS: fonts → tokens → base → header → mobile-nav → footer → page-specific"]
        E2["JS: header.js + reveal.js → page-specific"]
        E3["$css_map + $js_map template-to-file routing"]
    end

    subgraph SECURITY["Security Hardening"]
        SEC1["Disable comments + pingbacks"]
        SEC2["Remove wp_generator meta"]
        SEC3["Disable XML-RPC"]
    end

    subgraph SEO["SEOPress Compatibility"]
        SEO1["Suppress breadcrumb CSS"]
        SEO2["Disable core sitemaps"]
    end

    subgraph PERF["Performance"]
        P1["Strip HTML comments from frontend<br/>(preserves IE conditionals, more, nextpage)"]
    end

    subgraph FALLBACKS["Content Manager Fallbacks"]
        FB1["field / the_field / the_content"]
        FB2["image / image_url / gallery"]
        FB3["repeater"]
        FB4["option / the_option / the_option_url"]
        FB5["the_attr / the_url"]
    end
```

## Home Page Sections (page-home.php)

```mermaid
flowchart TB
    subgraph HOME["page-home.php"]
        direction TB
        HERO["🖼 Hero Section<br/><small>Background image + overlay<br/>tagline, title, description<br/>2× CTA buttons</small>"]
        FEATURES["⭐ Features Section<br/><small>Header (tagline, title, desc)<br/>3× feature-cards with icons</small>"]
        ABOUT["📖 About Section<br/><small>Text + image split layout<br/>tagline, title, description<br/>Learn More CTA</small>"]
        TESTIMONIALS["💬 Testimonials Section<br/><small>Header (tagline, title)<br/>3× testimonial-cards<br/>stars, quote, author, role</small>"]
        CTA["📣 CTA Section<br/><small>Title, description,<br/>primary CTA button</small>"]

        HERO --> FEATURES --> ABOUT --> TESTIMONIALS --> CTA
    end

    HERO -.- R1[".reveal + data-reveal-delay"]
    FEATURES -.- R1
    ABOUT -.- R1
    TESTIMONIALS -.- R1
    CTA -.- R1
```

## Design Tokens (tokens.css)

```mermaid
mindmap
  root((CSS Tokens))
    Colors
      Primary (3 shades)
      Secondary (2 shades)
      Accent (2 shades)
      Neutrals (grey-100→900)
      Semantic (text, bg, surface)
      Status (success, error, warning)
    Typography
      font-display (serif)
      font-body (sans-serif)
      Fluid sizes (xs→5xl via clamp)
      Line heights (tight→relaxed)
    Spacing
      Fluid scale (3xs→3xl via clamp)
      Container max + padding
    Visual
      Border radius (sm→full)
      Shadows (sm→xl)
      Transitions (easing + durations)
    Interaction
      hover-lift
      hover-opacity
      disabled-opacity
    Z-Index
      base → above → header → overlay → modal → toast
```

## Navigation System

```mermaid
flowchart LR
    subgraph MENUS["Registered Menus"]
        PRIMARY["primary<br/>'Primary Menu'"]
        FMENU["footer<br/>'Footer Menu'"]
    end

    subgraph DESKTOP["Desktop Header Nav"]
        DN["wp_nav_menu<br/>walker: WP_Forge_Menu_Walker<br/>classes: header__nav-item,<br/>header__nav-link"]
    end

    subgraph MOBILE["Mobile Nav Overlay"]
        MN["wp_nav_menu<br/>walker: WP_Forge_Menu_Walker<br/>classes: mobile-nav__item,<br/>mobile-nav__link"]
    end

    subgraph FALLBACK["Fallback Menu"]
        FB["Home / About / Services / Contact<br/>Auto-detects context via menu_class"]
    end

    PRIMARY --> DN
    PRIMARY --> MN
    PRIMARY -.->|"no menu assigned"| FB
```

## Data Flow: Content Manager Integration

```mermaid
flowchart LR
    subgraph PLUGIN["Content Manager Plugin<br/>(when active)"]
        PF["Page-level fields"]
        GO["Global options page"]
    end

    subgraph FALLBACK_FN["Fallback Functions<br/>(functions.php)"]
        FF1["field() → returns default"]
        FF2["option() → returns default"]
        FF3["image_url() → returns ''"]
    end

    subgraph TEMPLATES["Templates"]
        T_FOOTER["footer.php<br/>social links, contact info"]
        T_HOME["page-home.php<br/>hero, features, etc."]
    end

    PLUGIN -->|"Active"| TEMPLATES
    FALLBACK_FN -->|"Plugin inactive"| TEMPLATES
```

## Performance & Security

```mermaid
flowchart TB
    subgraph PERFORMANCE["⚡ Performance"]
        P1["Cache-busting via filemtime()"]
        P2["HTML comment stripping (ob_start)"]
        P3[".htaccess: gzip, expires headers"]
        P4["Font preloading in &lt;head&gt;"]
        P5["Lazy loading on non-hero images"]
    end

    subgraph SECURITY["🔒 Security"]
        S1["Comments disabled"]
        S2["Pingbacks disabled"]
        S3["wp_generator removed"]
        S4["XML-RPC disabled"]
        S5["esc_html / esc_url / esc_attr output"]
    end

    subgraph A11Y["♿ Accessibility"]
        A1["Skip to main content link"]
        A2["ARIA labels on nav, buttons, social"]
        A3["Focus trap in mobile menu"]
        A4["Semantic HTML (header, nav, main, footer)"]
        A5["prefers-reduced-motion support"]
    end
```
