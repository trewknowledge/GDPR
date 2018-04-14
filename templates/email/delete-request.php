<?php

$confirm_url = add_query_arg(
	array(
		'type'  => 'delete',
		'key'   => $args['key'],
		'email' => $args['user']->user_email,
	),
	home_url()
);

$forgot_password_url = add_query_arg(
	array(
		'action' => 'rp',
		'key'    => get_password_reset_key( $args['user'] ),
		'login'  => $args['user']->user_login,
	),
	wp_login_url()
);

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
', 'gdpr' ),
	esc_url_raw( $confirm_url ),
	esc_url_raw( $forgot_password_url )
);
