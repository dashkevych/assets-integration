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

    public function register() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueueAssets' ] );
        add_action( 'assets_integration_admin_assets_body', [ $this, 'tester' ] );
    }
    
    public function enqueueAssets() {
        if ( $this->isCdnDelivery() ) {
            $this->loadAssetsViaCdn();

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
        if ( empty( $this->settings['local_assets'] ) ) {
            return false;
        }

        return isset( $this->settings['local_assets']['js'] );
    }

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

    private function loadAssetsLocally() {
        foreach ( $this->settings['local_assets'] as $type => $isEnabled ) {
            switch ( $type ) {
                case 'css':
                    if ( '' !== $isEnabled ) {
                        echo 'load css';
                    }

                    break;
                case 'js':
                    if ( '' !== $isEnabled ) {
                        echo 'load js';
                    }

                    break;
            }
        }
    }

    public function tester() {
        //var_dump( $this->isLocalDelivery() );
        //echo '<br><pre>' . print_r( $this->loadAssetsLocally(), true ) . '</pre>';      
    }
}