<?php
/**
 * GDPR Cookie Setting via JS
 *
 * @package Gdpr
 */

declare( strict_types=1 );

// Disable undefined variables sniff due to many false positives in the use of closures.
// phpcs:disable WordPressVIPMinimum.Variables.VariableAnalysis.UndefinedVariable

/**
 * Class Gdpr_Cookie_Setting_Js
 *
 * @package Gdpr
 */
class Gdpr_Cookie_Setting_Js {
	/**
	 * Cookies need this specific date format.
	 */
	private const GMT_DATE_FORMAT = 'D, d M Y H:i:s \G\M\T';

	/**
	 * A wrapper to ensure this code is added to the page footer.
	 *
	 * @param string $name the cookie name.
	 * @param string $value the cookie value.
	 * @param int    $expires a timestamp.
	 * @param string $path the path.
	 * @param string $domain the domain.
	 */
	public function js_setcookie( string $name, string $value = '', int $expires = 0, string $path = '/', string $domain = '' ): void {
		/**
		 * This hook will work for non-AJAX requests. An example would be initial page load, setting cookies for the
		 * first time.
		 */
		add_action(
			'wp_footer',
			function() use ( $name, $value, $expires, $path, $domain ) : bool {
				return $this->set_cookie( $name, $value, $expires, $path, $domain );
			}
		);
	}

	/**
	 * Taking the signature of `setcookie`, and abusing it to write a cookie with JS.
	 *
	 * @param string $name the cookie name.
	 * @param string $value the cookie value.
	 * @param int    $expires a timestamp.
	 * @param string $path the path.
	 * @param string $domain the domain.
	 *
	 * @return bool
	 */
	private function set_cookie( string $name, string $value = '', int $expires = 0, string $path = '/', string $domain = '' ) : bool {
		$cookie_val = sprintf( '%s=%s;', $name, $value );

		if ( 0 === $expires ) {
			$date = new \DateTime( '+1 year' );
		} else {
			$date = ( new \DateTime() )->setTimestamp( $expires );
		}

		$cookie_val .= sprintf( 'expires=%s;', $date->format( self::GMT_DATE_FORMAT ) );

		$cookie_val .= sprintf( 'path=%s;', $path );

		if ( '' !== $domain ) {
			$cookie_val .= sprintf( 'domain=%s;', $domain );
		}

		$cookie_val .= 'secure;';

		echo '<script type="text/javascript">',
			"document.cookie = '" . wp_kses_post( $cookie_val ) . "'",
		'</script>';

		return true;
	}
}
