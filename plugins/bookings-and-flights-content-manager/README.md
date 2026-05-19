# Bookings and Flights Content Manager

A WordPress plugin for managing editable content (text, images, galleries) for the Bookings and Flights Static theme.

## Quick Start

1. **Replace placeholders** in all files:
   - `Bookings and Flights` → Your project name (e.g., "Acme Corp")
   - `bookings_and_flights` → Lowercase with underscores (e.g., "acme_corp")
   - `BOOKINGS_AND_FLIGHTS` → Uppercase with underscores (e.g., "ACME_CORP")
   - `Bookings_And_Flights` → PascalCase class prefix (e.g., "Acme_Corp")
   - `http://bookings-and-flights.local` → Your website URL
   - `Cav` → Your name or company
   - `` → Your website

2. **Rename the main plugin file** from `bookings_and_flights-content-manager.php` to match your project slug.

3. **Configure templates** in `includes/class-config.php`:
   - Add your page templates to `get_template_map()`

4. **Create field definitions** in `fields/`:
   - Copy `home.php` as a template
   - Create one file per page template

## File Structure

```
bookings_and_flights-content-manager/
├── assets/
│   ├── css/
│   │   └── admin.css          # Admin UI styles
│   └── js/
│       └── admin.js           # Media uploader, gallery, repeater, color picker, oEmbed, conditionals
├── fields/
│   └── home.php               # Example field definitions
├── includes/
│   ├── class-config.php        # Template mapping, field file list, CPT definitions, meta prefix
│   ├── class-export-import.php # JSON export/import with media sideloading
│   ├── class-fields.php        # Field registry and normalization
│   ├── class-meta-boxes.php    # Meta box rendering and saving
│   ├── class-options.php       # Global options page (social, contact, CTA)
│   ├── class-plugin.php        # Main plugin class
│   ├── class-revisions.php     # WordPress revision support for custom fields
│   ├── class-sanitizer.php     # Input sanitization by type
│   └── helpers.php             # Template helper functions
├── bookings_and_flights-content-manager.php  # Main plugin file
└── README.md                  # This file
```

## Field Types

| Type | Description | Sanitizer |
|------|-------------|-----------|
| `text` | Single line text input | `sanitize_text_field()` |
| `textarea` | Multi-line text input | `sanitize_textarea_field()` |
| `wysiwyg` | Rich text editor (TinyMCE) | `wp_kses_post()` |
| `url` | URL input with validation | `esc_url_raw()` |
| `email` | Email input with validation | `sanitize_email()` |
| `number` | Numeric input (supports min/max/step) | `floatval()` |
| `image` | Single image upload | `absint()` (attachment ID) |
| `gallery` | Multiple image upload | `array_map('absint')` |
| `color` | Color picker | `sanitize_hex_color()` |
| `select` | Dropdown select | `sanitize_text_field()` |
| `radio` | Radio button group | `sanitize_text_field()` |
| `checkbox` | Single checkbox | `0` or `1` |
| `repeater` | Repeatable row group | Custom per subfield |
| `link` | URL + Label side-by-side | `esc_url_raw()` + `sanitize_text_field()` |
| `date` | Date or datetime-local input | Regex `YYYY-MM-DD` or `YYYY-MM-DDTHH:MM` |
| `oembed` | URL with live embed preview | `esc_url_raw()` |

## Meta Key Prefix

All page field meta keys are stored with a `bookings_and_flights_` prefix to prevent collisions with other plugins. For example, a field key `hero_heading` is stored as `bookings_and_flights_hero_heading` in `wp_postmeta`.

Theme helpers accept the **unprefixed** key — the prefix is applied transparently:

```php
// Theme template — uses unprefixed key
$title = bookings_and_flights_field('hero_heading');
```

Export JSON also uses unprefixed keys for portability. The prefix is applied on import.

The prefix is configured in `class-config.php` via `Config::META_PREFIX`.

## Conditional Field Visibility

Fields can be shown/hidden based on another field's value using the `show_when` property:

```php
'show_video' => array(
    'label' => 'Show Video?',
    'type'  => 'checkbox',
),
'video_url' => array(
    'label'     => 'Video URL',
    'type'      => 'oembed',
    'show_when' => array('field' => 'show_video', 'value' => '1'),
),
```

