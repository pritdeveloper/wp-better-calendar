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
function wpbc_get_calendar() {
    global $wpdb, $wp_locale;
    $post_type = isset( $_POST[ 'post_type' ] ) ? $_POST[ 'post_type' ] : 'post';
    // month and year
    {
        $month = isset( $_POST[ 'month' ] ) ? $_POST[ 'month' ] : date( 'n' );
        $year = isset( $_POST[ 'year' ] ) ? $_POST[ 'year' ] : date( 'Y' );
    }
    $calendar = wpbc_make_calendar( $post_type, $month, $year );
    echo apply_filters( 'wpbc_get_calendar', $calendar, $post_type, $month, $year );
    die;
}

function wpbc_make_calendar( $post_type = 'post', $month = null, $year = null ) {
    if( !$month ) $month = date( 'n' );
    if( !$year ) $year = date( 'Y' );
    ob_start();
    // get week days
    {
        $timestamp = strtotime( 'next Monday' );
        $days = array();
        for ( $i = 0; $i < 7; $i++ ) {
            $day = strftime( '%A', $timestamp );
            $days[] = substr( $day, 0, 1 );
            $timestamp = strtotime('+1 day', $timestamp);
        }
    }
    
    // get first and last day for calendar
    {
        $first_day_num = date_create_from_format( 'Y-n-d', "{$year}-{$month}-01" )->format( 'N' );
        $last_day_of_month = new DateTime( "{$year}-{$month}-01" );
        $last_day_of_month->modify( "last day of this month" );
        $last_day_of_month = $last_day_of_month->format( "j" );
    }
    
    // get the previous and next months and years
    {
        // prev_next
        {
            $d = date_create_from_format( 'Y-n', "{$year}-{$month}" );
            $prev_month = $d->modify( 'first day of previous month' )->format( 'n' );
            
            $d = date_create_from_format( 'Y-n', "{$year}-{$month}" );
            $prev_year = $d->modify( 'first day of previous month' )->format( 'Y' );
            
            $prev_mnth_short_name = date_create_from_format( 'n', $prev_month )->format( 'M' );
        }
        
        // next
        {
            $d = date_create_from_format( 'Y-n', "{$year}-{$month}" );
            $next_month = $d->modify( 'first day of next month' )->format( 'n' );
            
            $d = date_create_from_format( 'Y-n', "{$year}-{$month}" );
            $next_year = $d->modify( 'first day of next month' )->format( 'Y' );
            
            $next_mnth_short_name = date_create_from_format( 'n', $next_month )->format( 'M' );
        }
    }
    
    // today
    {
        $today_date = date( 'j' );
        $today_month = date( 'n' );
        $today_year = date( 'Y' );
    }
    ?>
    <button class="wpbc_refresh_button" type="button" style="width: 100%">Refresh</button>
    <table data-post_type="<?php echo $post_type ?>" data-month="<?php echo $month ?>" data-year="<?php echo $year ?>">
        <thead>
            <tr class="mnth_year">
                <th colspan="7">
                    <span><?php echo date_create_from_format( 'n Y', "{$month} {$year}" )->format( 'F Y' ) ?></span>
                </th>
            </tr>
            <tr class="week_days">
                <?php foreach( $days as $day ) { ?>
                    <th><?php echo $day ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                $ct = 0;
                if( $first_day_num > 1 ) {
                    $ct = $first_day_num - 1;
                    echo '<td colspan="' . $ct . '" class="cell"></td>';
                }
                ?>
                <?php for( $i = 1; $i <= $last_day_of_month; $i++ ) { ?>
                    <?php $is_today = $i == $today_date && $month == $today_month && $year == $today_year ?>
                    <td class="cell day<?php echo $is_today ? ' today' : '' ?> transition transition_200"><?php echo $i ?></td>
                    <?php $ct++ ?>
                    <?php
                    if( $ct == 7 ) {
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
        <tfoot>
        </tfoot>
    </table>
    <table class="prev_next"><tbody><tr><td><a href="javascript:;" class="wpbc_show_calendar_click" data-post_type="<?php echo $post_type ?>" data-month="<?php echo $prev_month ?>" data-year="<?php echo $prev_year ?>"><div>&laquo; <?php echo $prev_mnth_short_name ?></div></a></span></td><td><a href="javascript:;" class="wpbc_show_calendar_click" data-post_type="<?php echo $post_type ?>" data-month="<?php echo $next_month ?>" data-year="<?php echo $next_year ?>"><div><?php echo $next_mnth_short_name ?> &raquo;</div></a></span></td></tr></tbody></table>
    <?php
    return ob_get_clean();
}