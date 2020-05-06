(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

	$( window ).load(function() {

		$(".send-sms-button").click(function(){
			if ($.trim($("#send_customer_sms").val())) {
				$("#sms-spinner").css('visibility', 'visible');
				$('.send-sms-button').prop('disabled', true);
				var data = {
					'action': 'send_sms',
					'phone': $('#_billing_phone').val(),
					'sms': $('#send_customer_sms').val()
				};
				
				jQuery.post(ajaxurl, data, function(response) {
					location.reload();
				});
			}			
		});
	});

})( jQuery );
