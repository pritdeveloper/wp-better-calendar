(function( $ ) {
	$( function() {
		// override blockui defaults
		{
			$.blockUI.defaults.message = '<h3>Please wait...</h3>';
			$.blockUI.defaults.css.border = 'none';
			$.blockUI.defaults.css.background = 'transparent';
			$.blockUI.defaults.css.width = 'auto';
			$.blockUI.defaults.css.color = 'white';
		}
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
			container.data( 'month', month ).data( 'year', year ).data( 'post_type', post_type );
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
		function load_calendar_posts_list( container, day ) {
			if( !day ) return;
			var post_type = container.data( 'post_type' );
			var month = container.data( 'month' );
			var year = container.data( 'year' );
			var wpbc_calendar_posts_list = container.find( '.wpbc_calendar_posts_list' );
			if( wpbc_calendar_posts_list.data( 'loading' ) == 1 ) return;
			wpbc_calendar_posts_list.data( 'loading', 1 );
			wpbc_calendar_posts_list.slideUp().slideDown( 400 );
			if( wpbc_calendar_posts_list.html() == '' ) wpbc_calendar_posts_list.html( '...' );
			wpbc_calendar_posts_list.block();
			$.ajax( ajaxurl, {
				method: 'post',
				data: {
					action: 'wpbc_calendar_posts_list',
					post_type: post_type,
					day: day,
					month: month,
					year: year
				},
				success: function( html ) {
					wpbc_calendar_posts_list.html( html );
				},
				error: function() {
					wpbc_calendar_posts_list.html( 'Something went wrong.' );
					setTimeout( function() {
						wpbc_calendar_posts_list.slideUp( 400 );
					}, 4000 );
				},
				complete: function() {
					wpbc_calendar_posts_list.data( 'loading', 0 ).unblock();
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
		d.on( 'click', '.wpbc_show_calendar_posts_list', function( e ) {
			e.preventDefault();
			var el = $(this);
			var day = el.data( 'day' );
			var container = el.closest( '.wp-better-calendar-container' );
			load_calendar_posts_list( container, day );
		} );
	} );
})( jQuery );