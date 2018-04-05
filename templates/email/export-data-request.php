<?php
  $confirm_url_xml = add_query_arg(
    array(
      'type' => 'export-data',
      'key' => $args['key'],
      'email' => $args['user']->user_email,
      'format' => 'xml',
    ),
    home_url()
  );
  $confirm_url_json = add_query_arg(
    array(
      'type' => 'export-data',
      'key' => $args['key'],
      'email' => $args['user']->user_email,
      'format' => 'json',
    ),
    home_url()
  );
  $forgot_password_url = add_query_arg(
    array(
      'action' => 'rp',
      'key' => get_password_reset_key( $args['user'] ),
      'login' => $args['user']->user_login,
    ),
    wp_login_url()
  );

echo sprintf(
  /* translators: 1: XML download link, 2: JSON download link, 3: reset password link */
  esc_html__(
'Someone requested to download your data from our site.
By clicking confirm we will redirect you back to our site where a download will begin.

To download it in a XML format, click here: %s
To download it in a JSON format, click here: %s



---------------------------------------------------------------------------------
If that wasn\'t you, reset your password: %s
', 'gdpr' ),
  esc_url_raw( $confirm_url_xml ),
  esc_url_raw( $confirm_url_json ),
  esc_url_raw( $forgot_password_url )
);
