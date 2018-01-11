<?php

/**
 * Provides the markup for a text field
 *
 * @link       http://trewknowledge.com
 * @since      1.0.0
 *
 * @package    GDPR
 * @subpackage GDPR/admin/partials
 */


?><input
class="<?php echo esc_attr( $atts['class'] ); ?>"
id="<?php echo esc_attr( $atts['label_for'] ); ?>"
name="<?php echo esc_attr( $atts['name'] ); ?>"
placeholder="<?php echo esc_attr( $atts['placeholder'] ); ?>"
type="<?php echo esc_attr( $atts['type'] ); ?>"
value="<?php echo esc_attr( $atts['value'] ); ?>"
<?php echo ( $atts['required'] ) ? 'required' : '' ?> /><?php

if ( ! empty( $atts['description'] ) ) {

?><span class="description"><?php _e( $atts['description'], 'gdpr' ); ?></span><?php

}
