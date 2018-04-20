<?php

echo sprintf(
/* translators: 6-digit token for audit log */
    esc_html__(
        'User account %s has been closed.

Data about account:
Token: %s', 'gdpr' ),
   $args['user'], $args['token']
);
