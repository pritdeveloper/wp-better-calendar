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
		$selected_post_type = ! empty( $instance['post_type'] ) ? $instance['post_type'] : 'post';
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		echo '<div class="wp-better-calendar-container" data-post_type="' . $selected_post_type . '">' . wpbc_make_calendar( $selected_post_type ) . '</div>';
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Better Calendar' );
		$selected_post_type = ! empty( $instance['post_type'] ) ? $instance['post_type'] : 'post';
		// all post types
		$all_post_types = apply_filters( 'wpbc_widget_post_types', get_post_types( array(
			'public' => true,
		), 'objects' ), $instance );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"><?php esc_attr_e( 'Post Type:' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>">
				<?php foreach( $all_post_types as $post_type_id => $post_type ) { ?>
					<?php $selected = $selected_post_type == $post_type_id ? ' selected' : ''?>
					<option value="<?php echo $post_type_id ?>"<?php echo $selected ?>><?php echo $post_type->label ?></option>
				<?php } ?>
			</select>
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['post_type'] = ( ! empty( $new_instance['post_type'] ) ) ? $new_instance['post_type'] : 'post';
		return $instance;
	}
}