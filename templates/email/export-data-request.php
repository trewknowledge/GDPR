<?php
defined( 'ABSPATH' ) || exit;

if ( 'html' === $args['email_content_type'] ) :
	do_action( 'gdpr_email_header', esc_html( $args['email_heading'] ) ); 
endif;

if ( ! empty ( $args['email_content'] ) ) {
	$replace_to_arr = array( '{confirm_url_xml}', '{confirm_url_json}', '{forgot_password_url}' );
	$replace_by_arr = array( esc_url_raw( $args['confirm_url_xml'] ), esc_url_raw( $args['confirm_url_json'] ), esc_url_raw( $args['forgot_password_url'] ) );
	$email_content  = str_replace( $replace_to_arr, $replace_by_arr, $args['email_content'] );

	echo esc_html__( $email_content );

} else {
	echo sprintf(
		/* translators: 1: XML download link, 2: JSON download link, 3: reset password link */
		esc_html__(
			'Someone requested to download your data from our site.
				By clicking confirm we will redirect you back to our site where a download will begin.

				To download it in a XML format, click here: %1$s
				To download it in a JSON format, click here: %2$s



				---------------------------------------------------------------------------------
				If that wasn\'t you, reset your password: %3$s
				', 'gdpr'
		),
		esc_url_raw( $args['confirm_url_xml'] ),
		esc_url_raw( $args['confirm_url_json'] ),
		esc_url_raw( $args['forgot_password_url'] )
	);
}

if ( 'html' === $args['email_content_type'] ) :
	do_action( 'gdpr_email_footer' );
endif;