**Behavior:**
- Hidden fields still save their data (toggling never loses content)
- Server-side evaluation prevents FOUC (hidden class applied in PHP)
- Supports checkbox, radio, select, and text controllers
- Single condition per field (no AND/OR in v1)
- Only top-level fields can be controllers (not repeater subfields)

## Revision Support

Custom field changes are tracked in WordPress revisions. When you edit a page and save, all field values are copied to the revision. You can:

- **Browse Revisions** — see a "Custom Fields" entry in the diff screen showing all field labels and values
- **Restore** — restoring an older revision reverts all custom field values

Complex types (repeater, gallery, link) display as JSON in the diff. Revisions respect the `WP_POST_REVISIONS` constant for limits.

## Field Definition Format

```php
<?php
defined('ABSPATH') || exit;

$fields = array(
    // Text field
    'hero_title' => array(
        'label'    => __('Hero Title', 'bookings_and_flights-content-manager'),
        'type'     => 'text',
        'default'  => __('Welcome', 'bookings_and_flights-content-manager'),
        'help'     => __('Main headline for the hero section.', 'bookings_and_flights-content-manager'),
        'required' => true,
    ),

    // Image field
    'hero_image_id' => array(
        'label'   => __('Hero Image', 'bookings_and_flights-content-manager'),
        'type'    => 'image',
        'default' => 0,
    ),

    // Textarea with custom rows
    'hero_description' => array(
        'label' => __('Description', 'bookings_and_flights-content-manager'),
        'type'  => 'textarea',
        'rows'  => 6,
    ),

    // Number with min/max
    'items_count' => array(
        'label' => __('Number of Items', 'bookings_and_flights-content-manager'),
        'type'  => 'number',
        'min'   => 1,
        'max'   => 10,
        'step'  => 1,
    ),

    // Select dropdown
    'layout_style' => array(
        'label'   => __('Layout Style', 'bookings_and_flights-content-manager'),
        'type'    => 'select',
        'options' => array(
            'grid'    => __('Grid', 'bookings_and_flights-content-manager'),
            'list'    => __('List', 'bookings_and_flights-content-manager'),
            'masonry' => __('Masonry', 'bookings_and_flights-content-manager'),
        ),
        'default' => 'grid',
    ),

    // Repeater field
    'team_members' => array(
        'label'     => __('Team Members', 'bookings_and_flights-content-manager'),
        'type'      => 'repeater',
        'subfields' => array(
            'name' => array(
                'label' => __('Name', 'bookings_and_flights-content-manager'),
                'type'  => 'text',
            ),
            'role' => array(
                'label' => __('Role', 'bookings_and_flights-content-manager'),
                'type'  => 'text',
            ),
            'bio' => array(
                'label' => __('Bio', 'bookings_and_flights-content-manager'),
                'type'  => 'textarea',
            ),
        ),
    ),

    // Gallery field
    'portfolio_images' => array(
        'label' => __('Portfolio Images', 'bookings_and_flights-content-manager'),
        'type'  => 'gallery',
        'help'  => __('Select multiple images for the portfolio.', 'bookings_and_flights-content-manager'),
    ),

    // Color picker
    'accent_color' => array(
        'label'   => __('Accent Color', 'bookings_and_flights-content-manager'),
        'type'    => 'color',
        'default' => '#1a365d',
    ),

    // Link field (URL + Label)
    'external_link' => array(
        'label' => __('External Link', 'bookings_and_flights-content-manager'),
        'type'  => 'link',
    ),

    // Date field
    'event_date' => array(
        'label' => __('Event Date', 'bookings_and_flights-content-manager'),
        'type'  => 'date',
    ),

    // Date + time field
    'event_datetime' => array(
        'label' => __('Event Date & Time', 'bookings_and_flights-content-manager'),
        'type'  => 'date',
        'time'  => true,
    ),

    // oEmbed field
    'promo_video' => array(
        'label' => __('Promo Video', 'bookings_and_flights-content-manager'),
        'type'  => 'oembed',
    ),

    // Conditional field
    'show_cta' => array(
        'label' => __('Show CTA?', 'bookings_and_flights-content-manager'),
        'type'  => 'checkbox',
    ),
    'cta_link' => array(
        'label'     => __('CTA Link', 'bookings_and_flights-content-manager'),
        'type'      => 'link',
        'show_when' => array('field' => 'show_cta', 'value' => '1'),
    ),
);

return array(
    'template' => 'page-home.php',
    'fields'   => $fields,
);
```

## Template Helper Functions

Use these functions in your theme templates:

