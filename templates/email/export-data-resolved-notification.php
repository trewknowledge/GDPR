<?php

echo sprintf(
/* translators: 6-digit token for audit log */
    esc_html__(
        'User account  %s has exported their data using the Download My Data GDPR functionality.
', 'gdpr' ),
    $args['user']
);
