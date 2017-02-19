(function( $ ) {
	$( function() {
		var cached_posts = [];
		// override blockui defaults
		{
			$.blockUI.defaults.message = '<h3 style="color: #fff;background: transparent">Please wait...</h3>';
			$.blockUI.defaults.css.border = 'none';
			$.blockUI.defaults.css.background = 'transparent';
			$.blockUI.defaults.css.width = 'auto';
			$.blockUI.defaults.css.color = 'white';
		}
		function load_calendar ( container, post_type, month, year ) {
			cached_posts = [];
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
					container.html( '<div style="text-align: center;color: red">Something went wrong.<br />Please refresh the page.</div>' );
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
			if( wpbc_calendar_posts_list.data( 'loaded_day' ) == day ) return;
			wpbc_calendar_posts_list.data( 'loaded_day', day );
			var wpbc_small_line = container.find( '.wpbc_small_line' );
			if( wpbc_small_line.length ) wpbc_small_line.show();
			
			// check cached data
			if( day in cached_posts ) return render_posts_list( wpbc_calendar_posts_list, cached_posts[ day ] );
			if( wpbc_calendar_posts_list.html() == '' ) wpbc_calendar_posts_list.html( '<div class="wpbc_post_container"><div class="post_date">-------------</div><div><a href="javascript:;" style="color: #ee2e24;-webkit-box-shadow: none;box-shadow: none;">-------</a></div></div>' );
			wpbc_calendar_posts_list.show().block();
			$.ajax( ajaxurl, {
				method: 'post',
				data: {
					action: 'wpbc_calendar_posts_list',
					post_type: post_type,
					day: day,
					month: month,
					year: year
				},
				success: function( data ) {
					var html = '';
					data = JSON.parse( data );
					if( data.status == 'success' ) {
						posts = data.data;
						render_posts_list( wpbc_calendar_posts_list, posts );
						// saved cached posts for later use
						cached_posts[ day ] = posts;
					} else {
						html = '<h3 style="margin: 0;text-align: center">' + data.msg + '</h3>';
						wpbc_calendar_posts_list.html( html ).hide().slideDown();
					}
				},
				error: function() {
					wpbc_calendar_posts_list.html( '<div class="wpbc_post_container"><div class="post_date">Something went wrong</div><div><a href="javascript:;" style="color: #ee2e24;-webkit-box-shadow: none;box-shadow: none;cursor: default">Please try again</a></div></div>' ).hide().slideDown().data( 'loaded_day', 0 );
				},
				complete: function() {
					wpbc_calendar_posts_list.unblock();
				}
			} );
		}
		function render_posts_list( wpbc_calendar_posts_list, posts ) {
			var html = '';
			for( var i in posts ) {
				var post = posts[ i ];
				html += `
					<div class="wpbc_post_container">
						<div class="post_date">` + post.date + `</div>
						<div><a href="` + post.permalink + `" style="color: #ee2e24;-webkit-box-shadow: none;box-shadow: none;">` + post.title + `</a></div>
					</div>`;
			}
			wpbc_calendar_posts_list.html( html ).hide().slideDown();
		}
		var d = $( document );
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
		d.on( 'click', '.wpbc_year_month_container', function( e ) {
			e.preventDefault();
			var wpbc_year_month_container = $( this );
			var wpbc_year_month_selector_container = wpbc_year_month_container.closest( 'tr' ).find( '.wpbc_year_month_selector_container' );
			wpbc_year_month_container.fadeOut( 400, function() {
				wpbc_year_month_selector_container.show();
			} );
		} );
		d.on( 'click', '.wpbc_load_year_month_cancel', function( e ) {
			e.preventDefault();
			var el = $( this );
			var tr = el.closest( 'tr' );
			var wpbc_year_month_container = tr.find( '.wpbc_year_month_container' );
			var wpbc_year_month_selector_container = tr.find( '.wpbc_year_month_selector_container' );
			wpbc_year_month_selector_container.fadeOut( 400, function() {
				wpbc_year_month_container.show();
			} );
		} );
		d.on( 'click', '.wpbc_load_year_month', function( e ) {
			e.preventDefault();
			var el = $( this );
			var wpbc_year_month_selector_container = el.closest( '.wpbc_year_month_selector_container' );
			var month = wpbc_year_month_selector_container.find( '.wpbc_month_selector' ).val();
			var year = wpbc_year_month_selector_container.find( '.wpbc_year_selector' ).val();
			var container = el.closest( '.wp-better-calendar-container' );
			load_calendar( container, null, month, year );
		} );
	} );
})( jQuery );