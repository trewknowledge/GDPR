<?php

echo sprintf(
	/* translators: 1: The type of request. 2: Link to where the request can be reviewed. */
  esc_html__(
'There is a new %1$s request waiting for review.

Review your requests: %2$s', 'gdpr' ),
  esc_html( $args['type'] ),
  esc_url_raw( $args['review_url'] )
);
