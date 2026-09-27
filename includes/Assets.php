<?php

declare(strict_types=1);

namespace Bristlecone\AdminStyle;

/**
 * Admin CSS enqueue. v1 is gated to the posts list table.
 *
 * @package BristleconeAdminStyle
 */
final class Assets {

	private static ?self $instance = null;

	public static function instance(): self {
		return self::$instance ??= new self();
	}

	public function register(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function enqueue( string $hook_suffix ): void {
		if ( ! is_admin() || is_network_admin() ) {
			return;
		}

		$settings = Settings::instance();
		if ( ! $settings->is_enabled() ) {
			return;
		}

		$family = $settings->font_family();
		if ( ! Fonts::applies_override( $family ) ) {
			return;
		}

		if ( ! $this->should_style_current_screen( $hook_suffix, $settings->scopes() ) ) {
			return;
		}

		$stack = Fonts::stack( $family );
		if ( '' === $stack ) {
			return;
		}

		$deps = array();
		if ( Fonts::is_webfont( $family ) ) {
			wp_enqueue_style(
				Fonts::HANDLE_FONTS,
				BRISTLECONE_ADMIN_STYLE_URL . 'assets/css/fonts.css',
				array(),
				BRISTLECONE_ADMIN_STYLE_VERSION
			);
			$deps[] = Fonts::HANDLE_FONTS;
		}

		wp_enqueue_style(
			Fonts::HANDLE_LIST,
			BRISTLECONE_ADMIN_STYLE_URL . 'assets/css/admin-list-fonts.css',
			$deps,
			BRISTLECONE_ADMIN_STYLE_VERSION
		);

		// Stack comes from the PHP allowlist, never from user-supplied CSS.
		$css = Fonts::list_table_selector() . '{font-family:' . $stack . ';}';
		wp_add_inline_style( Fonts::HANDLE_LIST, $css );
	}

	/**
	 * @param list<string> $scopes
	 */
	private function should_style_current_screen( string $hook_suffix, array $scopes ): bool {
		foreach ( $scopes as $scope ) {
			if ( $this->scope_matches( $scope, $hook_suffix ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Map a stored scope key to a screen check. Add arms here as known_scopes() grows.
	 */
	private function scope_matches( string $scope, string $hook_suffix ): bool {
		return match ( $scope ) {
			Settings::SCOPE_EDIT_POST => $this->is_posts_list_screen( $hook_suffix ),
			default                   => false,
		};
	}

	/**
	 * Posts → All Posts only. Not Pages, CPTs, the editor, Dashboard, or the front end.
	 */
	private function is_posts_list_screen( string $hook_suffix ): bool {
		if ( 'edit.php' !== $hook_suffix ) {
			return false;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();
		if ( ! $screen ) {
			return false;
		}

		return 'edit-post' === $screen->id
			|| ( 'edit' === $screen->base && 'post' === $screen->post_type );
	}
}
