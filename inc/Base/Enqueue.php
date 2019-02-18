<?php
/**
 * Load needed styles and scripts.
 * This class also loads translation files (might be changed later).
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
          // Load admin assets.
          add_action( 'admin_enqueue_scripts', [ $this, 'adminEnqueue' ] );
          // Load translation files.
          add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
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
     
     /**
	 * Load the plugin text domain.
	 *
	 * @since 1.0.0
      * @return void
	 */
	public function load_textdomain() {
          $lang_dir = dirname( plugin_basename( ASSETS_INTEGRATION_PLUGIN_FILE ) ) . '/languages/';
          load_plugin_textdomain( 'my-plugin', false, $lang_dir );
	}
}
