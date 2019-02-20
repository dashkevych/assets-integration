<?php
/**
 *
 *
 * @since 1.0.0
 * @package AssetsIntegration
 */

namespace AssetsIntegration;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die;

use AssetsIntegration\Base\Activate;
use AssetsIntegration\Base\Enqueue;
use AssetsIntegration\Base\SettingsApi;
use AssetsIntegration\Base\BootstrapController;
use AssetsIntegration\Pages\Admin;

final class Init {
	/**
	 * Store all the classes of the plugin.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function getServices() {
		return [
			Enqueue::class,
			Admin::class,
			BootstrapController::class
		];
	}

	/**
	 * Loop through the classes, instantiate them,
	 * and call the register method if it exists.
	 *
	 * @since 1.0.0
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function registerServices() {
		// Check if we meet the minimum PHP version.
		// The backup sanity check, in case the plugin is activated in a weird way.
		if ( version_compare( PHP_VERSION, Activate::getMinPhpVersion(), '<' ) ) {
			add_action( 'admin_notices', array( 'AssetsIntegration\Base\Activate', 'phpAdminNotice' ) );

			return;
		}

		// Plugin settings.
		$settings = new SettingsApi();
		// Register each service of the plugin.
		foreach ( self::getServices() as $class ) {
			$service = self::instantiate( $class, $settings );

			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	/**
	 * Instantiate the class.
	 *
	 * @since 1.0.0
	 * @param class $class
	 * @param class $settings WordPress Settings API.
	 * @return class new instance of the class
	 */
	private static function instantiate( $class, $settings ) {
		return new $class( $settings );
	}
}
