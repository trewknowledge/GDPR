<div class="wrap gdpr">
	<h1><?php esc_html_e( 'GDPR Email Templates', 'gdpr' ); ?></h1>

	<?php settings_errors(); ?>

	<?php
		if ( ! empty ( $_GET[ 'type' ] ) ) { 
			$email_type = sanitize_text_field( wp_unslash( $_GET[ 'type' ] ) );
		?>
		<h2 id="gdpr_heading"><?php echo ucfirst( str_replace( '_', ' ', $email_type ) ); ?>
			<small class="wc-admin-breadcrumb"><a href="<?php echo admin_url( 'admin.php?page=gdpr-email-templates' ); ?>" aria-label="<?php esc_html_e( 'Return to Email Notifications' ); ?>">⤴</a></small>
		</h2>
		<form method="post" class="gdpr-email-settings" >
			<input type="hidden" name="action" value="gdpr_manage_email_settings">
			<input type="hidden" name="email_type" value="<?php echo esc_attr( $email_type ); ?>">
			<?php wp_nonce_field( 'gdpr_email_settings_action', 'gdpr_email_settings_nonce' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="gdpr_<?php echo $email_type; ?>_email_subject"><?php esc_html_e( 'Subject', 'gdpr' ); ?> </label>
						<span data-gdprtooltip="<?php esc_attr_e( 'Subject line for this email.', 'gdpr' ); ?>">
							<span class="dashicons dashicons-info"></span>
						</span>
					</th>
					<td class="forminp">
						<input class="input-text regular-input " type="text" name="<?php echo $email_type; ?>_email_subject" id="<?php echo $email_type; ?>_email_subject" style="width:400px" value="" placeholder="">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="gdpr_<?php echo $email_type; ?>_email_heading"><?php esc_html_e( 'Email heading', 'gdpr' ); ?> </label>
						<span data-gdprtooltip="<?php esc_attr_e( 'Email heading.', 'gdpr' ); ?>">
							<span class="dashicons dashicons-info"></span>
						</span>
					</th>
					<td class="forminp">
						<input class="input-text regular-input " type="text" name="<?php echo $email_type; ?>_email_heading" id="<?php echo $email_type; ?>_email_heading" style="width:400px" value="" placeholder="">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="gdpr_<?php echo $email_type; ?>_additional_content"><?php esc_html_e( 'Additional Content', 'gdpr' ); ?> </label>
						<span data-gdprtooltip="<?php esc_attr_e( 'Text to appear below the main email content.', 'gdpr' ); ?>">
							<span class="dashicons dashicons-info"></span>
						</span>
					</th>
					<td class="forminp">
						<textarea class="input-text regular-input " name="<?php echo $email_type; ?>_additional_content" id="<?php echo $email_type; ?>_additional_content" style="width:400px; height:75px;" value="" placeholder=""></textarea>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="gdpr_<?php echo $email_type; ?>_email_content_type"><?php esc_html_e( 'Email Type', 'gdpr' ); ?> </label>
						<span data-gdprtooltip="<?php esc_attr_e( 'Email Content type.', 'gdpr' ); ?>">
							<span class="dashicons dashicons-info"></span>
						</span>
					</th>
					<td class="forminp">
						<select name="<?php echo $email_type; ?>_email_content_type">
							<option value="plain"><?php esc_html_e( 'Plain text', 'gdpr' ); ?></option>
							<option value="text/html" selected="selected"><?php esc_html_e( 'HTML', 'gdpr' ); ?></option>
						</select>
					</td>
				</tr>
			</table>
			<?php
				do_action( 'gdpr_extra_settings' );
				submit_button();
			?>
		</form>	
		<?php } else { ?>
		<form action="options.php" method="post" class="gdpr-email-template-form">
			<?php settings_fields( 'gdpr' ); ?>
			<h2 class="title"><?php esc_html_e( 'Email notifications', 'gdpr' ); ?></h2>
			<p><?php esc_html_e( 'Email notifications sent from GDPR are listed below. Click on an email to configure it.', 'gdpr' ); ?></p>
			<table class="form-table gdpr-emails">
				<thead>
					<tr>
						<th class="gdpr-email-settings-table-name"><?php esc_html_e( 'Email', 'gdpr' ); ?></th>
						<th><?php esc_html_e( 'Content type', 'gdpr' ); ?></th>
						<th></th>						
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href="<?php echo esc_url( add_query_arg( 'type', 'new_request', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>" ><?php esc_html_e( 'New Request', 'gdpr' ); ?></a>
						</td>
						<td>text/html</td>
						<td>
							<a class="button alignright show-settings" href="<?php echo esc_url( add_query_arg( 'type', 'new_request', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr'); ?></a>
						</td>
					</tr>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href="<?php echo esc_url( add_query_arg( 'type', 'delete_request', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>" ><?php esc_html_e( 'Delete Request', 'gdpr' ); ?></a>
						</td>
						<td>text/html</td>
						<td><a class="button alignright" href="<?php echo esc_url( add_query_arg( 'type', 'delete_request', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr' ); ?></a></td>
					</tr>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href="<?php echo esc_url( add_query_arg( 'type', 'delete_resolved', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>" >
								<?php esc_html_e( 'Delete Resolved', 'gdpr' ); ?></a>
							</td>
						<td>text/html</td>
						<td>
							<a class="button alignright" href="<?php echo esc_url( add_query_arg( 'type', 'delete_resolved', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr' ); ?></a>
						</td>
					</tr>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href="<?php echo esc_url( add_query_arg( 'type', 'rectify_request', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>" ><?php esc_html_e( 'Rectify Request', 'gdpr' ); ?></a>
						</td>
						<td>text/html</td>
						<td>
							<a class="button alignright" href="<?php echo esc_url( add_query_arg( 'type', 'rectify_request', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr' ); ?></a>
						</td>
					</tr>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href="<?php echo esc_url( add_query_arg( 'type', 'rectify_resolved', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Rectify Resolved', 'gdpr' ); ?></a>
						</td>
						<td>text/html</td>
						<td>
							<a class="button alignright" href="<?php echo esc_url( add_query_arg( 'type', 'rectify_resolved', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr' ); ?></a>
						</td>
					</tr>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href="<?php echo esc_url( add_query_arg( 'type', 'complaint_request', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Complaint Request', 'gdpr' ); ?></a>
						</td>
						<td>text/html</td>
						<td>
							<a class="button alignright" href="<?php echo esc_url( add_query_arg( 'type', 'complaint_request', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr' ); ?></a>
						</td>
					</tr>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href="<?php echo esc_url( add_query_arg( 'type', 'complaint_resolved', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"> <?php esc_html_e( 'Complaint Resolved', 'gdpr' ); ?></a>
						</td>
						<td>text/html</td>
						<td>
							<a class="button alignright" href="<?php echo esc_url( add_query_arg( 'type', 'complaint_resolved', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr' ); ?></a>
						</td>
					</tr>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href="<?php echo esc_url( add_query_arg( 'type', 'export_data_request', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"> <?php esc_html_e( 'Export Data Request', 'gdpr' ); ?></a>
						</td>
						<td>text/html</td>
						<td>
							<a class="button alignright" href="<?php echo esc_url( add_query_arg( 'type', 'export_data_request', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr' ); ?></a>
						</td>
					</tr>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href="<?php echo esc_url( add_query_arg( 'type', 'export_data_resolved', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"> <?php esc_html_e( 'Export Data Resolved', 'gdpr' ); ?></a>
						</td>
						<td>text/html</td>
						<td>
							<a class="button alignright" href="<?php echo esc_url( add_query_arg( 'type', 'export_data_resolved', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr' ); ?></a>
						</td>
					</tr>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href="<?php echo esc_url( add_query_arg( 'type', 'data_breach_resolved', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>" ><?php esc_html_e( 'Data Breach Resolved', 'gdpr' ); ?></a>
						</td>
						<td>text/html</td>
						<td>
							<a class="button alignright" href="<?php echo esc_url( add_query_arg( 'type', 'data_breach_resolved', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr' ); ?></a></td>
					</tr>
					<tr>
						<td class="gdpr-email-settings-table-name">
							<a href ="<?php echo esc_url( add_query_arg( 'type', 'data_breach_notification', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"> <?php esc_html_e( 'Data Breach Notification', 'gdpr' ); ?></a></td>
						<td>text/html</td>
						<td>
							<a class="button alignright" href="<?php echo esc_url( add_query_arg( 'type', 'data_breach_notification', admin_url( 'admin.php?page=gdpr-email-templates' ) ) ); ?>"><?php esc_html_e( 'Manage', 'gdpr' ); ?></a>
						</td>
					</tr>
				</tbody>
			</table>
			<h2 class="title"><?php esc_html_e( 'Email sender options', 'gdpr' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="gdpr_email_form_name"><?php esc_html_e( '"From" name', 'gdpr' ); ?>:</label>
							<span data-gdprtooltip="<?php esc_attr_e( 'How the sender name appears in outgoing GDPR emails.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</th>
						<td>
							<?php $email_from_name = get_option( 'gdpr_email_form_name', '' ); ?>
							<input type="text" name="gdpr_email_form_name" value="<?php echo esc_html( $email_from_name ); ?>" placeholder="" style="min-width:400px;" >
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="gdpr_email_from_address"><?php esc_html_e( '"From" address', 'gdpr' ); ?>:</label>
							<span data-gdprtooltip="<?php esc_attr_e( 'URL to an image you want to show in the email header. Upload image using media uploader ( Admin > Media )', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</th>
						<td>
							<?php $email_from_address = get_option( 'gdpr_email_from_address', '' ); ?>
							<input type="text" name="gdpr_email_from_address" value="<?php echo esc_url( $email_from_address ); ?>" placeholder="" style="min-width:400px;" >
						</td>
					</tr>
				</tbody>
			</table>
			<h2 class="title"><?php esc_html_e( 'Email template', 'gdpr' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="gdpr_complaint_request_email"><?php esc_html_e( 'Header image', 'gdpr' ); ?>:</label>
							<span data-gdprtooltip="<?php esc_attr_e( 'URL to an image you want to show in the email header. Upload image using media uploader ( Admin > Media )', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</th>
						<td>
							<?php $email_header_image_url = get_option( 'gdpr_email_header_image_url', '' ); ?>
							<input type="text" name="gdpr_email_header_image_url" value="<?php echo esc_url( $email_header_image_url ); ?>" placeholder="" style="min-width:400px;" >
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="gdpr_complaint_request_email"><?php esc_html_e( 'Footer Text', 'gdpr' ); ?>:</label>
							<span data-gdprtooltip="<?php esc_attr_e( 'The text to appear to the footer of all GDPR emails. ', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</th>
						<td>
							<?php $email_footer_text = get_option( 'gdpr_email_footer_text', '' ); ?>
							<textarea name="gdpr_email_footer_text" placeholder="" style="width:400px; height: 75px;" ><?php echo esc_html( $email_footer_text ); ?></textarea>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="gdpr_email_base_color"><?php esc_html_e( 'Base color', 'gdpr' ); ?>:</label>
							<span data-gdprtooltip="<?php esc_attr_e( 'The base color for GDPR email template.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</th>
						<td>
							<?php $email_base_color = get_option( 'gdpr_email_base_color', '' ); ?>
							<input type="text" name="gdpr_email_base_color" class="color_pick" value="<?php echo esc_url( $email_base_color ); ?>" placeholder="" >
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="gdpr_email_background_color"><?php esc_html_e( 'Background color', 'gdpr' ); ?>:</label>
							<span data-gdprtooltip="<?php esc_attr_e( 'The background color for GDPR email template.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</th>
						<td>
							<?php $email_background_color = get_option( 'gdpr_email_background_color', '' ); ?>
							<input type="text" name="gdpr_email_background_color" class="color_pick" value="<?php echo esc_url( $email_background_color ); ?>" placeholder="" >
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="gdpr_email_body_background_color"><?php esc_html_e( 'Body background color', 'gdpr' ); ?>:</label>
							<span data-gdprtooltip="<?php esc_attr_e( 'The body background color for GDPR email template.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</th>
						<td>
							<?php $email_body_background_color = get_option( 'gdpr_email_body_background_color', '' ); ?>
							<input type="text" name="gdpr_email_body_background_color" class="color_pick" value="<?php echo esc_url( $email_body_background_color ); ?>" placeholder="" >
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="gdpr_email_body_text_color"><?php esc_html_e( 'Body text color', 'gdpr' ); ?>:</label>
							<span data-gdprtooltip="<?php esc_attr_e( 'The body text color for GDPR email template.', 'gdpr' ); ?>">
								<span class="dashicons dashicons-info"></span>
							</span>
						</th>
						<td>
							<?php $email_body_text_color = get_option( 'gdpr_email_body_text_color', '' ); ?>
							<input type="text" name="gdpr_email_body_text_color" class="color_pick" value="<?php echo esc_url( $email_body_text_color ); ?>" placeholder="" >
						</td>
					</tr>
				</tbody>
			</table>

			<?php
			do_action( 'gdpr_extra_settings' );
			submit_button();
			?>
		</form>
	<?php	} ?>

<!-- #poststuff -->
</div>
