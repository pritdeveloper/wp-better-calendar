<?php

defined( 'ABSPATH' ) or die( 'Not Allowed.' );

if( !function_exists( 'pr' ) ) {
    function pr( $e ) {
        echo "<pre>";
        print_r( $e );
        echo "</pre>";
    }
}

if( !function_exists( 'vd' ) ) {
    function vd( $e ) {
        echo "<pre>";
        var_dump( $e );
        echo "</pre>";
    }
}

if (!function_exists('jl')) {
    function jl( $e, $loc = __DIR__, $file_name = '', $raw_log = false ) {
        $raw_log = $raw_log === true;
        if( !is_dir( $loc ) ) $loc = __DIR__;
        if( !$file_name ) {
            $file_name = 'log' . ( !$raw_log ? '.json' : '' ) ;
        }
        $log_data = $raw_log ? print_r( $e, true ) : @json_encode( $e, JSON_PRETTY_PRINT );
        @error_log( $log_data . "\n\n", 3, $loc . "/{$file_name}" );
    }
}

if (!function_exists('lg')) {
    function lg( $e, $loc = __DIR__, $file_name = '' ) {
        jl( $e, $loc, $file_name, true );
    }
}

add_action( 'wp_ajax_wpbc_get_calendar', 'wpbc_get_calendar' );
add_action( 'wp_ajax_nopriv_wpbc_get_calendar', 'wpbc_get_calendar' );
if (!function_exists('wpbc_get_calendar')) {
    function wpbc_get_calendar() {
        $post_type = isset( $_POST[ 'post_type' ] ) ? wpbc_clean( $_POST[ 'post_type' ] ) : 'post';
        // month and year
        {
            $month = isset( $_POST[ 'month' ] ) ? absint( $_POST[ 'month' ] ) : date( 'n' );
            $year = isset( $_POST[ 'year' ] ) ? absint( $_POST[ 'year' ] ) : date( 'Y' );
        }
        $calendar = wpbc_make_calendar( $post_type, $month, $year );
        echo apply_filters( 'wpbc_get_calendar', $calendar, $post_type, $month, $year );
        die;
    }
}

