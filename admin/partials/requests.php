<?php
	/**
	 * Provide a admin area view for the plugin
	 *
	 * This file is used to markup the admin-facing aspects of the plugin.
	 *
	 * @link       http://trewknowledge.com
	 * @since      1.0.0
	 *
	 * @package    GDPR
	 * @subpackage GDPR/admin/partials
	 */

	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$requests = get_option( 'gdpr_requests' ) ? get_option( 'gdpr_requests' ) : array();
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<?php	settings_errors( 'gdpr_messages' ); ?>
				<div class="postbox">
					<h2><?php echo _e( 'Email Lookup', 'gdpr' ); ?></h2>
					<div class="inside">
						<form class="gdpr-manual-email-lookup" method="post" action="">
							<input type="hidden" name="gdpr_action" value="email_lookup">
							<?php wp_nonce_field( 'gdpr-request-email-lookup', '_gdpr_email_lookup' ); ?>
							<input type="email" name="email" class="regular-text" id="gdpr-email-lookup-field" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
							<span class="spinner"></span>
							<?php submit_button( 'Submit', 'primary', '', false ); ?>
						</form>
					</div>
				</div>
				<div class="postbox">
					<table class="widefat" id="gdpr-requests-table">
						<thead>
							<tr>
								<th class="text-center"><?php esc_attr_e( 'ID', 'gdpr' ); ?></th>
								<th><?php esc_attr_e( 'Full Name', 'gdpr' ); ?></th>
								<th><?php esc_attr_e( 'Email', 'gdpr' ); ?></th>
								<th class="text-center"><?php esc_attr_e( 'Date of Request', 'gdpr' ); ?></th>
								<th class="text-center"><?php esc_attr_e( 'Time Left', 'gdpr' ); ?></th>
								<th class="text-center"><?php esc_attr_e( 'Review', 'gdpr' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($requests as $ID => $user ): ?>
								<?php
									$diff = GDPR::get_days_left( $user['requested_on'], 30 );
									$class = '';
									if ( $diff <= 7 ) {
										$class = 'gdpr-urgent';
									} else if ( $diff <= 15 ) {
										$class = 'gdpr-warning';
									}
								?>
								<tr class="<?php echo esc_attr( $class ) ?>" data-uid="<?php echo esc_attr( $ID ) ?>">
									<td class="text-center"><?php echo esc_html( $ID ) ?></td>
									<td><?php echo esc_html( $user['full_name'] ); ?></td>
									<td><?php echo esc_html( $user['email'] ); ?></td>
									<td class="text-center"><?php echo esc_html( $user['requested_on'] ); ?></td>
									<td class="text-center"><?php echo sprintf( esc_html__( '%s days', 'gdpr' ), $diff ) ?></td>
									<td class="text-center">
										<div data-uid="<?php echo esc_attr( $ID ); ?>">
											<?php wp_nonce_field( 'gdpr-process-request-delete-action', '_delete-without-review-nonce' ) ?>
											<button class="button gdpr-review"><?php _e( 'Review', 'gdpr' ); ?></button>
											<button class="button-primary gdpr-request-delete"><?php _e( 'Delete', 'gdpr' ); ?></button>
											<span class="spinner"></span>
										</div>
									</td>
								</tr>
								<tr data-uid="<?php echo esc_attr( $ID ) ?>">
									<td colspan="7">
										<div class="gdpr-review-table-<?php echo esc_attr( $ID ); ?>">
											<table class="widefat">
												<thead>
													<tr>
														<th><?php _e( 'Post Type', 'gdpr' ); ?></th>
														<th class="text-center"><?php _e( 'Count', 'gdpr' ); ?></th>
														<th class="text-center"><?php _e( 'Review', 'gdpr' ); ?></th>
														<th class="text-center"><?php _e( 'Reassign to', 'gdpr' ); ?></th>
														<th class="text-center"><?php _e( 'Actions', 'gdpr' ); ?></th>
													</tr>
												</thead>
												<tbody>
													<?php $post_types = get_post_types( array( 'public' => true ) ); ?>
													<?php foreach ( $post_types as $pt ): ?>
														<?php
															$count = count_user_posts( $ID, $pt );
															if ( '0' === $count) {
																continue;
															}
														?>
														<tr data-pt="<?php echo esc_attr( $pt ) ?>" data-uid="<?php echo esc_attr( $ID ) ?>">
															<td class="row-title"><?php echo esc_html( $pt ); ?></td>
															<td class="text-center"><?php echo esc_html( $count ); ?></td>
															<td class="text-center"><a href="<?php echo admin_url('edit.php?post_type=' . $pt . '&author=' . $ID); ?>" target="_blank" class="button"><?php _e( 'Review', 'gdpr' ); ?></a></td>
															<td class="text-center">
																<select name="reassign_to" class="gdpr-reassign" data-pt="<?php echo esc_attr( $pt ) ?>" data-uid="<?php echo esc_attr( $ID ); ?>">
																	<option value=""></option>
																	<?php $admins = get_users( array( 'role' => 'administrator' ) ); ?>
																	<?php foreach ( $admins as $admin ): ?>
																		<option value="<?php echo esc_attr( $admin->ID ) ?>"><?php echo esc_html( $admin->display_name ) ?></option>
																	<?php endforeach; ?>
																</select>
															</td>
															<td class="text-center">
																<form method="post" class="gdpr-form-process-request" data-uid="<?php echo esc_attr( $ID ) ?>" data-pt="<?php echo esc_attr( $pt ); ?>" data-reassign="" data-count="<?php echo esc_attr( $count ) ?>">
																	<?php wp_nonce_field( 'gdpr-process-request-reassign-action', '_reassign-nonce' ) ?>
																	<?php wp_nonce_field( 'gdpr-process-request-delete-action', '_delete-nonce' ) ?>
																	<button class="button-primary gdpr-reassign-button" data-pt="<?php echo esc_attr( $pt ) ?>" data-uid="<?php echo esc_attr( $ID ); ?>"><?php _e( 'Reassign', 'gdpr' ); ?></button>
																	<span class="spinner"></span>
																	<p style="display:none;"><strong><?php _e('Resolved', 'gdpr'); ?></strong></p>
																</form>
															</td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="postbox">
					<h2>About the plugin</h2>
					<div class="inside">
						Lorem ipsum, dolor sit amet consectetur adipisicing elit. Aspernatur eius sit itaque vitae molestiae nostrum rerum tenetur ex veniam incidunt laudantium, in earum odio, maxime molestias. Id, libero. Saepe, ipsum.
					</div>
				</div>
				<div class="postbox">
					<h2>Resources & Reference</h2>
					<div class="inside">
						<ul>
							<li><a href="#">What is GDPR?</a></li>
							<li><a href="#">Why should I care?</a></li>
							<li><a href="#">How can I be compliant?</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