```php
// Get field value
$title = bookings_and_flights_field('hero_title', 'Default Title');

// Output escaped text
bookings_and_flights_the_field('hero_title', 'Default Title');

// Output HTML (for wysiwyg)
bookings_and_flights_the_content('about_content', '<p>Default content</p>');

// Output URL
bookings_and_flights_the_url('hero_cta_url', '/contact/');

// Output attribute
bookings_and_flights_the_attr('hero_title', 'Default');

// Get image URL
$url = bookings_and_flights_image_url($image_id, 'large');

// Get image HTML tag
echo bookings_and_flights_image($image_id, 'full', array('class' => 'hero-image'));

// Get gallery image IDs
$ids = bookings_and_flights_gallery('portfolio_images');
foreach ($ids as $id) {
    echo bookings_and_flights_image($id, 'medium');
}

// Get repeater data
$members = bookings_and_flights_repeater('team_members');
foreach ($members as $member) {
    echo '<h3>' . esc_html($member['name']) . '</h3>';
    echo '<p>' . esc_html($member['role']) . '</p>';
}

// Get link field (returns array with 'url' and 'label')
$link = bookings_and_flights_link('external_link');
if (!empty($link['url'])) {
    printf('<a href="%s">%s</a>', esc_url($link['url']), esc_html($link['label']));
}

// Get oEmbed HTML
echo bookings_and_flights_oembed('promo_video');
```

## Section Grouping

Fields are automatically grouped by their key prefix. Configure prefixes in `class-fields.php`:

```php
private function get_section_prefixes($template) {
    return array(
        'hero_'         => __('Hero Section', 'bookings_and_flights-content-manager'),
        'features_'     => __('Features Section', 'bookings_and_flights-content-manager'),
        'about_'        => __('About Section', 'bookings_and_flights-content-manager'),
        // Add more prefixes as needed
    );
}
```

## Adding New Templates

1. Add template to `class-config.php`:
   ```php
   'page-services.php' => __('Services Page Content', 'bookings_and_flights-content-manager'),
   ```

2. Create `fields/services.php` with field definitions

3. Return the template name in the fields file:
   ```php
   return array(
       'template' => 'page-services.php',
       'fields'   => $fields,
   );
   ```

## Placeholder Variables

| Placeholder | Description | Example |
|-------------|-------------|---------|
| `Bookings and Flights` | Display name | Acme Corp |
| `bookings_and_flights` | Function prefix (lowercase) | acme_corp |
| `BOOKINGS_AND_FLIGHTS` | Constant prefix (uppercase) | ACME_CORP |
| `Bookings_And_Flights` | PHP class prefix (PascalCase) | Acme_Corp |
| `http://bookings-and-flights.local` | Website URL | https://acme.com |
| `Cav` | Developer name | John Doe |
| `` | Developer URL | https://johndoe.dev |

## Global Options

The plugin includes a global options page (Settings → Bookings and Flights) for site-wide
content that isn't tied to a specific page:

### Default Global Fields

| Key | Type | Section | Description |
|-----|------|---------|-------------|
| `social_facebook` | url | Social | Facebook page URL |
| `social_instagram` | url | Social | Instagram profile URL |
| `social_x` | url | Social | X (Twitter) profile URL |
| `social_tiktok` | url | Social | TikTok profile URL |
| `social_youtube` | url | Social | YouTube channel URL |
| `social_linkedin` | url | Social | LinkedIn profile URL |
| `contact_email` | email | Contact | Contact email address |
| `contact_phone` | text | Contact | Phone number |
| `contact_address_1` | text | Contact | Address line 1 |
| `contact_address_2` | text | Contact | Address line 2 |
| `contact_location` | text | Contact | Short location label |
| `footer_description` | textarea | Footer | Footer about text |
| `cta_heading` | text | CTA | Default CTA heading |
| `cta_text` | textarea | CTA | Default CTA description |
| `cta_button_text` | text | CTA | Default CTA button text |
| `cta_button_url` | url | CTA | Default CTA button URL |

### Usage in Templates

```php
// Get a global option
$email = bookings_and_flights_option('contact_email', 'default@example.com');

// Output escaped global option
bookings_and_flights_the_option('footer_description', 'Default description');

// Output as URL
bookings_and_flights_the_option_url('social_instagram', '#');
```

### Adding Custom Fields

Filter `bookings_and_flights_global_fields` to add or remove fields:

