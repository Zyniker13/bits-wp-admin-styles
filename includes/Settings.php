<?php

declare(strict_types=1);

namespace Bristlecone\AdminStyle;

/**
 * Settings API, Bristlecone menu, and option sanitization.
 *
 * @package BristleconeAdminStyle
 */
final class Settings {

	public const OPTION      = 'bristlecone_admin_style_settings';
	public const GROUP       = 'bristlecone_admin_style';
	public const PARENT_SLUG = 'bristlecone';
	public const PAGE_SLUG   = 'bristlecone-admin-style';

	/**
	 * v1 application target: Posts → All Posts (screen id edit-post).
	 * Additional keys can be added to known_scopes() as coverage grows.
	 */
	public const SCOPE_EDIT_POST = 'edit-post';

	private static ?self $instance = null;

	public static function instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * @return array{enabled: bool, font_family: string, scopes: list<string>}
	 */
	public function defaults(): array {
		return array(
			'enabled'     => false,
			'font_family' => Fonts::KEY_DEFAULT,
			'scopes'      => self::default_scopes(),
		);
	}

	/**
	 * @return list<string>
	 */
	public static function default_scopes(): array {
		return array( self::SCOPE_EDIT_POST );
	}

	/**
	 * Screen scopes this plugin may style.
	 *
	 * v1 only recognizes edit-post. Add keys here (and a matcher in Assets)
	 * when expanding toward the rest of /wp-admin/. Do not treat this list
	 * as permanently a single item.
	 *
	 * @return list<string>
	 */
	public static function known_scopes(): array {
		return array(
			self::SCOPE_EDIT_POST,
			// Future (not applied until Assets maps them):
			// 'edit-page', 'dashboard', 'all-admin', …
		);
	}

	/**
	 * @return array{enabled: bool, font_family: string, scopes: list<string>}
	 */
	public function all(): array {
		$stored = get_option( self::OPTION, false );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$merged = wp_parse_args( $stored, $this->defaults() );

		return array(
			'enabled'     => ! empty( $merged['enabled'] ),
			'font_family' => $this->sanitize_font_family( $merged['font_family'] ?? Fonts::KEY_DEFAULT ),
			'scopes'      => $this->sanitize_scopes( $merged['scopes'] ?? self::default_scopes() ),
		);
	}

	public function ensure_defaults(): void {
		if ( false !== get_option( self::OPTION, false ) ) {
			return;
		}

		add_option( self::OPTION, $this->defaults() );
	}

	public function is_enabled(): bool {
		return $this->all()['enabled'];
	}

	public function font_family(): string {
		return $this->all()['font_family'];
	}

	/**
	 * @return list<string>
	 */
	public function scopes(): array {
		return $this->all()['scopes'];
	}

	public function has_scope( string $scope ): bool {
		return in_array( $scope, $this->scopes(), true );
	}

	public function register(): void {
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		// Priority 9: create the shared Bristlecone parent early so a future
		// Markdown plugin can attach at the default priority 10.
		add_action( 'admin_menu', array( $this, 'register_menu' ), 9 );
	}

