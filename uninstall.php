<?php
/**
 * Uninstall Bristlecone Admin Styles.
 *
 * @package BristleconeAdminStyles
 */

declare(strict_types=1);

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'bristlecone_admin_styles_settings' );
