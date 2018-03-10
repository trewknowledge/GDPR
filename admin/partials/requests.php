<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/admin/partials
 */

?>

<div class="wrap">
	<h1><?php esc_html_e( 'Requests', 'gdpr' ); ?></h1>
	<?php settings_errors(); ?>
	<div class="nav-tab-wrapper">
		<?php foreach ( $tabs as $key => $value ) : ?>
			<a href="<?php echo '#' . $key; ?>" class="nav-tab">
				<?php echo esc_html( $value['name'] ); ?>
				<?php if ( $value['count'] ): ?>
					<span class="gdpr-pending-requests-badge"><?php echo esc_html( $value['count'] ); ?></span>
				<?php endif ?>
			</a>
			<?php $counter++; ?>
		<?php endforeach; ?>
	</div>

	<div class="tab hidden" data-id="access">
		<h2><?php esc_html_e( 'Right to access', 'gdpr' ) ?></h2>
		<div class="postbox">
			<form class="gdpr-manual-email-lookup" method="post" action="">
				<div class="inside">
					<input type="hidden" name="gdpr_action" value="requests_email_lookup">
					<?php wp_nonce_field( 'gdpr-request-email-lookup', 'gdpr_access_email_lookup' ); ?>
					<h4>
						<label for="gdpr-request-email-lookup"><?php esc_html_e( 'Manually add a user', 'gdpr' ); ?></label>
					</h4>
					<input type="email" name="email" class="gdpr-request-email-lookup regular-text" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
					<?php submit_button( 'Submit', 'primary', '', false ); ?>
					<span class="spinner"></span>
				</div>
			</form>
		</div>
	</div>
	<div class="tab hidden" data-id="rectify">
		<h2><?php esc_html_e( 'Rectify Data', 'gdpr' ) ?></h2>
		<div class="postbox">
			<form class="gdpr-manual-email-lookup" method="post" action="">
				<div class="inside">
					<input type="hidden" name="gdpr_action" value="requests_email_lookup">
					<?php wp_nonce_field( 'gdpr-request-email-lookup', 'gdpr_rectify_email_lookup' ); ?>
					<h4>
						<label for="gdpr-request-email-lookup"><?php esc_html_e( 'Manually add a user', 'gdpr' ); ?></label>
					</h4>
					<input type="email" name="email" class="gdpr-request-email-lookup regular-text" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
					<?php submit_button( 'Submit', 'primary', '', false ); ?>
					<span class="spinner"></span>
				</div>
			</form>
		</div>
		<table class="widefat gdpr-request-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Email', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Date of Request', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Information', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Actions', 'gdpr' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( isset( $rectify ) && ! empty( $rectify ) ): ?>
					<?php foreach ( $rectify as $request ): ?>
						<tr>
							<td class="row-title"><?php echo esc_html( $request['email'] ); ?></td>
							<td class="text-center"><?php echo esc_html( $request['date'] ); ?></td>
							<td class="text-center"><?php echo esc_html( $request['data'] ); ?></td>
							<td class="text-center">
								<button class="button button-primary"><?php esc_html_e( 'Mark as Resolved', 'gdpr' ); ?></button>
							</td>
						</tr>
					<?php endforeach ?>
				<?php else: ?>
					<tr>
						<td colspan="4" class="text-center">
							<?php esc_html_e( 'No pending requests', 'gdpr' ); ?>
						</td>
					</tr>
				<?php endif ?>
			</tbody>
			<tfoot>
				<tr>
					<th><?php esc_html_e( 'Email', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Date of Request', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Information', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Actions', 'gdpr' ); ?></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="tab hidden" data-id="portability">
		<h2><?php esc_html_e( 'Portability', 'gdpr' ) ?></h2>
		<div class="postbox">
			<form class="gdpr-manual-email-lookup" method="post" action="">
				<div class="inside">
					<input type="hidden" name="gdpr_action" value="requests_email_lookup">
					<?php wp_nonce_field( 'gdpr-request-email-lookup', 'gdpr_portability_email_lookup' ); ?>
					<h4>
						<label for="gdpr-request-email-lookup"><?php esc_html_e( 'Manually add a user', 'gdpr' ); ?></label>
					</h4>
					<input type="email" name="email" class="gdpr-request-email-lookup regular-text" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
					<?php submit_button( 'Submit', 'primary', '', false ); ?>
					<span class="spinner"></span>
				</div>
			</form>
		</div>
	</div>

	<div class="tab hidden" data-id="complaint">
		<h2><?php esc_html_e( 'Complaints', 'gdpr' ) ?></h2>
		<div class="postbox">
			<form class="gdpr-manual-email-lookup" method="post" action="">
				<div class="inside">
					<input type="hidden" name="gdpr_action" value="requests_email_lookup">
					<?php wp_nonce_field( 'gdpr-request-email-lookup', 'gdpr_complaint_email_lookup' ); ?>
					<h4>
						<label for="gdpr-request-email-lookup"><?php esc_html_e( 'Manually add a user', 'gdpr' ); ?></label>
					</h4>
					<input type="email" name="email" class="gdpr-request-email-lookup regular-text" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
					<?php submit_button( 'Submit', 'primary', '', false ); ?>
					<span class="spinner"></span>
				</div>
			</form>
		</div>
		<table class="widefat gdpr-request-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Email', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Date of Complaint', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Information', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Actions', 'gdpr' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( isset( $complaint ) && ! empty( $complaint ) ): ?>
					<?php foreach ( $complaint as $request ): ?>
						<tr>
							<td class="row-title"><?php echo esc_html( $request['email'] ); ?></td>
							<td class="text-center"><?php echo esc_html( $request['date'] ); ?></td>
							<td class="text-center"><?php echo esc_html( $request['data'] ); ?></td>
							<td class="text-center">
								<button class="button button-primary"><?php esc_html_e( 'Mark as Resolved', 'gdpr' ); ?></button>
							</td>
						</tr>
					<?php endforeach ?>
				<?php else: ?>
					<tr>
						<td colspan="4" class="text-center">
							<?php esc_html_e( 'No pending requests', 'gdpr' ); ?>
						</td>
					</tr>
				<?php endif ?>
			</tbody>
			<tfoot>
				<tr>
					<th><?php esc_html_e( 'Email', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Date of Complaint', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Information', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Actions', 'gdpr' ); ?></th>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="tab hidden" data-id="delete">
		<h2><?php esc_html_e( 'Right to erasure', 'gdpr' ) ?></h2>
		<div class="postbox">
			<form class="gdpr-manual-email-lookup" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
				<div class="inside">
					<input type="hidden" name="action" value="add_to_deletion_requests">
					<?php wp_nonce_field( 'gdpr-request-email-lookup', 'gdpr_delete_email_lookup' ); ?>
					<h4>
						<label for="gdpr-request-email-lookup"><?php esc_html_e( 'Manually add a user', 'gdpr' ); ?></label>
					</h4>
					<input type="email" name="user_email" class="gdpr-request-email-lookup regular-text" placeholder="<?php esc_attr_e( 'email@domain.com', 'gdpr' ); ?>" required>
					<?php submit_button( esc_html__( 'Submit', 'gdpr' ), 'primary', '', false ); ?>
				</div>
			</form>
		</div>
		<table class="widefat gdpr-request-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Email', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Date of Request', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Review', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Actions', 'gdpr' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( isset( $delete ) && ! empty( $delete ) ): ?>
					<?php $index = 0; ?>
					<?php foreach ( $delete as $request ): ?>
						<?php $user = get_user_by( 'email', $request['email'] ) ?>
						<tr class="<?php echo ( $index % 2 == 0 ? '' : 'alternate' ); ?>">
							<td class="row-title"><?php echo esc_html( $request['email'] ); ?></td>
							<td class="text-center"><?php echo esc_html( $request['date'] ); ?></td>
							<td class="text-center">
								<?php
								if ( $this->user_has_content( $user ) ) {
									echo '<button class="button gdpr-review" data-index="' . esc_attr( $index ) . '">' . esc_html__( 'Review', 'gdpr' ) . '</button>';
								} else {
									esc_html_e( 'No content to review', 'gdpr' );
								}
								?>
								<?php if ( $this->user_has_content( $user ) ): ?>
								<?php else: ?>
									<?php  ?>
								<?php endif; ?>
							</td>
							<td class="text-center">
								<form class="frm-process-user-deletion" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
									<?php wp_nonce_field( 'gdpr-request-delete-user', 'gdpr_delete_remove_user' ); ?>
									<input type="hidden" name="action" value="remove_from_deletion_requests">
									<input type="hidden" name="user_email" value="<?php echo esc_attr( $request['email'] ) ?>">
									<?php submit_button( esc_html__( 'Cancel Request', 'gdpr' ), 'delete', '', false ) ?>
								</form>
								<form class="frm-process-user-deletion" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
									<?php wp_nonce_field( 'gdpr-request-delete-user', 'gdpr_delete_user' ); ?>
									<input type="hidden" name="action" value="delete_user">
									<input type="hidden" name="user_email" value="<?php echo esc_attr( $request['email'] ) ?>">
									<?php submit_button( esc_html__( 'Delete User', 'gdpr' ), 'primary', '', false ) ?>
								</form>
							</td>
						</tr>
						<?php if ( $this->user_has_content( $user ) ): ?>
							<tr class="review" data-index="<?php echo esc_attr( $index ); ?>">
								<td colspan="4">
									<div class="hidden">
										<table class="widefat">
											<thead>
												<tr>
													<th><?php esc_html_e( 'Content Type', 'gdpr' ); ?></th>
													<th class="text-center"><?php _e( 'Count', 'gdpr' ); ?></th>
													<th class="text-center"><?php _e( 'Review', 'gdpr' ); ?></th>
													<th class="text-center"><?php _e( 'Reassign', 'gdpr' ); ?></th>
													<th class="text-center"><?php _e( 'Action', 'gdpr' ); ?></th>
												</tr>
											</thead>
											<tbody>
												<?php $post_types = get_post_types( array( 'public' => true ), 'objects' ); ?>
												<?php foreach ( $post_types as $pt ): ?>
													<?php
														$uid = get_user_by( 'email', $request['email'] );
														if ( $uid && is_a( $uid, 'WP_User' ) ) {
															$uid = $uid->ID;
														}
														$count = count_user_posts( $uid, $pt->name );
														if ( '0' === $count) {
															continue;
														}
													?>
													<tr>
														<td class="row-title"><?php echo esc_attr( $pt->label ) ?></td>
														<td class="text-center"><?php echo esc_attr( $count ) ?></td>
														<td class="text-center">
															<a href="<?php echo admin_url('edit.php?post_type=' . $pt->name . '&author=' . $uid); ?>" target="_blank" class="button"><?php echo esc_html( $pt->labels->view_items ); ?></a>
														</td>
														<td class="text-center">
															<select name="reassign" class="gdpr-reassign">
																<option value="0"></option>
																<?php $admins = get_users( array( 'role' => 'administrator' ) ); ?>
																<?php foreach ( $admins as $admin ): ?>
																	<option value="<?php echo esc_attr( $admin->ID ) ?>"><?php echo esc_html( $admin->display_name ) ?></option>
																<?php endforeach; ?>
															</select>
														</td>
														<td class="text-center">
															<button class="button button-primary" disabled><?php esc_html_e( 'Reassign', 'gdpr' ); ?></button>
															<span class="spinner"></span>
														</td>
													</tr>
												<?php endforeach; ?>
												<?php
													$comment_count = get_comments( array(
														'author_email' => $request['email'],
														'include_unapproved' => true,
														'count' => true,
													) );

													if ( $comment_count ) {
														?>
															<tr>
																<td class="row-title"><?php esc_html_e( 'Comments', 'gdpr' ); ?></td>
																<td class="text-center"><?php echo esc_html( $comment_count ); ?></td>
																<td class="text-center"><a href="<?php echo admin_url( 'edit-comments.php?comment_status=all&s=' . urlencode( $request['email'] ) ); ?>" target="_blank" class="button"><?php _e( 'View Comments', 'gdpr' ); ?></a></td>
																<td></td>
																<td class="text-center">
																	<form method="post" class="gdpr-form-process-request">
																		<?php wp_nonce_field( 'gdpr-anonymize-comments-action', '_anonymize-nonce' ) ?>
																		<button class="button-primary gdpr-anonymize-button"><?php _e( 'Anonymize', 'gdpr' ); ?></button>
																		<span class="spinner"></span>
																		<p class="hidden"><strong><?php _e('Resolved', 'gdpr'); ?></strong></p>
																	</form>
																</td>
															</tr>
														<?php
													}
												?>
											</tbody>
											<tr>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						<?php endif ?>
						<?php $index++; ?>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="4" class="text-center">
							<?php esc_html_e( 'No pending requests', 'gdpr' ); ?>
						</td>
					</tr>
				<?php endif ?>
			</tbody>
			<tfoot>
				<tr>
					<th><?php esc_html_e( 'Email', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Date of Request', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Review', 'gdpr' ); ?></th>
					<th class="text-center"><?php esc_html_e( 'Actions', 'gdpr' ); ?></th>
				</tr>
			</tfoot>
		</table>
	</div>

<!-- #poststuff -->
</div>
