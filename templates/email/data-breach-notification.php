<?php
echo sprintf(
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


To confirm this request, click here: %s

---------------------------------------------------------------------------------
If that is not intended, have the person who requested it change their password.
---------------------------------------------------------------------------------
', 'gdpr' ),
	esc_html( $args['content'] ),
	esc_html( $args['nature'] ),
	esc_html( $args['office_contact'] ),
	esc_html( $args['consequences'] ),
	esc_html( $args['measures'] ),
	esc_url_raw( $args['confirm_url'] )
);
?>
