<?php
/**
 * Fired during plugin activation.
 *
 * @since 1.0.0
 * @package AssetsIntegration
 */

namespace AssetsIntegration\Base;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die;

class Activate {
    /**
     * Minimum required PHP version.
     */
    private static $php_version = '7.0';

    /**
	 * Actually registers our CPT with the merged arguments.
     *
	 * @since 1.0.0
     * @return void
	 */
    public static function init() {
        // Check if we meet the minimum PHP version.
        if ( version_compare( PHP_VERSION, self::getMinPhpVersion(), '<' ) ) {
            deactivate_plugins( ASSETS_INTEGRATION_PLUGIN_FILE );
            wp_die( self::getMinPhpMessage() );
        }
    }

    /**
	 * Returns a message noting the minimum version of PHP required.
	 *
	 * @since 1.0.0
	 * @return string
	 */
    private static function getMinPhpMessage() {
        return sprintf(
            __( 'Assets Integration plugin requires PHP version %1$s. You are running version %2$s. Please upgrade and try again.', 'assets-integration' ),
            self::$php_version,
            PHP_VERSION
        );
    }

    /**
     * Outputs the admin notice that the user needs to upgrade their PHP version. It also
     * auto-deactivates the plugin.
     *
     * @since 1.0.0
     * @return void
     */
    public static function phpAdminNotice() {
        printf(
            '<div class="notice notice-error is-dismissible"><p><strong>%s</strong></p></div>',
            esc_html( self::getMinPhpMessage() )
        );

        // Make sure the plugin is deactivated.
		deactivate_plugins( ASSETSINTEGRATION_PLUGIN_FILE );
    }

    /**
     * Returns a minimum required PHP version.
     *
     * @since  1.0.0
     * @return string
     */
    public static function getMinPhpVersion() {
        return self::$php_version;
    }
}