```php
add_filter('bookings_and_flights_global_fields', function($fields) {
    $fields['custom_field'] = array(
        'label'   => 'Custom Field',
        'type'    => 'text',
        'section' => 'general',
        'default' => '',
    );
    return $fields;
});
```

---

### Theme Integration

The theme should include fallback functions in case the plugin is deactivated:

```php
// In theme's functions.php — Page-level field fallbacks
if (!function_exists('bookings_and_flights_field')) {
    function bookings_and_flights_field($key, $default = '') { return $default; }
}
if (!function_exists('bookings_and_flights_image_url')) {
    function bookings_and_flights_image_url($id = 0, $size = 'full') { return ''; }
}
if (!function_exists('bookings_and_flights_image')) {
    function bookings_and_flights_image($id = 0, $size = 'full', $attrs = array()) { return ''; }
}
if (!function_exists('bookings_and_flights_gallery')) {
    function bookings_and_flights_gallery($key) { return array(); }
}
if (!function_exists('bookings_and_flights_repeater')) {
    function bookings_and_flights_repeater($key) { return array(); }
}
if (!function_exists('bookings_and_flights_link')) {
    function bookings_and_flights_link($key) { return array('url' => '', 'label' => ''); }
}
if (!function_exists('bookings_and_flights_oembed')) {
    function bookings_and_flights_oembed($key, $args = array()) { return ''; }
}
if (!function_exists('bookings_and_flights_the_field')) {
    function bookings_and_flights_the_field($key, $default = '') { echo esc_html($default); }
}
if (!function_exists('bookings_and_flights_the_content')) {
    function bookings_and_flights_the_content($key, $default = '') { echo wp_kses_post($default); }
}
if (!function_exists('bookings_and_flights_the_attr')) {
    function bookings_and_flights_the_attr($key, $default = '') { echo esc_attr($default); }
}
if (!function_exists('bookings_and_flights_the_url')) {
    function bookings_and_flights_the_url($key, $default = '') { echo esc_url($default); }
}

// Global option fallbacks
if (!function_exists('bookings_and_flights_option')) {
    function bookings_and_flights_option($key, $default = '') { return $default; }
}
if (!function_exists('bookings_and_flights_the_option')) {
    function bookings_and_flights_the_option($key, $default = '') { echo esc_html($default); }
}
if (!function_exists('bookings_and_flights_the_option_url')) {
    function bookings_and_flights_the_option_url($key, $default = '') { echo esc_url($default); }
}
```

All 14 fallbacks are included in the theme boilerplate's `functions.php`.

---

## Export/Import

The plugin includes a built-in JSON export/import system under **Tools → Bookings and Flights Export/Import**. It exports all page meta fields, global options, and any CPTs declared in config — with media sideloading, taxonomy support, deterministic identity matching, and host-aware URL rewriting.

### Features

- **Zero-hardcoding** — automatically discovers pages, fields, and CPTs from existing config
- **Media sideloading** — images downloaded from source site, alt text preserved
- **Same-site detection** — `attachment_url_to_postid()` skips sideload when URL is already local
- **URL rewriting** — host-aware, recursive, scoped to string values only
- **Taxonomy support** — exports terms + assignments, creates missing terms on import
- **Deterministic matching** — pages matched by template+slug, CPTs by source_id→slug→title cascade
- **Schema versioning** — forward-compatible with migration filter for older versions
- **PRG redirect** — prevents double-import on browser refresh
- **SSRF protection** — only `http`/`https` schemes, validated via `wp_http_validate_url()`

### JSON Structure

```json
{
  "plugin": "bookings_and_flights-content-manager",
  "schema_version": 1,
  "plugin_version": "1.0.0",
  "exported_at": "2025-01-15T12:00:00+00:00",
  "site_url": "https://source-site.com",
  "pages": {
    "page-home.php/home": {
      "post_name": "home",
      "source_post_id": 42,
      "template": "page-home.php",
      "fields": { "hero_title": "Welcome", "hero_image_id": { "_attachment_url": "...", "_alt": "...", "id": 55 } }
    }
  },
  "global_options": { "social_facebook": "https://facebook.com/example", ... },
  "custom_post_types": {
    "bookings_and_flights_team": [
      {
        "source_post_id": 99,
        "title": "Jane Doe",
        "post_name": "jane-doe",
        "post_content": "...",
        "post_excerpt": "...",
        "status": "publish",
        "menu_order": 1,
        "featured_image": { "_attachment_url": "...", "_alt": "...", "id": 55 },
        "meta": { "_bookings_and_flights_team_role": "Lead Developer" },
        "taxonomies": { "team_role": [ { "slug": "lead", "name": "Lead" } ] }
      }
    ]
  }
}
```

