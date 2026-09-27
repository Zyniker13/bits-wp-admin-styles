<?php
/**
 * Plugin Name: Bristlecone Admin Styles
 * Plugin URI: https://bristleconeit.com/bristlecone-admin-styles/
 * Description: Curated, self-hosted fonts for WordPress admin list tables. v1 styles Posts → All Posts; the settings model is ready to expand across /wp-admin/.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.4
 * Author: Bristlecone IT Services
 * Author URI: https://bristleconeit.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bristlecone-admin-style
 * Domain Path: /languages
 *
 * @package BristleconeAdminStyle
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BRISTLECONE_ADMIN_STYLE_VERSION', '1.0.0' );
define( 'BRISTLECONE_ADMIN_STYLE_FILE', __FILE__ );
define( 'BRISTLECONE_ADMIN_STYLE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BRISTLECONE_ADMIN_STYLE_URL', plugin_dir_url( __FILE__ ) );

require_once BRISTLECONE_ADMIN_STYLE_DIR . 'includes/Fonts.php';
require_once BRISTLECONE_ADMIN_STYLE_DIR . 'includes/Settings.php';
require_once BRISTLECONE_ADMIN_STYLE_DIR . 'includes/Assets.php';
require_once BRISTLECONE_ADMIN_STYLE_DIR . 'includes/Plugin.php';

add_action(
	'plugins_loaded',
	static function (): void {
		\Bristlecone\AdminStyle\Plugin::instance()->init();
	}
);

register_activation_hook(
	__FILE__,
	static function (): void {
		\Bristlecone\AdminStyle\Plugin::instance()->activate();
	}
);
