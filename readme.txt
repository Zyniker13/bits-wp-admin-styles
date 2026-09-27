=== Bristlecone Admin Styles ===
Contributors: bristleconeit
Tags: admin, fonts, typography, list-table, bristlecone
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 8.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Curated, self-hosted fonts for WordPress admin list tables. v1 styles Posts → All Posts.

== Description ==

Bristlecone Admin Styles lets site admins pick an allowlisted font family for dense WordPress admin list tables. Custom fonts are **off by default**.

The plugin is developed by [Bristlecone IT Services](https://bristleconeit.com). Plugin homepage: [bristleconeit.com/bristlecone-admin-styles](https://bristleconeit.com/bristlecone-admin-styles/). Source and issues are on [GitHub](https://github.com/Zyniker13/bits-wp-admin-styles).

= v1 scope =

* Applies only to **Posts → All Posts**
* Targets the posts `.wp-list-table` (headers, titles, cells, row actions)
* Does not change the admin menu, admin bar, Dashboard, Pages, the editor, or the front end
* Settings store a `scopes` list so more `/wp-admin/` screens can be added later

= Fonts =

Allowlisted presets only (no free-text CSS, no third-party font CDN on admin load):

* System default (no override)
* System monospace
* Inter, Source Sans 3, IBM Plex Sans, and IBM Plex Mono (bundled WOFF2, SIL OFL 1.1)

See `assets/fonts/NOTICE` in the plugin for font license notes.

= Settings =

Open **Bristlecone → Admin Styles** (`manage_options`). Enable the checkbox, choose a family, and save.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate **Bristlecone Admin Styles**.
3. Open Bristlecone → Admin Styles.
4. Enable custom admin fonts and select a family. The default is off.

== Frequently Asked Questions ==

= Why is nothing different after I activate the plugin? =

Custom fonts default to off. Enable them under Bristlecone → Admin Styles and choose a family other than System default. v1 only changes Posts → All Posts.

= Does this load Google Fonts from the internet? =

No. Bundled faces are served from the plugin. System stacks use fonts already on the computer.

= Will this change my theme or the public site? =

No. Styles enqueue only in `wp-admin` on the posts list screen.

= How does this work with Bristlecone Markdown? =

This plugin does not change bits-markdown. It registers a top-level Bristlecone menu only if that parent is missing. A later Markdown ticket can add a **Markdown** submenu with page slug `bristlecone-markdown`.

== Changelog ==

= 1.0.0 =
* Initial release.
* Bristlecone → Admin Styles settings (off by default).
* Allowlisted system stacks plus bundled Inter, Source Sans 3, IBM Plex Sans, and IBM Plex Mono.
* Posts → All Posts list-table font override when enabled.

== Upgrade Notice ==

= 1.0.0 =
Initial public release of Bristlecone Admin Styles.
