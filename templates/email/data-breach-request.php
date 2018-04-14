<?php

echo sprintf(
	/* translators: 1: User who requested the notification, 2: Nature of data breach, 3: Contact details for data protection officer, 4: Likely consequences of breach, 5: Measures taken, 6: Confirmation link */
	esc_html__(
		'A request to send a mass email notification to all users regarding a data breach has been made by %1$s.

--------------------------------------------------------
Nature of the personal data breach:
--------------------------------------------------------
%2$s

--------------------------------------------------------
Name and contact details of the data protection officer:
--------------------------------------------------------
%3$s

--------------------------------------------------------
Likely consequences of the personal data breach:
--------------------------------------------------------
%4$s

--------------------------------------------------------
Measures taken or proposed to be taken:
--------------------------------------------------------
%5$s


To confirm this request, click here: %6$s

---------------------------------------------------------------------------------
If that is not intended, have the person who requested it change their password.
---------------------------------------------------------------------------------
', 'gdpr' ),
	esc_html( $args['requester'] ),
	esc_html( $args['nature'] ),
	esc_html( $args['office_contact'] ),
	esc_html( $args['consequences'] ),
	esc_html( $args['measures'] ),
	esc_url_raw( $args['confirm_url'] )
);
