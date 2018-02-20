<?php

/**
 * This file is used to markup the cookie preferences window.
 *
 *
 * @link       https://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/public/partials
 */
?>

<div class="gdpr cookie-preferences">
	<div class="gdpr overlay"></div>
	<div class="wrapper">
		<header>
			<div class="logo">
				<?php the_custom_logo(); ?>
			</div>
			<div class="box-title">
				<h3><?php esc_html_e( 'Privacy Preference Center', 'gdpr' ); ?></h3>
			</div>
		</header>
		<div class="content">
			<ul class="tabs">
				<?php foreach ( $tabs as $tab ) : ?>
				<li><button type="button"><?php echo esc_html( $tab['name'] ); ?></button></li>
				<?php endforeach; ?>
			</ul>
			<div class="tab-content">
					<!-- Add the content of the tabs here -->
			</div>
		</div>
	</div>
</div>
