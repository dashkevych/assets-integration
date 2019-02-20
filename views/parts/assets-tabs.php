<?php
/**
 * Template part for displaying the asset tabs.
 *
 * @since 1.0.0
 * @package AssetsIntegration
 */

if ( empty( $this->page_tabs ) ) {
    return;
}

$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'bootstrap'; ?>

<ul class="nav-tab-wrapper">
    <?php 
    foreach ( $this->page_tabs as $tab_id => $tab_name ) : 

        $tab_url = add_query_arg( array(
            'tab' => $tab_id,
        ) ); 
        
        $active = ( $active_tab == $tab_id ) ? ' nav-tab-active' : ''; 
        
        printf(
            '<li class="nav-tab %1$s"><a href="%2$s">%3$s</a></li>',
            $active,
            esc_url( $tab_url ),
            esc_html( $tab_name )
        );
        
    endforeach; ?>
</ul>