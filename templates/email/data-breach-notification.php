<?php
echo sprintf(
	/* translators: 1: Email content, 2: Nature of data breach, 3: Contact details for data protection officer, 4: Likely consequences of breach, 5: Measures taken */
	esc_html__(
'%1$s

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
', 'gdpr' ),
	esc_html( $args['content'] ),
	esc_html( $args['nature'] ),
	esc_html( $args['office_contact'] ),
	esc_html( $args['consequences'] ),
	esc_html( $args['measures'] ),
	esc_url_raw( $args['confirm_url'] )
);
