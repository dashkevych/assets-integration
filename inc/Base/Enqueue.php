<?php
/**
 * Load needed styles and scripts.
 *
 * @since 1.0.0
 * @package AssetsIntegration
 */

namespace AssetsIntegration\Base;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die;

class Enqueue {
	/**
     * Enable class functionalities.
     *
     * @since 1.0.0
     * @return void
     */
	public function register() {
		add_action( 'admin_enqueue_scripts', [ $this, 'adminEnqueue' ] );
	}

	/**
     * Enqueue admin styles and scripts.
     *
     * @since 1.0.0
     * @return void
     */
	public function adminEnqueue() {
		wp_enqueue_style( 'assets-integration-admin-style', ASSETS_INTEGRATION_PLUGIN_URL . 'assets/admin/css/style.css', array(), '1.0.0' );
		wp_enqueue_script( 'assets-integration-admin-script', ASSETS_INTEGRATION_PLUGIN_URL . 'assets/admin/js/app.min.js', '1.0.0', true );
	}
}
