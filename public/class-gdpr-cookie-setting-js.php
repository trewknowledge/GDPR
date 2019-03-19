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
	 * The transient prefix.
	 */
	private const TRANSIENT_PREFIX = 'gdpr_cookie_setting_';

	/**
	 * When the transient should expire - 60s * 60m = 1 hour.
	 */
	private const EXPIRES_IN = 60 * 60;

	/**
	 * The GDPR Plugin uses these cookie
	 */
	private const GDPR_COOKIES = [
		'gdpr[allowed_cookies]',
		'gdpr[consent_types]',
	];

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

		/**
		 * When a site visitors accepts the Cookie Policy / GDPR Cookie Bar, this is sent in to WP as an AJAX request.
		 * The hook above will attempt to run on that AJAX request. This will not have the expected / intended outcome.
		 *
		 * Therefore, by saving the cookie data to a WP transient, we can check for the existence of that transient
		 * on the next "typical" web request, and set the cookies on that request, which does give the desired outcome.
		 */
		$transient_name = $this->get_transient_name( $name );

		$data = [$name, $value, $expires, $path, $domain];

		set_site_transient( $transient_name, $data, self::EXPIRES_IN );
	}

	/**
	 * For each of the `GDPR_Cookies`, write any saved cookie information for the visitor, as per the data they have
	 * stored in transients on previous requests.
	 */
	public function set_cookies_from_transients_on_page_load(): void {
		array_map(
			function ( string $cookie_name ) : bool {
				$transient_name = $this->get_transient_name( $cookie_name );

				$data = get_site_transient( $transient_name );

				delete_site_transient( $transient_name );

				if ( false === $data ) {
					return false;
				}

				[$name, $value, $expires, $path, $domain] = $data;

				add_action(
					'wp_footer',
					function() use ( $name, $value, $expires, $path, $domain ) : bool {
						return $this->set_cookie( $name, $value, $expires, $path, $domain );
					}
				);

				return true;
			},
			self::GDPR_COOKIES
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

	/**
	 * Example outcomes:
	 *
	 * gdpr_cookie_setting_gdpr[allowed_cookies]_4ffc0746ac855d3b4a6094c7978198a3
	 * gdpr_cookie_setting_gdpr[consent_types]_4ffc0746ac855d3b4a6094c7978198a3
	 *
	 * The COOKIEHASH should always be the same for a given session.
	 *
	 * @param string $cookie_name the cookie name.
	 * @return mixed|void
	 */
	private function get_transient_name( string $cookie_name ) {
		return apply_filters( 'woocommerce_cookie', self::TRANSIENT_PREFIX  . $cookie_name . '_' . COOKIEHASH );
	}
}
