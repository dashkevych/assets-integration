<?php
/**
 * @since 1.0.0
 * @package AssetsIntegration
 */

namespace AssetsIntegration\Base;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die;

class BootstrapController {
    /**
	 * Settings API.
	 *
	 * @since 1.0.0
	 * @var object
	 */
    private $settingsApi;

    /**
	 * Bootstrap settings.
	 *
	 * @since 1.0.0
	 * @var object
	 */
    private $settings;

    /**
	 * Set up a Bootstrap controller.
	 *
	 * @since 1.0.0
	 * @param object $settings
	 * @return void
	 */
    public function __construct( $settings ) {
        $this->settingsApi = $settings;
        $this->settings = $this->getBootstrapSettings();
    }

    /**
	 * Registered all functionaliies needed for this controller.
	 *
	 * @since 1.0.0
	 * @return void
	 */
    public function register() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueAssets' ] );
    }
    
    /**
	 * Enqueue assets.
	 *
	 * @since 1.0.0
	 * @return void
	 */
    public function enqueueAssets() {
        // Load assets via CDN.
        if ( $this->isCdnDelivery() ) {
            $this->loadAssetsViaCdn();

            return;
        }

        // Load assets locally.
        if ( $this->isLocalDelivery() ) {
            $this->loadAssetsLocally();

            return;
        }
    }

    /**
	 * Return saved Bootstrap settings.
	 *
	 * @since 1.0.0
	 * @return array
	 */
    private function getBootstrapSettings() {
        $assets_settings = $this->settingsApi->getSettings( 'assets' );
        
        return $assets_settings['bootstrap'];
    }

    /**
	 * Check if assets need to be loaded via CND or not.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
    private function isCdnDelivery() {
        return $this->settings['is_cdn'];
    }

    /**
	 * Check if local assets have been selected.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
    private function isLocalDelivery() {
        if ( ! $this->settings['local_version'] ) {
            return false;
        }

        if ( empty( $this->settings['local_assets'] ) ) {
            return false;
        }

        return true;
    }

    /**
	 * Load assets via CDN. 
	 *
	 * @since 1.0.0
	 * @return void
	 */
    private function loadAssetsViaCdn() {
        if ( empty( $this->settings['cdn_assets'] ) ) {
            return;
        }

        foreach ( $this->settings['cdn_assets'] as $type => $link ) {
            switch ( $type ) {
                case 'css':
                    if ( '' !== $link ) {
                        wp_enqueue_style( 'bootstrap-library-style', esc_url( $link ) );
                    }

                    break;
                case 'js':
                    if ( '' !== $link ) {
                        wp_enqueue_script( 'bootstrap-library-script', esc_url( $link ), array( 'jquery' ), '', 'true' );
                    }

                    break;
            }
        }
    }

    /**
	 * Load assets locally. 
	 *
	 * @since 1.0.0
	 * @return void
	 */
    private function loadAssetsLocally() {
        foreach ( $this->settings['local_assets'] as $type => $isEnabled ) {
            switch ( $type ) {
                case 'css':
                    // Load local CSS asset based on the selected version.
                    if ( '' !== $isEnabled ) {
                        wp_enqueue_style( 'bootstrap-library-style', $this->getLocalResourcesURL( 'css', $this->settings['local_version'] ) );
                    }

                    break;
                case 'js':
                    // Load local JS asset based on the selected version.
                    if ( '' !== $isEnabled ) {
                        wp_enqueue_script( 'bootstrap-library-script', $this->getLocalResourcesURL( 'js', $this->settings['local_version'] ), array( 'jquery' ), esc_attr( $this->settings['local_version'] ), true );
                    }

                    break;
            }
        }
    }

    /**
	 * Return local asset URL based on the selected type and version.
	 *
	 * @since 1.0.0
     * @param string $type  Type of selected asset: CSS or JS
     * @param string $version   Version of the selected asset
	 * @return string
	 */
    public function getLocalResourcesURL( $type, $version ) {
        $assetURL = ASSETS_INTEGRATION_PLUGIN_URL . 'resources/bootstrap/' . $version . '/' . $type . '/';

        if ( 'css' === $type ) {
            $assetFileName = 'bootstrap.min.css';
        } else {
            $assetFileName = 'bootstrap.bundle.min.js';
        }

        return esc_url( $assetURL . $assetFileName );
    }
}