The `schema_version` is an integer that increments only when the JSON structure has breaking changes. On import, files with a newer schema are rejected; older schemas trigger the `bookings_and_flights_import_migrate` filter.

### Declaring CPTs for Export

Add entries to `Bookings_And_Flights_Config::get_cpt_definitions()` in `class-config.php`:

```php
'bookings_and_flights_team' => array(
    'label'        => __( 'Team Members', 'bookings_and_flights-content-manager' ),
    'meta_prefix'  => '_bookings_and_flights_team_',
    'meta_fields'  => array(
        '_bookings_and_flights_team_role'    => 'text',
        '_bookings_and_flights_team_bio'     => 'textarea',
        '_bookings_and_flights_team_website' => 'url',
    ),
    'image_keys'   => array( '_bookings_and_flights_team_photo' ),
    'gallery_keys' => array(),
    'taxonomies'   => array( 'team_department' ),
    'orderby'      => 'menu_order',
    'order'        => 'ASC',
),
```

| Key | Type | Purpose |
|-----|------|---------|
| `label` | string | Human-readable name for admin notices |
| `meta_prefix` | string | Guard: only export meta keys starting with this prefix |
| `meta_fields` | array | Allowlist + type map: `meta_key => field_type` for sanitization |
| `image_keys` | array | Meta keys storing a single attachment ID |
| `gallery_keys` | array | Meta keys storing comma-separated attachment IDs |
| `taxonomies` | array | Taxonomy slugs to export terms + assignments for |
| `orderby` | string | WP_Query orderby param (default: `menu_order`) |
| `order` | string | ASC/DESC (default: `ASC`) |

### Identity Matching

**Pages:** matched by `template + post_name` (slug). If no match is found, the page is skipped with a warning.

**CPT Posts:** matched by cascade:
1. `_source_post_id` meta → exact re-import match
2. `post_name` (slug) within the CPT
3. `post_title` (last resort)
4. If no match → create new post

After import, `_source_post_id` meta is stored on the local post for future re-imports.

### Filter Hooks

All filters are optional — the system works without any of them.

| Hook | Args | Purpose |
|------|------|---------|
| `bookings_and_flights_cpt_definitions` | `$definitions` | Add/modify CPT export configs |
| `bookings_and_flights_export_pages` | `$pages` | Filter exported page data |
| `bookings_and_flights_export_global_options` | `$options` | Filter exported global options |
| `bookings_and_flights_export_cpt_posts` | `$posts, $post_type` | Filter exported CPT posts |
| `bookings_and_flights_export_data` | `$data` | Filter entire export payload before encoding |
| `bookings_and_flights_import_pre_save_meta` | `$value, $key, $definition, $post_id` | Filter meta value before saving on import |
| `bookings_and_flights_import_migrate` | `$data, $schema_version` | Transform data from older schema versions |

### Acceptance Test Checklist

| # | Scenario | Expected Outcome |
|---|----------|-----------------|
| 1 | Export with no CPTs configured | JSON has empty `custom_post_types: {}`, no errors |
| 2 | Export + import on same site | All data unchanged, no duplicate media, no sideloads |
| 3 | Import on different site | Media sideloaded, alt text restored, URLs rewritten |
| 4 | Re-import same file twice | Idempotent — same data, no duplicates |
| 5 | Two pages sharing a template | Each exported with slug, each matched independently |
| 6 | CPT with duplicate titles | Matched by source_id or slug, not title collision |
| 7 | Import with failed image URL | Warning logged, field set to 0, rest of import continues |
| 8 | CPT meta key not in `meta_fields` | Warning logged, key skipped (not imported) |
| 9 | Import older schema_version | Migration filter fires, import proceeds |
| 10 | Import newer schema_version | Rejected with clear error message |
| 11 | Taxonomy terms missing on target | Created automatically, assigned to posts |
| 12 | Global option with `image` type | Attachment ID resolved to URL on export, sideloaded on import |
| 13 | Browser refresh after import | PRG redirect prevents double processing |
| 14 | Malformed JSON upload | Clear error with `json_last_error_msg()` |
