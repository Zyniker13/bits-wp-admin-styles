<?php

declare(strict_types=1);

namespace Bristlecone\AdminStyles;

/**
 * Plugin bootstrap.
 *
 * @package BristleconeAdminStyles
 */
final class Plugin {

	private static ?self $instance = null;

	public static function instance(): self {
		return self::$instance ??= new self();
	}

	public function init(): void {
		load_plugin_textdomain(
			'bristlecone-admin-styles',
			false,
			dirname( plugin_basename( BRISTLECONE_ADMIN_STYLES_FILE ) ) . '/languages'
		);

		Settings::instance()->register();
		Assets::instance()->register();
	}

	public function activate(): void {
		Settings::instance()->ensure_defaults();
	}
}
