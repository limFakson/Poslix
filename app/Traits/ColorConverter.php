<?php
namespace App\Traits;

trait ColorConverter {

    // color converter from hexadecimal to rgb or rgba

    protected function hex2rgba( $color, $opacity = false ) {
        $default = 'rgb(0,0,0)';
        if ( empty( $color ) ) {
            return $default;
        }
        if ( $color[ 0 ] == '#' ) {
            $color = substr( $color, 1 );
        }

        if ( strlen( $color ) >= 7 ) {
            $hex = array( $color[ 0 ] . $color[ 1 ], $color[ 2 ] . $color[ 3 ], $color[ 4 ] . $color[ 5 ] );
        } elseif ( strlen( $color ) == 6 ) {
            $hex = array( $color[ 0 ] . $color[ 1 ], $color[ 2 ] . $color[ 3 ], $color[ 4 ] . $color[ 5 ] );
        } elseif ( strlen( $color ) == 3 ) {
            $hex = array( $color[ 0 ] . $color[ 0 ], $color[ 1 ] . $color[ 1 ], $color[ 2 ] . $color[ 2 ] );
        } else {
            return $default;
        }

        $rgb = array_map( 'hexdec', $hex );

        if ( $opacity ) {
            if ( abs( $opacity ) > 1 )
            $opacity = 1.0;
            $output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode( ',', $rgb ) . ')';
        }

        return $output;
    }
}