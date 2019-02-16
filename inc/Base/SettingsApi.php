<?php
/**
 * Interface for WordPress Settings API.
 *
 * @since 1.0.0
 * @package AssetsIntegration
 */

namespace AssetsIntegration\Base;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die;

class SettingsApi {
    private $option_name = 'assets_integration_settings';
    public $admin_pages = array();
    public $admin_subpages = array();

    public $settings = array();
    public $sections = array();
    public $fields = array();

    /**
	 * Enable custom admin settings.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    public function register() {
        // Set a plugin option if needed.
        $this->setSettingsOption();

        if ( ! empty( $this->admin_pages ) ) {
            add_action( 'admin_menu', [ $this, 'addAdminMenu' ] );
        }

        if ( ! empty( $this->settings ) ) {
            add_action( 'admin_menu', [ $this, 'registerCustomSettings' ] );
        }
    }

    /**
	 * Adds settings option to WordPress if it does not exist.
	 *
	 * @since  1.0.0
	 * @return void
	 */
    private function setSettingsOption() {
        if ( false == get_option( $this->option_name ) ) {
            add_option( $this->option_name, $this->getDefaultSettings() );
        }
    }

    /**
	 * Returns an option name of the plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
     public function getOptionName() {
         return $this->option_name;
     }

    /**
	 * Retrieve default settings of the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
    public function getDefaultSettings( $section = NULL ) {
        // Bootstrap default settings.
        $bootstrap = [
            'local_version' => '',
            'local_assets' => [
                'css' => '',
                'js' => '',
            ],
            'is_cdn' => '',
            'cdn_assets' => [
                'css' => '',
                'js' => '',
            ],
        ];

        // Default settings.
        $defaults = [
            'assets' => [
                'bootstrap' => $bootstrap,
            ]
        ];

        // Return defaults for a specific section.
        if ( NULL !== $section && isset( $defaults[$section] ) ) {
            return $defaults[$section];
		}

        // Return all defaults.
        return $defaults;
    }

    /**
	 * Retrieve current theme options that were set in the Customizer.
	 *
	 * @since  1.0.0
	 * @param string $section
	 * @return array
	 */
    public function getSavedSettings( $section = NULL ) {
        $settings = get_option( $this->option_name, array() );

        if ( NULL !== $section && isset( $settings[$section] ) ) {
            return $settings[$section];
		}

        return $settings;
    }

    /**
	 * Retrieve all plugin settings with a fallback to defaults.
	 *
	 * @since  1.0.0
	 * @param string $section
	 * @return array
	 */
    public function getSettings( $section = NULL ) {
        return $this->parse_args_r(
            $this->getSavedSettings( $section ),
            $this->getDefaultSettings( $section )
        );
    }

    /**
	 * Like wp_parse_args but supports recursivity.
	 *
	 * @since 1.0.0
	 * @param array $args
	 * @param array $defaults
	 * @return array
	 */
	public function parse_args_r( $args, $defaults ) {
		$args = (array) $args;
		$defaults = (array) $defaults;
		$output = $defaults;

		foreach ( $args as $k => $v ) {
			if ( is_array( $v ) && isset( $output[ $k ] ) ) {
                $output[ $k ] = $this->parse_args_r( $v, $output[ $k ] );
			} else {
				$output[ $k ] = $v;
			}
		}

		return $output;
    }
    
    /**
	 * Adds a menu page in a dashbaord.
     *
	 * @since 1.0.0
     * @return object
	 */
    public function addPages( $pages ) {
        $this->admin_pages = $pages;

        return $this;
    }

    /**
	 * Adds subpages to the menu in a dashbaord.
     *
	 * @since 1.0.0
     * @return object
	 */
    public function addSubPages( $pages ) {
		$this->admin_subpages = array_merge( $this->admin_subpages, $pages );

		return $this;
	}

    /**
	 * Allow to add subpages to the primary menu item in dashbaord.
     *
	 * @since 1.0.0
     * @return object
	 */
    public function withSubPage( $title = null ) {
        if ( empty( $this->admin_pages ) ) {
            return $this;
        }

        $admin_page = $this->admin_pages[0];

        $subpages = [
            [
                'parent_slug' => $admin_page['menu_slug'],
                'page_title' => $admin_page['page_title'],
                'menu_title' => ( $title ) ? $title : $admin_page['menu_title'],
                'capability' => $admin_page['capability'],
                'menu_slug' => $admin_page['menu_slug'],
                'callback' => $admin_page['callback']
            ]
        ];

        $this->admin_subpages = $subpages;

        return $this;
    }

    /**
	 * Registers admin menus.
     *
	 * @since 1.0.0
     * @return void
	 */
    public function addAdminMenu() {
        foreach ( $this->admin_pages as $page ) {
            add_menu_page(
                $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']
            );
        }

        foreach ( $this->admin_subpages as $page ) {
            add_submenu_page(
                $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback']
            );
        }
    }

    /**
	 * Sets custom settings.
     *
	 * @since 1.0.0
     * @return object
	 */
    public function setSettings( $settings ) {
        $this->settings = $settings;

        return $this;
    }

    /**
	 * Sets a settings section.
     *
	 * @since 1.0.0
     * @return object
	 */
    public function setSections( $sections ) {
        $this->sections = $sections;

        return $this;
    }

    /**
	 * Sets a settings field.
     *
	 * @since 1.0.0
     * @return object
	 */
    public function setFields( $fields ) {
        $this->fields = $fields;

        return $this;
    }

    /**
	 * Registers custom settings.
     *
	 * @since 1.0.0
     * @return void
	 */
    public function registerCustomSettings() {
        // Register setting.
        foreach ( $this->settings as $setting ) {
            register_setting( $setting['option_group'], $setting['option_name'], ( isset( $setting['callback'] ) ? $setting['callback'] : '' ) );
        }

        // Add settings section.
        foreach ( $this->sections as $section ) {
            add_settings_section( $section['id'], $section['title'], ( isset( $section['callback'] ) ? $section['callback'] : '' ), $section['page'] );
        }

        // Add settings field.
        foreach ( $this->fields as $field ) {
            add_settings_field( $field['id'], $field['title'], ( isset( $setting['callback'] ) ? $field['callback'] : '' ), $field['page'], $field['section'], ( isset( $field['args'] ) ? $field['args'] : '' ) );
        }
    }
}
