;( function( $ ) {
		
	'use strict';
	
	var WOOQV_ADMIN = {};

	WOOQV_ADMIN.notices = {
		
		init: function() {
			
			setTimeout(function() {
			
				$('.wooqv-notice').each(function() {
					$(this).prependTo('#wpbody-content');
				});
				
			},10)
		}
	};
	
	$(document).ready(function() {
		
		WOOQV_ADMIN.notices.init();
	});

})( jQuery );	