	public function register_setting(): void {
		register_setting(
			self::GROUP,
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => $this->defaults(),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * Register the shared Bristlecone menu and this plugin’s Admin Styles page.
	 *
	 * Parent-guard: create slug `bristlecone` only when missing so either this
	 * plugin or a future Bristlecone Markdown plugin can stand alone.
	 *
	 * How Markdown should attach later (do not change bits-markdown in this
	 * ticket — document only):
	 *  1. Stop using add_options_page( …, 'bristlecone-markdown', … ).
	 *  2. Use the same parent guard: if empty( $GLOBALS['admin_page_hooks']['bristlecone'] ),
	 *     add_menu_page( …, 'bristlecone', … ) with the bold “B” icon (or reuse
	 *     whatever parent already exists).
	 *  3. add_submenu_page( 'bristlecone', 'Bristlecone Markdown', 'Markdown',
	 *     'manage_options', 'bristlecone-markdown', … ).
	 *     Keep page slug `bristlecone-markdown`. Submenu label is **Markdown**.
	 *  4. If that plugin created the parent, remove the auto-duplicate first
	 *     submenu: remove_submenu_page( 'bristlecone', 'bristlecone' ).
	 *  5. Capability stays manage_options so the menu appears for the same users.
	 */
	public function register_menu(): void {
		$parent_slug    = self::PARENT_SLUG;
		$created_parent = false;

		if ( empty( $GLOBALS['admin_page_hooks'][ $parent_slug ] ) ) {
			add_menu_page(
				__( 'Bristlecone', 'bristlecone-admin-style' ),
				__( 'Bristlecone', 'bristlecone-admin-style' ),
				'manage_options',
				$parent_slug,
				// Page callbacks run after admin chrome is printed, so a
				// redirect here would be too late. Render Admin Styles so
				// ?page=bristlecone is usable if this plugin created the parent.
				array( $this, 'render_page' ),
				$this->menu_icon(),
				58
			);
			$created_parent = true;
		}

		add_submenu_page(
			$parent_slug,
			__( 'Bristlecone Admin Styles', 'bristlecone-admin-style' ),
			__( 'Admin Styles', 'bristlecone-admin-style' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);

		// WP copies the parent slug as the first submenu item. Remove that
		// duplicate “Bristlecone / Bristlecone” row when we created the parent.
		if ( $created_parent ) {
			remove_submenu_page( $parent_slug, $parent_slug );
		}
	}

	/**
	 * Bold letter “B” as a custom SVG menu icon (not a Dashicon).
	 * WordPress recolors fill="black" via admin menu CSS filters.
	 */
	private function menu_icon(): string {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true"><path fill="black" fill-rule="evenodd" d="M3.75 2.25h7.4c2.72 0 4.7 1.52 4.7 3.92 0 1.42-.74 2.52-1.96 3.12 1.5.62 2.46 1.9 2.46 3.62 0 2.68-2.22 4.34-5.42 4.34H3.75V2.25Zm3.2 2.15v3.15h3.7c1.22 0 1.98-.64 1.98-1.6 0-.94-.76-1.55-1.98-1.55H6.95Zm0 5.3v3.9h4.15c1.42 0 2.28-.76 2.28-1.92 0-1.18-.86-1.98-2.28-1.98H6.95Z"/></svg>';

		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}

	/**
	 * @param mixed $value
	 * @return array{enabled: bool, font_family: string, scopes: list<string>}
	 */
	public function sanitize( $value ): array {
		$defaults = $this->defaults();
		if ( ! is_array( $value ) ) {
			return $defaults;
		}

		$scopes = array_key_exists( 'scopes', $value )
			? $this->sanitize_scopes( $value['scopes'] )
			: $this->preserved_scopes();

		return array(
			'enabled'     => ! empty( $value['enabled'] ),
			'font_family' => $this->sanitize_font_family( $value['font_family'] ?? $defaults['font_family'] ),
			'scopes'      => $scopes,
		);
	}

	/**
	 * @return list<string>
	 */
	private function preserved_scopes(): array {
		$stored = get_option( self::OPTION, false );
		if ( is_array( $stored ) && array_key_exists( 'scopes', $stored ) ) {
			return $this->sanitize_scopes( $stored['scopes'] );
		}

		return self::default_scopes();
	}

	private function sanitize_font_family( mixed $value ): string {
		$key = is_string( $value ) ? $value : Fonts::KEY_DEFAULT;
		return Fonts::is_known( $key ) ? $key : Fonts::KEY_DEFAULT;
	}

	/**
	 * @param mixed $value
	 * @return list<string>
	 */
	private function sanitize_scopes( mixed $value ): array {
		if ( ! is_array( $value ) ) {
			return self::default_scopes();
		}

		$allowed = array();
		foreach ( $value as $scope ) {
			if ( ! is_string( $scope ) ) {
				continue;
			}
			if ( in_array( $scope, self::known_scopes(), true ) && ! in_array( $scope, $allowed, true ) ) {
				$allowed[] = $scope;
			}
		}

		return array() === $allowed ? self::default_scopes() : $allowed;
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = $this->all();
		$choices  = Fonts::choices();
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Bristlecone Admin Styles', 'bristlecone-admin-style' ); ?></h1>
			<p><?php echo esc_html__( 'Curated fonts for dense admin list tables. Custom fonts are off by default. Monospace and the bundled faces make title, slug, and date columns easier to scan on All Posts. Other admin screens keep WordPress core fonts until a later release.', 'bristlecone-admin-style' ); ?></p>

			<form action="options.php" method="post">
				<?php settings_fields( self::GROUP ); ?>
				<?php foreach ( $settings['scopes'] as $scope ) : ?>
					<input type="hidden" name="<?php echo esc_attr( self::OPTION ); ?>[scopes][]" value="<?php echo esc_attr( $scope ); ?>" />
				<?php endforeach; ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php echo esc_html__( 'Enable custom admin fonts', 'bristlecone-admin-style' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::OPTION ); ?>[enabled]" value="1" <?php checked( $settings['enabled'] ); ?> />
								<?php echo esc_html__( 'Apply the selected font family on enabled admin screens', 'bristlecone-admin-style' ); ?>
							</label>
							<p class="description"><?php echo esc_html__( 'When this is off, no admin font CSS is loaded.', 'bristlecone-admin-style' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="bristlecone-admin-style-font-family"><?php echo esc_html__( 'Font family', 'bristlecone-admin-style' ); ?></label></th>
						<td>
							<select id="bristlecone-admin-style-font-family" name="<?php echo esc_attr( self::OPTION ); ?>[font_family]">
								<?php foreach ( $choices as $key => $label ) : ?>
									<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['font_family'], $key ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
							<p class="description"><?php echo esc_html__( 'Allowlisted presets only. Bundled webfonts are served from this plugin (no third-party CDN). System default leaves WordPress core fonts in place even if custom fonts are enabled.', 'bristlecone-admin-style' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Apply to', 'bristlecone-admin-style' ); ?></th>
						<td>
							<p><strong><?php echo esc_html__( 'Posts → All Posts', 'bristlecone-admin-style' ); ?></strong></p>
							<p class="description"><?php echo esc_html__( 'v1 applies only to the posts list table. The saved settings include a scopes list (currently edit-post) so more /wp-admin/ screens can be added later without changing the option shape.', 'bristlecone-admin-style' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
