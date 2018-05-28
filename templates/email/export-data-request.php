<?php
echo sprintf(
	/* translators: 1: XML download link, 2: JSON download link, 3: reset password link */
	esc_html__(
'Someone requested to download your data from our site.
By clicking confirm we will redirect you back to our site where a download will begin.

To download it in a XML format, click here: %1$s
To download it in a JSON format, click here: %2$s



---------------------------------------------------------------------------------
If that wasn\'t you, reset your password: %3$s
', 'gdpr' ),
	esc_url_raw( $args['confirm_url_xml'] ),
	esc_url_raw( $args['confirm_url_json'] ),
	esc_url_raw( $args['forgot_password_url'] )
);
