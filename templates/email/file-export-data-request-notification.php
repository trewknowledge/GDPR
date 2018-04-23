<?php

echo sprintf(
    esc_html__(
        'User account  %s has exported their data using the Download My Data GDPR functionality.
', 'gdpr' ),
    $args['user']
);
