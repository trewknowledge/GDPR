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
 * @subpackage admin/partials
 */

?>

<div class="wrap gdpr">
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
		<?php endforeach; ?>
	</div>

	<div class="gdpr-tab hidden" data-id="rectify">
		<h2><?php esc_html_e( 'Rectify Data', 'gdpr' ) ?></h2>
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
					<?php foreach ( $rectify as $i => $request ): ?>
						<tr>
							<td class="row-title"><?php echo esc_html( $request['email'] ); ?></td>
							<td class="text-center"><?php echo esc_html( $request['date'] ); ?></td>
							<td class="text-center"><?php echo wp_kses( wpautop( wp_unslash( $request['data'] ) ), array( 'p' => true, 'br' => true ) ); ?></td>
							<td class="text-center">
								<form class="frm-process-rectification" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
									<?php wp_nonce_field( 'gdpr-request-nonce', 'gdpr_cancel_rectify_nonce' ); ?>
									<input type="hidden" name="action" value="gdpr_cancel_request">
									<input type="hidden" name="type" value="rectify">
									<input type="hidden" name="index" value="<?php echo esc_attr( $i ); ?>">
									<input type="hidden" name="user_email" value="<?php echo esc_attr( $request['email'] ) ?>">
									<?php submit_button( esc_html__( 'Cancel Request', 'gdpr' ), 'delete', '', false ) ?>
								</form>
								<form class="frm-process-rectification" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
									<?php wp_nonce_field( 'gdpr-mark-as-resolved', 'gdpr_rectify_mark_resolved_nonce' ); ?>
									<input type="hidden" name="action" value="gdpr_mark_resolved">
									<input type="hidden" name="type" value="rectify">
									<input type="hidden" name="index" value="<?php echo esc_attr( $i ); ?>">
									<input type="hidden" name="user_email" value="<?php echo esc_attr( $request['email'] ) ?>">
									<?php submit_button( esc_html__( 'Mark as Resolved', 'gdpr' ), 'primary', '', false ) ?>
								</form>
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

	<div class="gdpr-tab hidden" data-id="complaint">
		<h2><?php esc_html_e( 'Complaints', 'gdpr' ) ?></h2>
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
					<?php foreach ( $complaint as $i => $request ): ?>
						<tr>
							<td class="row-title"><?php echo esc_html( $request['email'] ); ?></td>
							<td class="text-center"><?php echo esc_html( $request['date'] ); ?></td>
							<td class="text-center"><?php echo esc_html( wp_unslash( $request['data'] ) ); ?></td>
							<td class="text-center">
								<form class="frm-process-complaint" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
									<?php wp_nonce_field( 'gdpr-request-nonce', 'gdpr_cancel_complaint_nonce' ); ?>
									<input type="hidden" name="action" value="gdpr_cancel_request">
									<input type="hidden" name="type" value="complaint">
									<input type="hidden" name="index" value="<?php echo esc_attr( $i ); ?>">
									<input type="hidden" name="user_email" value="<?php echo esc_attr( $request['email'] ) ?>">
									<?php submit_button( esc_html__( 'Cancel Request', 'gdpr' ), 'delete', '', false ) ?>
								</form>
								<form class="frm-process-complaint" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
									<?php wp_nonce_field( 'gdpr-mark-as-resolved', 'gdpr_complaint_mark_resolved_nonce' ); ?>
									<input type="hidden" name="action" value="gdpr_mark_resolved">
									<input type="hidden" name="type" value="complaint">
									<input type="hidden" name="index" value="<?php echo esc_attr( $i ); ?>">
									<input type="hidden" name="user_email" value="<?php echo esc_attr( $request['email'] ) ?>">
									<?php submit_button( esc_html__( 'Mark as Resolved', 'gdpr' ), 'primary', '', false ) ?>
								</form>
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

	<div class="gdpr-tab hidden" data-id="delete">
		<h2><?php esc_html_e( 'Right to erasure', 'gdpr' ) ?></h2>
		<div class="postbox not-full">
			<form class="gdpr-manual-email-lookup" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
				<div class="inside">
					<input type="hidden" name="action" value="gdpr_add_to_deletion_requests">
					<?php wp_nonce_field( 'gdpr-add-to-deletion-requests', 'gdpr_deletion_requests_nonce' ); ?>
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
					<?php foreach ( $delete as $i => $request ): ?>
						<?php $user = get_user_by( 'email', $request['email'] ) ?>
						<tr class="<?php echo ( $index % 2 == 0 ? '' : 'alternate' ); ?>">
							<td class="row-title"><?php echo esc_html( $request['email'] ); ?></td>
							<td class="text-center"><?php echo esc_html( $request['date'] ); ?></td>
							<td class="text-center">
								<?php
								if ( GDPR_Requests::user_has_content( $user ) ) {
									echo '<button class="button gdpr-review" data-index="' . esc_attr( $index ) . '">' . esc_html__( 'Review', 'gdpr' ) . '</button>';
								} else {
									esc_html_e( 'No content to review', 'gdpr' );
								}
								?>
								<?php if ( GDPR_Requests::user_has_content( $user ) ): ?>
								<?php else: ?>
									<?php  ?>
								<?php endif; ?>
							</td>
							<td class="text-center">
								<form class="frm-process-user-deletion" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
									<?php wp_nonce_field( 'gdpr-request-nonce', 'gdpr_cancel_delete_nonce' ); ?>
									<input type="hidden" name="action" value="gdpr_cancel_request">
									<input type="hidden" name="type" value="delete">
									<input type="hidden" name="user_email" value="<?php echo esc_attr( $request['email'] ) ?>">
									<input type="hidden" name="index" value="<?php echo esc_attr( $i ); ?>">
									<?php submit_button( esc_html__( 'Cancel Request', 'gdpr' ), 'delete', '', false ) ?>
								</form>
								<form class="frm-process-user-deletion" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
									<?php wp_nonce_field( 'gdpr-request-delete-user', 'gdpr_delete_user' ); ?>
									<input type="hidden" name="action" value="gdpr_delete_user">
									<input type="hidden" name="user_email" value="<?php echo esc_attr( $request['email'] ) ?>">
									<input type="hidden" name="index" value="<?php echo esc_attr( $i ); ?>">
									<?php submit_button( esc_html__( 'Delete User', 'gdpr' ), 'primary', '', false ) ?>
								</form>
							</td>
						</tr>
						<?php if ( GDPR_Requests::user_has_content( $user ) ): ?>
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
														if ( $uid && $uid instanceof WP_User ) {
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
															<form method="post" class="gdpr-reassign-content">
																<?php wp_nonce_field( 'gdpr-reassign-content-action', 'gdpr_reassign_content_nonce' ) ?>
																<input type="hidden" name="user_email" value="<?php echo esc_attr( $request['email'] ) ?>">
																<input type="hidden" name="reassign_to" value="">
																<input type="hidden" name="post_type" value="<?php echo esc_attr( $pt->name ); ?>">
																<input type="hidden" name="post_count" value="<?php echo esc_attr( $count ); ?>">
																<?php submit_button( esc_html__( 'Reassign', 'gdpr' ), 'primary', '', false, array( 'disabled' => true ) ); ?>
																<span class="spinner"></span>
																<p class="hidden"><strong><?php esc_html_e( 'Resolved', 'gdpr' ); ?></strong></p>
															</form>
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
																	<form method="post" class="gdpr-anonymize-comments">
																		<?php wp_nonce_field( 'gdpr-anonymize-comments-action', 'gdpr_anonymize_comments_nonce' ) ?>
																		<input type="hidden" name="user_email" value="<?php echo esc_attr( $request['email'] ) ?>">
																		<input type="hidden" name="comment_count" value="<?php echo esc_attr( $comment_count ) ?>">
																		<?php submit_button( esc_html__( 'Anonymize', 'gdpr' ), 'primary', '', false ) ?>
																		<span class="spinner"></span>
																		<p class="hidden"><strong><?php esc_html_e( 'Resolved', 'gdpr' ); ?></strong></p>
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
