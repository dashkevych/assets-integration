<?php
/**
 * Admin page functionalities.
 *
 * @since 1.0.0
 * @package AssetsIntegration
 */

namespace AssetsIntegration\Pages;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die;

use AssetsIntegration\Pages\Callbacks\AdminCallbacks;
use AssetsIntegration\Base\SettingsSanitizer;

class Admin {
    /**
	 * Settings API.
	 *
	 * @since 1.0.0
	 * @var object
	 */
    private $settings;

    /**
	 * Callbacks for the settings option.
	 *
	 * @since 1.0.0
	 * @var object
	 */
    private $callbacks;

    /**
	 * Settings sanitizer.
	 *
	 * @since 1.0.0
	 * @var object
	 */
    private $sanitize;

    /**
	 * Array of admin pages.
	 *
	 * @since 1.0.0
	 * @var array
	 */
    public $pages = [];

    /**
	 * Array of admin sub-pages.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $subpages = [];

    /**
	 * The slug name to refer to the menu page.
	 *
	 * @since 1.0.0
	 * @var string
	 */
    public $parent_menu_slug = 'assets_integration';
    
    /**
	 * Registered settings structure.
	 *
	 * @since 1.0.0
	 * @var array
	 */
    private $settings_structure = [];

    /**
	 * Array of registered settings sections.
	 *
	 * @since 1.0.0
	 * @var array
	 */
    private $setting_sections_args = [];

    /**
	 * Array of registered settings fields.
	 *
	 * @since 1.0.0
	 * @var array
	 */
    private $setting_fields_args = [];

    /**
	 * Array of settings tabs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
    public $page_tabs = [];

    /**
	 * Set up the admin page.
	 *
	 * @since 1.0.0
	 * @param object $settings 
	 * @return void
	 */
    public function __construct( $settings ) {
        $this->settings = $settings;
    }

    public function register() {
        // Set up the settings structure.
        $this->setSettingsStructure();
        
        // Set up admin callbacks.
        $this->callbacks = new AdminCallbacks( $this->settings );

        // Set up settings sanitizer.
        $this->sanitize = new SettingsSanitizer( $this->settings, $this->settings_structure );

        // Set up the settings options based on the settings structure.
        $this->setSettingsOptions();
        
        // Set up admin pages.
        $this->setPages();

        // Set up the plugin settings.
        $this->setSettings();

        // Set up the plugin settings sections.
        $this->setSections();

        // Set up the plugin settings fileds.
        $this->setFields();

        // Add the plugin page to the Dashbaord menu.
        $this->settings->addPages( $this->pages )->withSubPage()->addSubPages( $this->subpages )->register();
    
        // Set up navigational tabs in the admin page.
        $this->setSettingsTabs();

        // Display assets tab on the admin page.
        add_action( 'assets_integration_admin_header', [ $this, 'displaySettingsTabs' ] );

        // Set up admin notices.
        add_action( 'admin_notices', [ $this, 'adminNotices' ] );
    }

    /**
     * Set up admin notices.
     *
     * @since 1.0.0
     * @return void
     */
    public function adminNotices() {
        settings_errors( 'assets-integration-notices' );
    }

    /**
     * Sets pages in the admin menu.
     *
     * @since 1.0.0
     * @return void
     */
    public function setPages() {
        $this->pages = [
            [
                'page_title' => esc_html__( 'Assets Integration', 'assets-integration' ),
                'menu_title' => esc_html__( 'Assets', 'assets-integration' ),
                'capability' => 'manage_options',
                'menu_slug' => $this->parent_menu_slug,
                'callback' => [ $this, 'displayAdminPage' ],
                'icon_url' => 'dashicons-archive',
                'position' => 110,
            ]
        ];
    }

    /**
     * Sets up settings for the plugin.
     *
     * @since 1.0.0
     * @return array
     */
    private function setSettingsOptions() {
        // If there is no settings structure, stop the process of creating settings.
        if ( empty( $this->settings_structure ) ) {
            return;
        }

        foreach ( $this->settings_structure as $section => $tabs ) {
            foreach ( $tabs as $tab => $settings ) {

                $settings_page_id = 'assets_integration_' . $section . '_' . $tab;
                $section_id = 'assets_integration_' . $section . '_' . $tab;

                // Register a settings section.
                $this->setting_sections_args[] = array(
                    'id' => $section_id,
                    'title' => __return_null(),
                    'callback' => __return_false(),
                    'page' => $settings_page_id,
                );

                foreach ( $settings as $option ) {
                    $fields_args = wp_parse_args( $option, array(
                        'id' => '',
                        'tab' => $tab,
                        'section' => $section,
                        'title' => '',
                        'description' => '',
                        'label' => '',
                        'type' => '',
                        'subtype' => '',
                        'options' => [],
                    ) );
    
                    $callback = $fields_args['type'] . 'Output';
                    
                    // Register a settings field to a settings page and section.
                    $this->setting_fields_args[] = [
                        'id' => $fields_args['id'],
                        'title' => $fields_args['title'],
                        'callback' => method_exists( $this->callbacks, $callback ) ? [ $this->callbacks, $callback ] : [ $this->callbacks, 'missingCallback' ],
                        'page' => $settings_page_id,
                        'section' => $section_id,
                        'args' => $fields_args
                    ];
                }
            }
        }
    }

