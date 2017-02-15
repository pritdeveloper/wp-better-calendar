(function( $ ) {
	$.blockUI.defaults.message = '<h3>Please wait...</h3>';
	$.blockUI.defaults.css.border = 'none';
	$.blockUI.defaults.css.background = 'transparent';
	$.blockUI.defaults.css.width = 'auto';
	$.blockUI.defaults.css.color = 'white';
	$( function() {
		function load_calendar ( container, post_type, month, year ) {
			// month and year
			{
				var d = new Date();
				var current_month = d.getMonth();
				current_month++;
				var current_year = d.getFullYear();
				if( !month ) month = current_month;
				if( !year ) year = current_year;
			}
			if( !post_type ) post_type = container.data( 'post_type' );
			container.block();
			$.ajax( ajaxurl, {
				method: 'post',
				data: {
					action: 'wpbc_get_calendar',
					post_type: post_type,
					month: month,
					year: year
				},
				success: function( container_html ) {
					container.html( container_html );
				},
				error: function() {
					container.html('Something went wrong. Please refresh the page.');
				},
				complete: function() {
					container.unblock();
				}
			} );
		}
		var d = $( document );
		d.on( 'click', '.wpbc_refresh_button', function( e ) {
			e.preventDefault();
			var el = $(this);
			var container = el.closest( '.wp-better-calendar-container' );
			var post_type = container.data( 'post_type' );
			load_calendar( container, post_type );
		} );
		d.on( 'click', '.wpbc_show_calendar_click', function( e ) {
			e.preventDefault();
			var el = $(this);
			var post_type = el.data( 'post_type' );
			var month = el.data( 'month' );
			var year = el.data( 'year' );
			var container = el.closest( '.wp-better-calendar-container' );
			load_calendar( container, post_type, month, year );
		} );
		// d.find( '.wp-better-calendar-container' ).each( function() {
		// 	var container = $( this );
		// 	load_calendar( container );
		// } );
	} );
})( jQuery );