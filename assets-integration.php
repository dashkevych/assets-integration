<?php
/**
 * Plugin Name: Assets Integration
 * Description: The easiest way to load Bootstrap assets (JS & CSS) in WordPress.
 * Author: Taras Dashkevych
 * Author URI: https://themesharbor.com
 * Version: 1.0.0
 * License: GPL3
 * Text Domain: assets-integration
 * Domain Path: languages
 *
 * @package AssetsIntegration
 * @version 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || die;

// Get autoload functionality.
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

// Define the plugin constants.
define( 'ASSETS_INTEGRATION_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'ASSETS_INTEGRATION_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ASSETS_INTEGRATION_PLUGIN_FILE', __FILE__ );

/**
 * The code that runs during plugin activation.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
function assets_integration_activate() {
	AssetsIntegration\Base\Activate::init();
}
register_activation_hook( __FILE__, 'assets_integration_activate' );

if ( class_exists( 'AssetsIntegration\\Init' ) ) {
	AssetsIntegration\Init::registerServices();
}
