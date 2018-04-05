<?php
echo sprintf(
	/* translators: 1: Email content, 2: Nature of data breach, 3: Contact details for data protection officer, 4: Likely consequences of breach, 5: Measures taken */
	esc_html__(
'%s

--------------------------------------------------------
Nature of the personal data breach:
--------------------------------------------------------
%s

--------------------------------------------------------
Name and contact details of the data protection officer:
--------------------------------------------------------
%s

--------------------------------------------------------
Likely consequences of the personal data breach:
--------------------------------------------------------
%s

--------------------------------------------------------
Measures taken or proposed to be taken:
--------------------------------------------------------
%s
', 'gdpr' ),
	esc_html( $args['content'] ),
	esc_html( $args['nature'] ),
	esc_html( $args['office_contact'] ),
	esc_html( $args['consequences'] ),
	esc_html( $args['measures'] ),
	esc_url_raw( $args['confirm_url'] )
);
?>
