// MDN polyfill for "closest".
import 'mdn-polyfills/Element.prototype.closest';

document.addEventListener("DOMContentLoaded", function() {
    // This looks very messy. Needs to be rewritten in the near future.
    
    let bootstrapForm = document.getElementById( 'assets-settings-bootstrap' );

    if ( null === bootstrapForm ) {
        return;
    }

    let assetDeliveryLocal = bootstrapForm.querySelector( '#assets_integration_settings_assets_bootstrap_is_cdn_' );
    let assetDeliveryCDN = bootstrapForm.querySelector( '#assets_integration_settings_assets_bootstrap_is_cdn_1' );
    let assetPriority = bootstrapForm.querySelector( '#assets_integration_settings_assets_bootstrap_priority' );
    
    assetDeliveryLocal.closest( 'tr' ).classList.add( 'show' );
    assetPriority.closest( 'tr' ).classList.add( 'show' );

    let assetLocalVersion = bootstrapForm.querySelector( '#assets_integration_settings_assets_bootstrap_local_version' );

    let assetLocalCSS = bootstrapForm.querySelector( '#assets_integration_settings_assets_bootstrap_local_assets_css' );
    let assetCDNCSS = bootstrapForm.querySelector( '#assets_integration_settings_assets_bootstrap_cdn_assets_css' );

    if ( assetDeliveryLocal.checked ) {
        localOptions();
    } else {
        cdnOptions();
    }

    assetDeliveryLocal.addEventListener( 'change', function() {
        if ( ! assetDeliveryLocal.checked ) {
            return;
        }

        localOptions();
    }, false );

    assetDeliveryCDN.addEventListener( 'change', function() {
        if ( ! assetDeliveryCDN.checked ) {
            return;
        }

        cdnOptions();
    }, false );

    assetLocalVersion.addEventListener( 'change', function() {
        // hide section with local assets.
        if ( '' === assetLocalVersion.value ) {
            assetLocalCSS.closest( 'tr' ).classList.remove( 'show' );
            return;
        }

        // show section with local assets.
        assetLocalCSS.closest( 'tr' ).classList.add( 'show' );
    }, false );

    function localOptions() {
        // show section with local assets.
        if ( '' !== assetLocalVersion.value ) {
            assetLocalCSS.closest( 'tr' ).classList.add( 'show' );
        }

        // show section that shows version for the local assets.
        assetLocalVersion.closest( 'tr' ).classList.add( 'show' );
        // hide section with CDN URLs.
        assetCDNCSS.closest( 'tr' ).classList.remove( 'show' );
    }

    function cdnOptions() {
        // hide section that shows version for the local assets.
        assetLocalVersion.closest( 'tr' ).classList.remove( 'show' );
        // hide section with local assets.
        assetLocalCSS.closest( 'tr' ).classList.remove( 'show' );
        // display section with CDN URLs.
        assetCDNCSS.closest( 'tr' ).classList.add( 'show' );
    }
});