<?php

declare(strict_types=1);

namespace Bristlecone\AdminStyles;

/**
 * Allowlisted font families. Keys are stored; CSS stacks are mapped in PHP.
 *
 * Never accept free-text CSS from the user.
 *
 * @package BristleconeAdminStyles
 */
final class Fonts {

	public const KEY_DEFAULT        = 'default';
	public const KEY_MONOSPACE      = 'monospace';
	public const KEY_INTER          = 'inter';
	public const KEY_SOURCE_SANS_3  = 'source-sans-3';
	public const KEY_IBM_PLEX_SANS  = 'ibm-plex-sans';
	public const KEY_IBM_PLEX_MONO  = 'ibm-plex-mono';

	public const HANDLE_LIST  = 'bristlecone-admin-styles-list';
	public const HANDLE_FONTS = 'bristlecone-admin-styles-fonts';

	/**
	 * System UI stack used as a sans fallback for bundled webfonts.
	 */
	private const SYSTEM_SANS = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif';

	/**
	 * System monospace stack (also the fallback for IBM Plex Mono).
	 */
	private const SYSTEM_MONO = 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace';

	/**
	 * @return list<string>
	 */
	public static function keys(): array {
		return array(
			self::KEY_DEFAULT,
			self::KEY_MONOSPACE,
			self::KEY_INTER,
			self::KEY_SOURCE_SANS_3,
			self::KEY_IBM_PLEX_SANS,
			self::KEY_IBM_PLEX_MONO,
		);
	}

	public static function is_known( string $key ): bool {
		return in_array( $key, self::keys(), true );
	}

	/**
	 * True when the key should emit a font-family override.
	 */
	public static function applies_override( string $key ): bool {
		return self::is_known( $key ) && self::KEY_DEFAULT !== $key;
	}

	public static function is_webfont( string $key ): bool {
		return in_array(
			$key,
			array(
				self::KEY_INTER,
				self::KEY_SOURCE_SANS_3,
				self::KEY_IBM_PLEX_SANS,
				self::KEY_IBM_PLEX_MONO,
			),
			true
		);
	}

	/**
	 * @return array<string, string> key => translated label
	 */
	public static function choices(): array {
		return array(
			self::KEY_DEFAULT       => __( 'System default (no override)', 'bristlecone-admin-styles' ),
			self::KEY_MONOSPACE     => __( 'System monospace', 'bristlecone-admin-styles' ),
			self::KEY_INTER         => __( 'Inter (bundled)', 'bristlecone-admin-styles' ),
			self::KEY_SOURCE_SANS_3 => __( 'Source Sans 3 (bundled)', 'bristlecone-admin-styles' ),
			self::KEY_IBM_PLEX_SANS => __( 'IBM Plex Sans (bundled)', 'bristlecone-admin-styles' ),
			self::KEY_IBM_PLEX_MONO => __( 'IBM Plex Mono (bundled)', 'bristlecone-admin-styles' ),
		);
	}

	/**
	 * Hard-coded stack for an allowlisted key. Empty for system default.
	 */
	public static function stack( string $key ): string {
		return match ( $key ) {
			self::KEY_MONOSPACE     => self::SYSTEM_MONO,
			self::KEY_INTER         => '"Inter", ' . self::SYSTEM_SANS,
			self::KEY_SOURCE_SANS_3 => '"Source Sans 3", ' . self::SYSTEM_SANS,
			self::KEY_IBM_PLEX_SANS => '"IBM Plex Sans", ' . self::SYSTEM_SANS,
			self::KEY_IBM_PLEX_MONO => '"IBM Plex Mono", ' . self::SYSTEM_MONO,
			default                 => '',
		};
	}

	/**
	 * Selectors that receive the chosen family. Scoped to the posts list table.
	 */
	public static function list_table_selector(): string {
		return implode(
			',',
			array(
				'body.wp-admin.post-type-post .wp-list-table',
				'body.wp-admin.post-type-post .wp-list-table th',
				'body.wp-admin.post-type-post .wp-list-table td',
				'body.wp-admin.post-type-post .wp-list-table .row-title',
				'body.wp-admin.post-type-post .wp-list-table .column-title strong',
				'body.wp-admin.post-type-post .wp-list-table .row-actions',
				'body.wp-admin.post-type-post .wp-list-table .row-actions a',
			)
		);
	}
}
