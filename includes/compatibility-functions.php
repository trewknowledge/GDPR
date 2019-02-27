<?php
/**
 * The plugin compatibility functions
 *
 * @package    GDPR
 */

if ( ! function_exists( 'boolval' ) ) {
	function boolval( $var ) {
		return (bool) $var;
	}
}
