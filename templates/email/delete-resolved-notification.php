<?php

echo sprintf(
/* translators: 6-digit token for audit log */
    esc_html__(
        ' Some user account has been closed.

', 'gdpr' ),
   $args['user'], $args['token']
);
