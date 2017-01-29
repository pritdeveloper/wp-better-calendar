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

function wp_bc_show_calendar( $month_to_show, $year_to_show ) {
    $date = date_create_from_format( 'Y-m', $year_to_show . "-" . $month_to_show );
    ?>
    <div>
        <?php echo $date->format( 'F Y' ) ?>
    </div>
    <?php
}