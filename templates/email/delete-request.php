<?php
echo sprintf(
  /* translators: 1: Confirmation link, 2: Reset password link */
  esc_html__(
'Someone placed a request for your information to be removed from our site.
By clicking confirm your account will be removed from our site and all data we collected
over time will be erased from our database. It will be impossible for us to retrieve that
information in the future.



To confirm this request, click here: %s



---------------------------------------------------------------------------------
If that wasn\'t you, reset your password: %s
', 'gdpr' ),
  esc_url_raw( $args['confirm_url'] ),
  esc_url_raw( $args['forgot_password_url'] )
);
