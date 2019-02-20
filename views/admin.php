<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <?php 
    // Get current active tab.
    $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'bootstrap';

    do_action( 'assets_integration_admin_header' ); ?>

    <form 
        id="assets-settings-<?php echo esc_attr( $active_tab ); ?>" 
        class = "<?php echo esc_attr( $active_tab ); ?>-settings assets-settings"
        method="post" 
        action="options.php">

        <?php 
        settings_fields( 'assets_integration_settings' );
        do_settings_sections( 'assets_integration_assets_' . $active_tab ); 

        submit_button(); ?>

    </form><!-- #assets-settings -->
</div><!-- .wrap -->
