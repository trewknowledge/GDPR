<?php
defined( 'ABSPATH' ) || exit;

if ( 'html' === $args['email_content_type'] ) :
	do_action( 'gdpr_email_header', esc_html( $args['email_heading'] ) ); 
endif;

if ( ! empty ( $args['email_content'] ) ) {
	$replace_to_arr = array( '{content}', '{nature}', '{office_contact}', '{consequences}', '{measures}', '{confirm_url}' );
	$replace_by_arr = array( esc_html( $args['content'] ), esc_html( $args['nature'] ), esc_html( $args['office_contact'] ), esc_html( $args['consequences'] ), esc_html( $args['measures'] ), esc_url_raw( $args['confirm_url'] ) );
	$email_content  = str_replace( $replace_to_arr, $replace_by_arr, $args['email_content'] );

	echo esc_html__( $email_content );

} else {
	echo sprintf(
	/* translators: 1: Email content, 2: Nature of data breach, 3: Contact details for data protection officer, 4: Likely consequences of breach, 5: Measures taken */
	esc_html__(
		'%1$s

			--------------------------------------------------------
			Nature of the personal data breach:
			--------------------------------------------------------
			%2$s

			--------------------------------------------------------
			Name and contact details of the data protection officer:
			--------------------------------------------------------
			%3$s

			--------------------------------------------------------
			Likely consequences of the personal data breach:
			--------------------------------------------------------
			%4$s

			--------------------------------------------------------
			Measures taken or proposed to be taken:
			--------------------------------------------------------
			%5$s
			', 'gdpr'
				),
				esc_html( $args['content'] ),
				esc_html( $args['nature'] ),
				esc_html( $args['office_contact'] ),
				esc_html( $args['consequences'] ),
				esc_html( $args['measures'] ),
				esc_url_raw( $args['confirm_url'] )
	);
}
if ( 'html' === $args['email_content_type'] ) :
	do_action( 'gdpr_email_footer' );
endif;
