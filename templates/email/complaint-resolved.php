<?php
defined( 'ABSPATH' ) || exit;
do_action( 'gdpr_email_header', esc_html( $args['email_heading'] ) ); 

if ( ! empty ( $args['email_content'] ) ) {
	echo esc_html__( $args['email_content'] ); 
} else {
	echo esc_html__(
		'We resolved your complaint request.
		If you have any problems or questions, don\'t hesitate to contact us.', 'gdpr'
	);
}

do_action( 'gdpr_email_footer' );
