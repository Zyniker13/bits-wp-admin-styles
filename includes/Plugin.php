<?php

declare(strict_types=1);

namespace Bristlecone\AdminStyle;

/**
 * Plugin bootstrap.
 *
 * @package BristleconeAdminStyle
 */
final class Plugin {

	private static ?self $instance = null;

	public static function instance(): self {
		return self::$instance ??= new self();
	}

	public function init(): void {
		load_plugin_textdomain(
			'bristlecone-admin-style',
			false,
			dirname( plugin_basename( BRISTLECONE_ADMIN_STYLE_FILE ) ) . '/languages'
		);

		Settings::instance()->register();
		Assets::instance()->register();
	}

	public function activate(): void {
		Settings::instance()->ensure_defaults();
	}
}
