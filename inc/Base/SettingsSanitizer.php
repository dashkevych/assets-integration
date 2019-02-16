<?php
/**
 * Sanitize plugin settings.
 *
 * @since 1.0.0
 * @package AssetsIntegration
 */

namespace AssetsIntegration\Base;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die;

class SettingsSanitizer {
	/**
	 * Settings API.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private $settings;

	 /**
	 * Registered settings structure.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $settings_structure = [];
	
	/**
	 * Set up a sanitizer.
	 *
	 * @since 1.0.0
	 * @param object $settings
	 * @param array $settings_structure
	 * @return void
	 */
    public function __construct( $settings, $settings_structure ) {
		$this->settings = $settings;
        $this->settings_structure = $settings_structure;
    }

	public function input( $input, $type = 'text' ) {
		if ( is_array( $input ) ) {
			$sanitized_input = array();

			foreach ( $input as $key => $value ) {
				$key = sanitize_key( $key );

				if ( 'url' === $type ) {
					$sanitized_input[ $key ] = esc_url( $value );
					continue;
				}

				$sanitized_input[ $key ] = sanitize_text_field( $value );
			}

			$input = $sanitized_input;
		}

		return $input;
	}

	private function checkbox( $input, $type = '' ) {
		if ( is_array( $input ) ) {
			$sanitized_input = array();

			foreach ( $input as $key => $value ) {
				$key = sanitize_key( $key );

				if ( 'bool' === $type ) {
					$sanitized_input[ $key ] = absint( $value );
					continue;
				}

				$sanitized_input[ $key ] = sanitize_key( $value );
			}

			$input = $sanitized_input;
		}

		return $input;
	}

	/**
     * Returns registestered settings section keys.
	 * 
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 * 
     * @since 1.0.0
     * @param array $args Arguments: registered settings.
     * @return array
     */
	public function getAllowedSettingsSections( $args = [] ) {        
		$allowed_keys = [];

		// Get all sections located in the registered settings.
        foreach ( $args['settings'] as $section => $tabs ) {
            $allowed_keys[] = $section;
        }

        return $allowed_keys;
	}
	
	/**
     * Returns registestered settings tab keys.
	 * Each section is consisted of sub-sections (tabs).
	 * 
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 * 
     * @since 1.0.0
     * @param array $args Arguments: settings section and registered settings.
     * @return array
     */
	private function getAllowedSettingsTabs( $args = [] ) {
		$allowed_keys = [];

		// Get all tabs (sub-sections) availible for the specific section.
        foreach ( $args['settings'][ $args['section'] ] as $tab => $settings ) {
            $allowed_keys[] = $tab;
        }

        return $allowed_keys;
	}
	
	/**
     * Returns registestered settings tab keys.
	 * Each section is consisted of sub-sections (tabs).
	 * 
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 * 
     * @since 1.0.0
     * @param array $args Arguments: settings section and registered settings.
     * @return array
     */
	private function getAllowedSettingsSettings( $args = [] ) {
		$allowed_keys = [];
		
		// Get all settings availible for the specific tab.
        foreach ( $args['settings'][ $args['section'] ][ $args['tab'] ] as $setting => $options ) {
            $allowed_keys[] = $setting;
        }

        return $allowed_keys;
	}
	
	/**
     * Check if the passed settings have valid settings keys.Activate
	 * These keys have to match the keys in the registered settings structure.
	 * 
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 * 
     * @since 1.0.0
	 * @param array $input Input that needs to be checked.
     * @param array $registered_settings Registered plugin settings.
     * @return bool
     */
	private function isValidSettingsStructure( $input ) {
		$is_valid = true;

		foreach ( $input as $section => $tabs ) {
            // Get registered settings structure.
            $sanitizer['settings'] = $this->settings_structure;

            // Get allowed settings sections based on the registered settings structure.
            $allowed_sections = $this->getAllowedSettingsSections( $sanitizer );

            // Make sure the saved section is registered in the settings.
            if ( ! in_array( $section, $allowed_sections ) ) {
                $is_valid = false;
                add_settings_error( 'assets-integration-notices', '', sprintf( __( '%s section key is not defined in the plugin settings.', 'assets-integration' ), $section ), 'error' );
               
                break;
            }

            foreach ( $tabs as $tab => $settings ) {
				$sanitizer['section'] = $section;
				
                // Get allowed settings tabs based on the settings section.
				$allowed_tabs = $this->getAllowedSettingsTabs( $sanitizer );
				
                // Make sure the saved tab is registered in the settings.
                if ( ! in_array( $tab, $allowed_tabs ) ) {
                    $is_valid = false;
                    add_settings_error( 'assets-integration-notices', '', sprintf( __( '%s tab key is not defined in the plugin settings.', 'assets-integration' ), $tab ), 'error' );
                    
                    break;
                }

                foreach ( $settings as $setting => $options ) {
                    $sanitizer['tab'] = $tab;

                    // Get allowed tab settings.
                    $allowed_settings = $this->getAllowedSettingsSettings( $sanitizer );

                    // Make sure the saved setting is registered in the settings.
                    if ( ! in_array( $setting, $allowed_settings ) ) {
                        $is_valid = false;
                        add_settings_error( 'assets-integration-notices', '', sprintf( __( '%s setting key is not defined in the plugin settings.', 'assets-integration' ), $setting ), 'error' );
						
						break;
                    }
                }
            }
        }

		return $is_valid;
	}

