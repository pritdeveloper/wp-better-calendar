<?php

defined( 'ABSPATH' ) or die('Not Allowed.');
// include if the WP_Widget class is not there
if( !class_exists( 'WP_Widget' ) ) require_once ABSPATH . 'wp-includes/class-wp-widget.php';

class WP_Better_Calendar_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(
			'wp_better_calendar', // Base ID
			esc_html__( 'Better Calendar' ), // Name
			array( 'description' => esc_html__( 'A Better Calendar for your website.' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		$current_month = date( 'm' );
		$current_year = date( 'Y' );
		$month_to_show = $current_month;
		$year_to_show = $current_year;
		wp_bc_show_calendar( $month_to_show, $year_to_show );
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Better Calendar' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
}