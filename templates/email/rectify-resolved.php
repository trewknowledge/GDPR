<?php
defined( 'ABSPATH' ) || exit;

if ( 'html' === $args['email_content_type'] ) :
	do_action( 'gdpr_email_header', esc_html( $args['email_heading'] ) ); 
endif;

if ( ! empty ( $args['email_content'] ) ) {
	echo esc_html__( $args['email_content'] ); 
} else {
	echo esc_html__(
		'We resolved your rectification request.
		If you have any problems or questions, don\'t hesitate to contact us.', 'gdpr'
	);
}

if ( 'html' === $args['email_content_type'] ) :
	do_action( 'gdpr_email_footer' );
endif;
