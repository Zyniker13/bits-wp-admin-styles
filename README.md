# Bristlecone Admin Styles

WordPress plugin by [Bristlecone IT Services](https://bristleconeit.com). Curated, self-hosted fonts for WordPress admin list tables.

Plugin homepage: [https://bristleconeit.com/bristlecone-admin-styles/](https://bristleconeit.com/bristlecone-admin-styles/).

Development repository: [https://github.com/Zyniker13/bits-wp-admin-styles](https://github.com/Zyniker13/bits-wp-admin-styles).

The GitHub repo is `bits-wp-admin-styles`. The plugin folder / text domain / bootstrap slug is `bristlecone-admin-styles` (plural).

## Requirements

- WordPress 6.4+
- PHP 8.4+

## Install

1. Copy this directory into `wp-content/plugins/` (folder name can stay `bits-wp-admin-styles` or be `bristlecone-admin-styles`).
2. Activate **Bristlecone Admin Styles**.
3. Open **Bristlecone → Admin Styles**.
4. Enable custom admin fonts and choose a family. The default is **off**.

There is no Composer install step.

## What v1 styles

When enabled and the family is not “System default”, the plugin enqueues CSS only on **Posts → All Posts** (`edit-post` / `edit.php` with `post_type=post`). It targets `.wp-list-table` text (headers, row titles, cells, row actions). It does **not** load on:

- Dashboard
- Pages list
- The post editor (`post.php` / `post-new.php`)
- The front end
- The admin menu or admin bar

The option `bristlecone_admin_styles_settings` stores `{ enabled, font_family, scopes }`. `scopes` defaults to `['edit-post']` and is the expansion point for later `/wp-admin/` coverage. Do not treat that list as permanently one screen.

## Fonts

Allowlisted keys only (no free-text CSS):

| Key | Label | Source |
|-----|--------|--------|
| `default` | System default (no override) | WordPress core fonts |
| `monospace` | System monospace | OS stack |
| `inter` | Inter | Bundled WOFF2 |
| `source-sans-3` | Source Sans 3 | Bundled WOFF2 |
| `ibm-plex-sans` | IBM Plex Sans | Bundled WOFF2 |
| `ibm-plex-mono` | IBM Plex Mono | Bundled WOFF2 |

License notes for bundled faces: [`assets/fonts/NOTICE`](assets/fonts/NOTICE).

## Bristlecone menu and future Markdown

This plugin registers a top-level **Bristlecone** menu (slug `bristlecone`, capability `manage_options`, custom bold **B** icon) only when that parent is not already present. The submenu is **Admin Styles** (page slug `bristlecone-admin-styles`). The auto-duplicate “Bristlecone / Bristlecone” first submenu is removed when this plugin created the parent.

**Do not change [bits-markdown](https://github.com/Zyniker13/bits-markdown) in this repository.** When Markdown later moves off Settings → Bristlecone Markdown, it should attach like this:

1. Drop `add_options_page( …, 'bristlecone-markdown', … )`.
2. Use the same parent-guard: create slug `bristlecone` only if `$GLOBALS['admin_page_hooks']['bristlecone']` is empty (so either plugin can stand alone).
3. `add_submenu_page( 'bristlecone', 'Bristlecone Markdown', 'Markdown', 'manage_options', 'bristlecone-markdown', … )`.
4. Keep the page slug **`bristlecone-markdown`**. Submenu label is **Markdown**.
5. If Markdown created the parent, `remove_submenu_page( 'bristlecone', 'bristlecone' )` to avoid the duplicate first item.

See the comments on `Settings::register_menu()` in `includes/Settings.php`.

## Uninstall

Deleting the plugin via WordPress runs `uninstall.php`, which removes `bristlecone_admin_styles_settings`.

## License

GPL-2.0-or-later. See `LICENSE`. Bundled fonts keep their SIL OFL 1.1 terms.
