<?php
defined( 'ABSPATH' ) || exit;
do_action( 'gdpr_email_header', esc_html( $args['email_heading'] ) ); 

if ( ! empty ( $args['email_content'] ) ) {
	$replace_to_arr = array( '{confirm_url}', '{forgot_password_url}' );
	$replace_by_arr = array( esc_url_raw( $args['confirm_url'] ), esc_url_raw( $args['forgot_password_url'] ) );
	$email_content  = str_replace( $replace_to_arr, $replace_by_arr, $args['email_content'] );

	echo esc_html__( $email_content );

} else {
	echo sprintf(
		/* translators: 1: Confirmation link, 2: Reset password link */
		esc_html__(
			'Someone placed a request for your information to be removed from our site.
			By clicking confirm your account will be removed from our site and all data we collected
			over time will be erased from our database. It will be impossible for us to retrieve that
			information in the future.



			To confirm this request, click here: %1$s



			---------------------------------------------------------------------------------
			If that wasn\'t you, reset your password: %2$s
			', 'gdpr'
		),
		esc_url_raw( $args['confirm_url'] ),
		esc_url_raw( $args['forgot_password_url'] )
	);
}

do_action( 'gdpr_email_footer' );
