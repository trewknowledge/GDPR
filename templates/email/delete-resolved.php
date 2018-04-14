<?php

echo sprintf(
	/* translators: 6-digit token for audit log */
	esc_html__(
		'Your account has been closed.

We no longer hold any information about you.
If you ever need to make a complaint you can email us and we will try to help you.
To be able to make a complaint you will be requested to provide your email address and the token below.

%1$s', 'gdpr' ),
	esc_html( $args['token'] )
);
