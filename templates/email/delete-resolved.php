<?php
defined( 'ABSPATH' ) || exit;

if ( 'html' === $args['email_content_type'] ) :
	do_action( 'gdpr_email_header', esc_html( $args['email_heading'] ) ); 
endif;

if ( ! empty ( $args['email_content'] ) ) {
	$replace_to_arr = array( '{token}' );
	$replace_by_arr = array( esc_html( $args['token'] ) );
	$email_content  = str_replace( $replace_to_arr, $replace_by_arr, $args['email_content'] );

	echo esc_html__( $email_content );

} else {
	echo sprintf(
		/* translators: 6-digit token for audit log */
		esc_html__(
			'Your account has been closed.

			We no longer hold any information about you.
			If you ever need to make a complaint you can email us and we will try to help you.
			To be able to make a complaint you will be requested to provide your email address and the token below.

			%s', 'gdpr'
		),
		esc_html( $args['token'] )
	);
}

if ( 'html' === $args['email_content_type'] ) :
	do_action( 'gdpr_email_footer' );
endif;
