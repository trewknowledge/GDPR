<?php
echo sprintf(
  /* translators: 1: The complaint content, 2: confirmation link, 3: reset password link */
  esc_html__(
'Someone placed a complaint on your behalf on our site.
By clicking confirm a request will be made and we will do our best to fulfil it.

--------------------------------------------------------
Request
--------------------------------------------------------
%s




To confirm this request, click here: %s



---------------------------------------------------------------------------------
If that wasn\'t you, reset your password: %s
', 'gdpr' ),
  esc_html( $args['data'] ),
  esc_url_raw( $args['confirm_url'] ),
  esc_url_raw( $args['forgot_password_url'] )
);