if (!function_exists('wpbc_make_calendar')) {
    function wpbc_make_calendar( $post_type = 'post', $month = null, $year = null ) {
        global $wpdb, $wp_locale;
        $post_type_obj = get_post_type_object( $post_type );
        if( !$month ) $month = date( 'n' );
        if( !$year ) $year = date( 'Y' );
        // start of week
        $start_of_week = get_option( 'start_of_week' );
        ob_start();
        // get week days
        {
            $timestamp = strtotime( 'next ' . $wp_locale->get_weekday( $start_of_week ) );
            $days = array();
            for ( $i = 0; $i < 7; $i++ ) {
                $day = strftime( '%A', $timestamp );
                $days[ $day ] = substr( $day, 0, 1 );
                $timestamp = strtotime('+1 day', $timestamp);
            }
        }
        
        // get first and last day for calendar
        {
            $first_day_num = date_create_from_format( 'Y-n-d', "{$year}-{$month}-01" )->format( 'w' );
            $last_day_of_month = new DateTime( "{$year}-{$month}-01" );
            $last_day_of_month->modify( "last day of this month" );
            $last_day_of_month = $last_day_of_month->format( "j" );
        }
        
        // get the previous and next months and years
        {
            // previous
            {
            	$previous = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
            		FROM {$wpdb->posts}
            		WHERE post_date < '$year-$month-01'
            		AND post_type = '{$post_type}' AND post_status = 'publish'
        			ORDER BY post_date DESC
        			LIMIT 1");
        		if( $previous ) {
            		$prev_month = $previous->month;
            		$prev_year = $previous->year;
                    $prev_mnth_short_name = date_create_from_format( 'n', $prev_month )->format( 'M' );
    		    }
            }
            
            // next
            {
            	$next = $wpdb->get_row("SELECT MONTH(post_date) AS month, YEAR(post_date) AS year
            		FROM $wpdb->posts
            		WHERE post_date > '$year-$month-{$last_day_of_month} 23:59:59'
            		AND post_type = '{$post_type}' AND post_status = 'publish'
        			ORDER BY post_date ASC
        			LIMIT 1");
        		if( $next ) {
                    $next_month = $next->month;
        		    $next_year = $next->year;
                    $next_mnth_short_name = date_create_from_format( 'n', $next_month )->format( 'M' );
    		    }
            }
        }
        
        // today
        {
            $today_date = date( 'j' );
            $today_month = date( 'n' );
            $today_year = date( 'Y' );
        }
        
        // post type years for the selected post type
        $post_type_years = wpbc_get_post_type_years( $post_type );
        ?>
        <table data-post_type="<?php echo $post_type ?>" data-month="<?php echo $month ?>" data-year="<?php echo $year ?>">
            <thead>
                <tr class="mnth_year">
                    <th colspan="7">
                        <?php if( $previous || $next ) { ?>
                        <span class="wpbc_year_month_container" title="Click to edit"><?php echo date_create_from_format( 'n Y', "{$month} {$year}" )->format( 'F Y' ) ?></span>
                        <div class="wpbc_year_month_selector_container">
                            <a href="javascript:;" class="wpbc_load_year_month_cancel" title="Cancel">&times;</a>
                            <select class="wpbc_month_selector">
                                <?php for( $i = 1; $i <= 12; $i++ ) { ?>
                                    <?php $selected = $i == $month ? ' selected' : '' ?>
                                    <option value="<?php echo $i ?>"<?php echo $selected ?>><?php echo date_create_from_format( 'n', $i )->format( ( count( $post_type_years ) > 1 ? 'M' : 'F' ) ) ?></option>
                                <?php } ?>
                            </select>
                            <?php if( count( $post_type_years ) > 1 ) { ?>
                                <select class="wpbc_year_selector">
                                    <?php foreach( $post_type_years as $post_type_year ) { ?>
                                        <?php $selected = $post_type_year == $year ? ' selected' : '' ?>
                                        <option value="<?php echo $post_type_year ?>"<?php echo $selected ?>><?php echo $post_type_year ?></option>
                                    <?php } ?>
                                </select>
                            <?php } else { ?>
                                <input type="hidden" class="wpbc_year_selector" value="<?php echo current( $post_type_years ) ?>" />
                            <?php } ?>
                            <a href="javascript:;" class="wpbc_load_year_month">Go</a>
                        </div>
                        <?php } else { ?>
                            <span><?php echo date_create_from_format( 'n Y', "{$month} {$year}" )->format( 'F Y' ) ?></span>
                        <?php } ?>
                    </th>
                </tr>
                <tr class="week_days">
                    <?php foreach( $days as $day_label => $day ) { ?>
                        <th title="<?php echo $day_label ?>"><?php echo $day ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <?php ob_start() ?>
            <tbody>
                <tr>
                    <?php
                    $month_has_any_post = false;
                    $ct = calendar_week_mod( $first_day_num - $start_of_week );
                    if( $ct ) echo '<td colspan="' . $ct . '" class="cell"></td>';
                    ?>
                    <?php for( $i = 1; $i <= $last_day_of_month; $i++ ) { ?>
                        <?php
                        $is_today = $i == $today_date && $month == $today_month && $year == $today_year;
                        // day has posts
                        {
                            $day = date_create_from_format( "Y-n-j", "{$year}-{$month}-{$i}" )->format( "Y-m-d" );
                            $day_start = $day . ' 00:00:00';
                            $day_end = $day . ' 23:59:59';
                            $query = $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_type='%s' AND post_status='publish' AND post_date >= '%s' AND post_date <= '%s' LIMIT 1", $post_type, $day_start, $day_end );
                            $row = $wpdb->get_row( $query );
                            $day_has_posts = $row !== null;
                            if( $day_has_posts ) $month_has_any_post = true;
                        }
                        ?>
                        <td class="cell day<?php echo $is_today ? ' today' : '' ?><?php echo $day_has_posts ? ' has_posts' : '' ?> transition transition_200"<?php echo $is_today ? ' title="Today"' : '' ?>>
                            <?php
                            if( $day_has_posts ) echo '<a href="javascript:;" class="wpbc_show_calendar_posts_list" data-post_type="' . $post_type . '" data-day="' . $i . '" data-month="' . $month . '" data-year="' . $year . '">' . $i . '</a>';
                            else echo $i;
                            ?>
                        </td>
                        <?php $ct++ ?>
                        <?php
                        if( $i < $last_day_of_month && $ct == 7 ) {
                            echo "</tr><tr>";
                            $ct = 0;
                        }
                        if( $i == $last_day_of_month && $ct < 7 ) {
                            $colspan = 7 - $ct;
                            echo '<td colspan="' . $colspan . '" class="cell"></td>';
                        }
                        ?>
                    <?php } ?>
                </tr>
            </tbody>
            <?php
            $tb = ob_get_clean();
            echo $month_has_any_post ? $tb : '';
            ?>
            <?php if( !$month_has_any_post ) { ?>
                <tfoot><tr><td colspan="7" style="text-align: center;color: #ee2e24">No <?php echo $post_type_obj->labels->singular_name ?> for <i><?php echo date_create_from_format( 'n Y', "{$month} {$year}" )->format( 'F Y' ) ?></i></td></tr></tfoot>
            <?php } ?>
        </table>
        <?php if( $previous || $next ) { ?>
            <table class="prev_next">
                <tbody>
                    <tr>
                        <td>
                            <?php if( $previous ) { ?>
                                <a href="javascript:;" class="wpbc_show_calendar_click" data-post_type="<?php echo $post_type ?>" data-month="<?php echo $prev_month ?>" data-year="<?php echo $prev_year ?>"><div>&laquo; <?php echo $prev_mnth_short_name ?></div></a>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if( $next ) { ?>
                                <a href="javascript:;" class="wpbc_show_calendar_click" data-post_type="<?php echo $post_type ?>" data-month="<?php echo $next_month ?>" data-year="<?php echo $next_year ?>"><div><?php echo $next_mnth_short_name ?> &raquo;</div></a>
                            <?php } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="wpbc_small_line"></div>
        <?php } ?>
        <div class="wpbc_calendar_posts_list"></div>
        <?php
        return ob_get_clean();
    }
}

add_action( 'wp_ajax_wpbc_calendar_posts_list', 'wpbc_calendar_posts_list' );
add_action( 'wp_ajax_nopriv_wpbc_calendar_posts_list', 'wpbc_calendar_posts_list' );
if (!function_exists('wpbc_calendar_posts_list')) {
    function wpbc_calendar_posts_list() {
        $post_type = isset( $_POST[ 'post_type' ] ) ? wpbc_clean( $_POST[ 'post_type' ] ) : 'post';
        $day = isset( $_POST[ 'day' ] ) ? absint( $_POST[ 'day' ] ) : '';
        $month = isset( $_POST[ 'month' ] ) ? absint( $_POST[ 'month' ] ) : date( 'n' );
        $year = isset( $_POST[ 'year' ] ) ? absint( $_POST[ 'year' ] ) : date( 'Y' );
        $posts_list = wpbc_get_calendar_list( $post_type, $day, $month, $year );
        echo json_encode( $posts_list );
        die;
    }
}

if (!function_exists('wpbc_get_calendar_list')) {
    function wpbc_get_calendar_list( $post_type = 'post', $day = null, $month = null, $year = null ) {
        $ret = array(
            'status' => '',
            'msg' => '',
            'data' => ''
        );
        global $wpdb;
        $post_type_obj = get_post_type_object( $post_type );
        if( !$day ) {
            $ret[ 'status' ] = 'error';
            $ret[ 'msg' ] = 'Something went wrong.<br />Please try again.';
            return apply_filters( 'wpbc_get_calendar_list', $ret, $post_type, $day, $month, $year );
        }
        if( !$month ) $month = date( 'n' );
        if( !$year ) $year = date( 'Y' );
        // get post ids for current day
        {
            $date = date_create_from_format( "Y-n-j", "{$year}-{$month}-{$day}" )->format( "Y-m-d" );
            $date_start = $date . ' 00:00:00';
            $date_end = $date . ' 23:59:59';
            $query = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type='%s' AND post_status='publish' AND post_date >= '%s' AND post_date <= '%s'", $post_type, $date_start, $date_end );
            $results = $wpdb->get_results( $query );
            if( empty( $results ) ) {
                $ret[ 'status' ] = 'error';
                $ret[ 'msg' ] = 'No ' . $post_type_obj->labels->singular_name . ' found for this day.';
                return apply_filters( 'wpbc_get_calendar_list', $ret, $post_type, $day, $month, $year );
            }
            $post_ids = array();
            foreach( $results as $result ) $post_ids[] = $result->ID;
        }
        
        // make data
        {
            $data = array();
            foreach( $post_ids as $post_id ) {
                $data[] = array(
                    'id' => $post_id,
                    'date' => get_the_date( 'F d, Y', $post_id ),
                    'permalink' => get_post_permalink( $post_id ),
                    'title' => get_the_title( $post_id ),
                );
            }
            $ret[ 'status' ] = 'success';
            $ret[ 'data' ] = apply_filters( 'wpbc_get_calendar_list_data', $data, $post_type, $day, $month, $year );
        }
        return apply_filters( 'wpbc_get_calendar_list', $ret, $post_type, $day, $month, $year );
    }
}

if (!function_exists('wpbc_get_post_type_years')) {
    function wpbc_get_post_type_years( $post_type = 'post' ) {
        global $wpdb;
        $post_type_years = array();
        $query = $wpdb->prepare( "SELECT DISTINCT YEAR(post_date) as year FROM {$wpdb->posts} WHERE post_type='%s' AND post_status='publish' ORDER BY year", $post_type );
        $results = $wpdb->get_results( $query );
        if( !empty( $results ) ) foreach( $results as $result ) $post_type_years[] = $result->year;
        return $post_type_years;
    }
}

if( !function_exists( 'wpbc_clean' ) ) {
    function wpbc_clean( $var ) {
    	if ( is_array( $var ) ) {
    		return array_map( 'wpbc_clean', $var );
    	} else {
    		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
    	}
    }
}