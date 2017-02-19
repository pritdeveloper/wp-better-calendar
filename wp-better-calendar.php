<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/pritpalsinghin
 * @since             1.0.0
 * @package           Wp_Better_Calendar
 *
 * @wordpress-plugin
 * Plugin Name:       WP Better Calendar
 * Plugin URI:        https://wordpress.org/plugins/wp-better-calendar/
 * Description:       A better calendar for sidebar widget.
 * Version:           1.0.1
 * Author:            Pritpal Singh
 * Author URI:        https://profiles.wordpress.org/pritpalsinghin
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-better-calendar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if( version_compare( "5.5", phpversion(), ">" ) ) return;

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-better-calendar-activator.php
 */
function activate_wp_better_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-better-calendar-activator.php';
	Wp_Better_Calendar_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-better-calendar-deactivator.php
 */
function deactivate_wp_better_calendar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-better-calendar-deactivator.php';
	Wp_Better_Calendar_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_better_calendar' );
register_deactivation_hook( __FILE__, 'deactivate_wp_better_calendar' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-better-calendar.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_better_calendar() {

	$plugin = new Wp_Better_Calendar();
	$plugin->run();

}
run_wp_better_calendar();
