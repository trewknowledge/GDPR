<?php
defined( 'ABSPATH' ) || exit;

if ( 'html' === $args['email_content_type'] ) : 
	do_action( 'gdpr_email_header', esc_html( $args['email_heading'] ) ); 
endif;

if ( ! empty ( $args['email_content'] ) ) {
	$replace_to_arr = array( '{type}', '{review_url}' );
	$replace_by_arr = array( esc_html( $args['type'] ), esc_url_raw( $args['review_url'] ) );
	$email_content  = str_replace( $replace_to_arr, $replace_by_arr, $args['email_content'] );

	echo esc_html__( $email_content );

} else {
	echo sprintf(
		/* translators: 1: The type of request. 2: Link to where the request can be reviewed. */
		esc_html__(
			'There is a new %1$s request waiting for review.

			Review your requests: %2$s', 'gdpr'
		),
		esc_html( $args['type'] ),
		esc_url_raw( $args['review_url'] )
	);
}

if ( 'html' === $args['email_content_type'] ) :
	do_action( 'gdpr_email_footer' );
endif;
