(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
     *
     * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
     *
     * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.

	 */

})( jQuery );

jQuery( document ).ready( function() {
	jQuery( 'body' ).on( 'change', 'input[name="payment_method"]', function() {
		jQuery( 'body' ).trigger( 'update_checkout' );
	} );
} );

jQuery( window ).load( function() {
	if ( jQuery( '.forceall_shipping_method' ).length ) {
		if ( jQuery( '.forceall_shipping_method' ).is( ':hidden' ) ) {
			updateCartButton();
		}
	}

	function updateCartButton() {
		jQuery( '.forceall_shipping_method' ).attr( 'checked', true ).trigger( 'change' );
		var checked = jQuery( '.forceall_shipping_method' ).is( ':checked' );
		if ( checked == 'true' ) {
			jQuery( '[name=\'update_cart\']' ).trigger( 'click' );
		}
	}
	function update_quantity(){
		// Fires whenever variation selects are changed
		var min_qty = jQuery( '.quantity .input-text.qty.text' ).attr( 'min' );
		if('' != min_qty){
			jQuery( '.quantity .input-text.qty.text' ).val( min_qty );
		}
		console.log('on');
	}
	jQuery( '.variations_form' ).on( 'woocommerce_variation_select_change', function() {
		setTimeout(update_quantity, 200)
	} );
} );

