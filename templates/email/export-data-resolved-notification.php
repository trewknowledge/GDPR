<?php

echo sprintf(
/* translators: 6-digit token for audit log */
    esc_html__(
        'User account %s has export data.

', 'gdpr' ),
    $args['user']
);
