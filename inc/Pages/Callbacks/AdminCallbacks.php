<?php
/**
 * Class with all callbacks needed for the admin page.
 *
 * @since 1.0.0
 * @package AssetsIntegration
 */

namespace AssetsIntegration\Pages\Callbacks;

// Exit if accessed directly.
defined( 'ABSPATH' ) || die;

class AdminCallbacks {

    /**
	 * Plugin settings API.
	 *
	 * @since 1.0.0
	 * @var object
	 */
    private $settings;

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

    /**
     * Prints description for a specific option.
     *
     * @since 1.0.0
     * @param array $description Text for the description
     * @return void
     */
    private function printDescription( $description = '' ) {
        if ( isset( $description ) && '' !== $description ) {
            printf( '<p class="description">%s</p>', $description );
        }
    }

    /**
     * Input callback.
     *
     * Prints HTML input fields.
     *
     * @since 1.0.0
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function inputOutput( $args ) {

        switch ( $args['subtype'] ) {
            case 'url':
                $input_type = 'url';
                $input_class = 'regular-text code';
                break;
            default:
                $input_type = 'text';
                $input_class = 'regular-text';
        }

        $section_key = sanitize_key( $args['section'] );
        $tab_key = sanitize_key( $args['tab'] );
        $field_key = sanitize_key( $args['id'] );

        $section_settings = $this->settings->getSettings( $section_key );

        if ( empty( $args['options'] ) ) {
            printf(
                '<input name="%1$s[%2$s][%3$s][%4$s]" class="%7$s" value="%5$s" type="%6$s">',
                $this->settings->getOptionName(),
                $section_key,
                $tab_key,
                $field_key,
                $section_settings[$tab_key][$field_key],
                $input_type,
                $input_class
            );

            return;
        }

        $option_settings = $section_settings[$tab_key][$field_key];

        $output = '<fieldset>';

        foreach( $args['options'] as $key => $option ) {
            $key = sanitize_key( $key );        

            $output .= sprintf(
                '<label for="%1$s_%2$s_%3$s_%4$s_%5$s">%7$s <br><input id="%1$s_%2$s_%3$s_%4$s_%5$s" name="%1$s[%2$s][%3$s][%4$s][%5$s]" class="%9$s" value="%6$s" type="%8$s"></label><br>',
                $this->settings->getOptionName(),
                $section_key,
                $tab_key,
                $field_key,
                $key,
                esc_attr( $option_settings[$key] ),
                esc_html( $option ),
                $input_type,
                $input_class
            );
        }

        $output .= '<fieldset>';

        echo $output;

        $this->printDescription( $args['description'] );
    }

    /**
     * Textarea callback.
     *
     * Prints an HTML textarea.
     *
     * @since 1.0.0
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function textareaOutput( $args ) {
        $section_key = sanitize_key( $args['section'] );
        $tab_key = sanitize_key( $args['tab'] );
        $field_key = sanitize_key( $args['id'] );

        $section_settings = $this->settings->getSettings( $section_key );

        printf(
            '<textarea name="%1$s[%2$s][%3$s][%4$s]" class="large-text" rows="5" cols="30">%5$s</textarea>',
            $this->settings->getOptionName(),
            $section_key,
            $tab_key,
            $field_key,
            $section_settings[$tab_key][$field_key]
        );

        $this->printDescription( $args['description'] );
    }

    /**
     * Checkbox callback.
     *
     * Prints HTML chechboxes.
     *
     * @since 1.0.0
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function checkboxOutput( $args ) {

        $section_key = sanitize_key( $args['section'] );
        $tab_key = sanitize_key( $args['tab'] );
        $field_key = sanitize_key( $args['id'] );

        $section_settings = $this->settings->getSettings( $section_key );

        if ( empty( $args['options'] ) ) {
            $checked = ! empty( $args['value'] ) ? checked( 1, $args['value'], false ) : '';

            printf(
                '<label for="%1$s_%2$s_%3$s_%4$s"><input id="%1$s_%2$s_%3$s_%4$s" name="%1$s[%2$s][%3$s][%4$s]" value="1" %5$s type="checkbox">%6$s</label>',
                $this->settings->getOptionName(),
                $section_key,
                $tab_key,
                $field_key,
                $checked,
                $args['label']
            );

            return;
        }

        $option_settings = $section_settings[$tab_key][$field_key];

        $output = '<fieldset>';

        foreach( $args['options'] as $key => $option ) {
            $checked = ! empty( $option_settings[$key] ) ? checked( 1, $option_settings[$key], false ) : '';
            
            $output .= sprintf(
                '<label for="%1$s_%2$s_%3$s_%4$s_%5$s"><input id="%1$s_%2$s_%3$s_%4$s_%5$s" name="%1$s[%2$s][%3$s][%4$s][%5$s]" value="1" %6$s type="checkbox">%7$s</label><br>',
                $this->settings->getOptionName(),
                $section_key,
                $tab_key,
                $field_key,
                esc_attr( $key ),
                esc_attr( $checked ),
                esc_html( $option )
            );
        }

        $output .= '<fieldset>';

        echo $output;

        $this->printDescription( $args['description'] );
        
    }

    /**
     * Radio callback.
     *
     * Prints HTML radio buttons.
     *
     * @since 1.0.0
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function radioOutput( $args ) {
        $section_key = sanitize_key( $args['section'] );
        $tab_key = sanitize_key( $args['tab'] );
        $field_key = sanitize_key( $args['id'] );

        $settings = $this->settings->getSettings( $section_key );
        
        if ( ! isset( $settings[ $tab_key ][ $field_key ] ) ) {
            return;
        }

        if ( empty( $args['options'] ) ) { 
            return;
        }

        $output = '<fieldset>';

        foreach( $args['options'] as $key => $option ) {
            $checked = false;

            if ( $key == $settings[ $tab_key ][ $field_key ] ) {
                $checked = true;
            }

            $output .= sprintf(
                '<label for="%1$s_%2$s_%3$s_%4$s_%5$s"><input id="%1$s_%2$s_%3$s_%4$s_%5$s" name="%1$s[%2$s][%3$s][%4$s]" value="%5$s" %6$s type="radio">%7$s</label><br>',
                $this->settings->getOptionName(),
                $section_key,
                $tab_key,
                $field_key,
                esc_attr( $key ),
                checked( true, $checked, false ),
                esc_html( $option )
            );
        }

        $output .= '</fieldset>';

        echo $output;

        $this->printDescription( $args['description'] );
    }

    /**
     * Select callback.
     *
     * Prints an HTML Dropdown.
     *
     * @since 1.0.0
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function selectOutput( $args ) {
        $section_key = sanitize_key( $args['section'] );
        $tab_key = sanitize_key( $args['tab'] );
        $field_key = sanitize_key( $args['id'] );

        $settings = $this->settings->getSettings( $section_key );
        
        if ( ! isset( $settings[ $tab_key ][ $field_key ] ) ) {
            return;
        }

        $output = sprintf(
            '<select id="%1$s_%2$s_%3$s_%4$s" name="%1$s[%2$s][%3$s][%4$s]">',
            $this->settings->getOptionName(),
            $section_key,
            $tab_key,
            $field_key
        );

        if ( ! empty( $args['options'] ) ) {
            foreach( $args['options'] as $key => $option ) {
                $selected = selected( $settings[ $tab_key ][ $field_key ], $key, false );

                $output .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $option ) . '</option>';
            }
        }

        $output .= '</select>';

        echo $output;

        $this->printDescription( $args['description'] );
    }

    /**
     * Missing callback
     *
     * If a function is missing for settings callbacks alert the user.
     *
     * @since 1.0.0
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function missingCallback( $args ) {
    	printf(
    		esc_html__( 'The callback function used for the %s setting is missing.', 'cluedock' ),
    		'<strong>' . $args['id'] . '</strong>'
    	);
    }
}