    /**
     * Create a stucture of the plugin settings.
     *
     * @since 1.0.0
     * @return array
     */
    public function getRegisteredSettings() {
        return $this->settings_structure;
    }

    /**
     * Sets a settings structure of the plugin.
     *
     * @since 1.0.0
     * @return void
     */
    private function setSettingsStructure() {
        // Create the array of the plugin settings to build a settings form.
        $this->settings_structure = [
            'assets' => [
                'bootstrap' => [
                    'is_cdn' => [
                        'id' => 'is_cdn',
                        'title' => esc_html__( 'Asset delivery', 'assets-integration' ),
                        'description' => esc_html__( 'Select the way you want to load this asset.', 'assets-integration' ),
                        'type' => 'radio',
                        'options' => [
                            '' => 'Load assets locally',
                            '1' => 'Load assets via CDN'
                        ],
                    ],
                    
                    'local_version' => [
                        'id' => 'local_version',
                        'title' => esc_html__( 'Version', 'assets-integration' ),
                        'description' => esc_html__( 'Select a verion of the asset you want to use on your site.', 'assets-integration' ),
                        'type' => 'select',
                        'options' => [
                            '' => esc_html__( 'None', 'assets-integration' ),
                            '3.4.x' => '3.4.x',
                            '4.3.x' => '4.3.x',
                        ],
                    ],

                    'local_assets' => [
                        'id' => 'local_assets',
                        'title' => esc_html__( 'Asset Type', 'assets-integration' ),
                        'description' => esc_html__( 'Select asset type you want to load on your site.', 'assets-integration' ),
                        'type' => 'checkbox',
                        'subtype' => 'bool',
                        'options' => [
                            'css' => esc_html__( 'Load a CSS', 'assets-integration' ),
                            'js' => esc_html__( 'Load a JavaScript', 'assets-integration' ),
                        ],
                    ],

                    'cdn_assets' => [
                        'id' => 'cdn_assets',
                        'title' => esc_html__( 'Asset Type', 'assets-integration' ),
                        'description' => esc_html__( 'Enter URLs of CDN assets', 'assets-integration' ),
                        'type' => 'input',
                        'subtype' => 'url',
                        'options' => [
                            'css' => esc_html__( 'CSS URL', 'assets-integration' ),
                            'js' => esc_html__( 'JavaScript URL', 'assets-integration' ),
                        ],
                    ],

                    'priority' => [
                        'id' => 'priority',
                        'title' => esc_html__( 'Priority', 'assets-integration' ),
                        'description' => esc_html__( 'Optional: This option is used to specify the order in which these assets are loaded on your site. Lower numbers correspond with earlier execution. Default is 10.', 'assets-integration' ),
                        'type' => 'input',
                        'subtype' => 'number',
                    ],
                ]
            ]
        ];
    }

    /**
     * Sets up settings for the plugin.
     *
     * @since 1.0.0
     * @return void
     */
    public function setSettings() {
        $args = [
            [
                'option_group' => 'assets_integration_settings',
                'option_name' => $this->settings->getOptionName(),
                'callback' => [ $this->sanitize, 'settingsForm' ],
            ],
        ];

        $this->settings->setSettings( $args );
    }

    /**
     * Sets up settings sections in the Dashabrod.
     *
     * @since 1.0.0
     * @return void
     */
    public function setSections() {
        $this->settings->setSections( $this->setting_sections_args );
    }

    /**
     * Set up settings fields in the Dashabrod.
     *
     * @since 1.0.0
     * @return void
     */
    public function setFields() {
        $this->settings->setFields( $this->setting_fields_args );
    }

    /**
     * Set up the settings tabs.
     *
     * @since 1.0.0
     * @return void
     */
    public function setSettingsTabs() {
        $this->page_tabs = [
            'bootstrap' => esc_html__( 'Bootstrap', 'assets-integration' ),
        ];
    }

    /**
     * Display settings tabs on the admin page.
     *
     * @since 1.0.0
     * @return void
     */
    public function displaySettingsTabs() {
        return require_once( ASSETS_INTEGRATION_PLUGIN_PATH . 'views/parts/assets-tabs.php' );
    }

    /**
     * Display settings form on the admin page.
     *
     * @since 1.0.0
     * @return void
     */
    public function displayAdminPage() {
        return require_once( ASSETS_INTEGRATION_PLUGIN_PATH . '/views/admin.php' );
    }  
}
