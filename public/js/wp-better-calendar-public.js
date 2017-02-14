(function( $ ) {
	$( function() {
		var d = $( document );
		function load_calendar ( container ) {
			container.html( 'Loading Calendar...' );
		}
		$( '.wp-better-calendar-container' ).each( function() {
			var container = $( this );
			load_calendar( container );
		} );
	} );
})( jQuery );