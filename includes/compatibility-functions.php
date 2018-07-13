<?php
/**
 * The plugin compatibility functions
 *
 * @package    GDPR
 */

/**
 * compatibility function array_filter before php 5.6
 * $flag is new in php 5.6
 */
function array_filter_compat( array $array, $callback, $flag = 0 ) {
	if ( $flag == 0 ) {
		return array_filter( $array, $callback );
	}
	elseif ( $flag == ARRAY_FILTER_USE_KEY ) {
		$matchedKeys = array_filter( array_keys( $array ), $callback );
		return array_intersect_key( $array, array_flip( $matchedKeys ) );
	}
	else { /* ARRAY_FILTER_USE_BOTH */
		$matchedKeys = array_filter(array_keys( $array ), $callback );
		$matchedValues = array_filter( $array, $callback );
		return array_intersect_key( $array, array_flip( $matchedKeys ) + $matchedValues );
	}
}

