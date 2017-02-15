<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/pritpalsinghin
 * @since      1.0.0
 *
 * @package    Wp_Better_Calendar
 * @subpackage Wp_Better_Calendar/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Better_Calendar
 * @subpackage Wp_Better_Calendar/public
 * @author     Pritpal Singh <pritdeveloper+wp@gmail.com>
 */
class Wp_Better_Calendar_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-better-calendar-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		$admin_ajax_url = admin_url( 'admin-ajax.php' );
		?>
		<script>
			window[ 'ajaxurl' ] = '<?php echo $admin_ajax_url ?>';
		</script>
		<?php
		wp_enqueue_script( $this->plugin_name . '-blockui', '//cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js', array( 'jquery' ), $this->version );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-better-calendar-public.js', array( 'jquery' ), $this->version, false );
	}
	
	/**
	 * Activate the Widget
	 */
	public function activate_widget() {
		register_widget( 'WP_Better_Calendar_Widget' );
	}

}
