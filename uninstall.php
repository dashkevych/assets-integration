<?php
/**
 * Trigger this file on Plugin uninstall
 *
 * @since 1.0.0
 * @package AssetsIntegration
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'assets_integration_settings' );