	/**
     * Check if the passed settings have valid settings keys.Activate
	 * These keys have to match the keys in the registered settings structure.
	 * 
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 * 
     * @since 1.0.0
	 * @param array $input Input that needs to be checked.
     * @param array $registered_settings Registered plugin settings.
     * @return array
     */
	private function getRegisteredSettingsTypes( $section, $tab ) {
		$settings_types = [];

		foreach( $this->settings_structure[ $section ][ $tab ] as $setting_ID => $setting_args ) {
            foreach ( $setting_args as $key => $value ) {
                switch ( $key ) {
                    case 'type':
                        $setting_types[ $setting_ID ][ $key ] = $value;
                        break;
                    case 'subtype':
                        $setting_types[ $setting_ID ][ $key ] = $value;
                        break;
                    default:
                        continue;
                }
            }
        }

        return $setting_types;
	}

	/**
     * Sanitize each option based on its type.
	 * 
     * @since 1.0.0
	 * @param array $new_settings Settings that need to be checked and sanitized.
	 * @param string $section Settings section
	 * @param string $tab Settings tab (sub-section)
     * @return array
     */
	private function settingsOptions( $input, $section, $tab ) {
		$setting_types = $this->getRegisteredSettingsTypes( $section, $tab );

		foreach ( $setting_types as $key => $type ) {
			switch ( $type['type'] ) {
				case 'input':

					$input[ $section ][ $tab ][ $key ] = $this->input( $input[ $section ][ $tab ][ $key ], $type['subtype'] );

					break;
				
				case 'checkbox':

					$input[ $section ][ $tab ][ $key ] = $this->checkbox( $input[ $section ][ $tab ][ $key ], $type['subtype'] );

					break;
				
				case 'radio':
				case 'select':

					// Alphanumeric characters, dashes, underscores, dots, colons and slashes are allowed.
					$input[ $section ][ $tab ][ $key ] = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $input[ $section ][ $tab ][ $key ] );

					break;
			}
		}

		return $input;
	}

	/**
     * Sanitize the settings form.
	 * 
     * @since 1.0.0
	 * @param array $new_settings Settings that need to be checked and sanitized.
     * @return array
     */
	public function settingsForm( $new_settings ) {
		// Get previously saved settings.
		$existing_settings = $this->settings->getSettings();
		
		// Get new settings.
		$new_settings = $new_settings ? $new_settings : [];
		
		// If the new settings are empty then return previously saved settings.
        if ( empty( $new_settings ) ) {
            return $existing_settings;
		}

		// TODO: make section more flexible.
		$section = 'assets';
		
		// Pull out the current section tab.
        parse_str( $_POST['_wp_http_referer'], $referrer );
        $tab = isset( $referrer['tab'] ) ? sanitize_key( $referrer['tab'] ) : 'bootstrap';

        // If the tab is not valid then return previously saved settings.
        if ( ! isset( $new_settings[ $section ][ $tab ] ) ) {
            return $existing_settings;
		}
		
		// Make sure the passed settings have valid settings keys.
		$is_valid_structure = $this->isValidSettingsStructure( $new_settings );
		
        // If the structure of the new settings is not valid  then return previously saved settings.
        if ( ! $is_valid_structure ) {
            return $existing_settings;
		}

		// Sanitize settings options.
		$new_settings = $this->settingsOptions( $new_settings, $section, $tab );

		// Combine existing settings with a new one.
		$output = array_merge( $existing_settings, $new_settings );

		add_settings_error( 'assets-integration-notices', '', esc_html__( 'Settings updated.', 'assets-integration' ), 'updated' );

		return $output;
	}
}
