<?php
/**
 * GDPR Cookie Tracking
 *
 * @package Gdpr
 */

declare( strict_types=1 );

/**
 * Class Gdpr_Cookie_Tracking
 *
 * @package Gdpr
 */
class Gdpr_Cookie_Tracking {

	public const OPTIONAL_AUTOMATTIC_COOKIE = 'tk_ai';
	public const OPTIONAL_ANALYTICS_COOKIE = '_ga';

	/**
	 * Removes code that calls third party tracking scripts
	 *
	 * @return void
	 */
	public function remove_tracking_scripts() : void {
		add_filter( 'jetpack_honor_dnt_header_for_stats', '__return_true' );

		do_action( 'gdpr_remove_tracking_scripts' );
	}

	/**
	 * Do not track JetPack.
	 *
	 * @return bool
	 */
	public function do_not_track_automattic_jetpack() : bool {
		return $this->do_not_track( self::OPTIONAL_AUTOMATTIC_COOKIE );
	}

	/**
	 * Do not track Google.
	 *
	 * @return bool
	 */
	public function do_not_track_google() : bool {
		return $this->do_not_track( self::OPTIONAL_ANALYTICS_COOKIE );
	}

	/**
	 * Do not track.
	 *
	 * @param string $cookie the cookie to check for.
	 * @return bool
	 */
	private function do_not_track( string $cookie ) : bool {
		if ( \function_exists( 'is_allowed_cookie' ) && is_allowed_cookie( $cookie ) ) {
			return true;
		}

		$this->remove_tracking_scripts();

		return false;
	}
}
