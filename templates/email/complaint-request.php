<?php
defined( 'ABSPATH' ) || exit;
do_action( 'gdpr_email_header', esc_html( $args['email_heading'] ) ); 

if ( ! empty ( $args['email_content'] ) ) {
	$replace_to_arr = array( '{data}', '{confirm_url}', '{forgot_password_url}' );
	$replace_by_arr = array( esc_html( $args['data'] ), esc_url_raw( $args['confirm_url'] ), esc_url_raw( $args['forgot_password_url'] ) );
	$email_content  = str_replace( $replace_to_arr, $replace_by_arr, $args['email_content'] );

	echo esc_html__( $email_content );

} else {
	echo sprintf(
			/* translators: 1: The complaint content, 2: confirmation link, 3: reset password link */
			esc_html__(
				'Someone placed a complaint on your behalf on our site.
		By clicking confirm a request will be made and we will do our best to fulfil it.

		--------------------------------------------------------
		Request
		--------------------------------------------------------
		%1$s




		To confirm this request, click here: %2$s



		---------------------------------------------------------------------------------
		If that wasn\'t you, reset your password: %3$s
		', 'gdpr'
			),
			esc_html( $args['data'] ),
			esc_url_raw( $args['confirm_url'] ),
			esc_url_raw( $args['forgot_password_url'] )
		);
}



do_action( 'gdpr_email_footer' );
