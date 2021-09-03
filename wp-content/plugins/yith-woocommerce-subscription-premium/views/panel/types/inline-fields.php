<?php
/**
 * Inline fields Framework Field Template.
 *
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

extract( $field );
$value = maybe_unserialize( $value );
if ( ! empty( $fields ) && is_array( $fields ) ) { ?>
	<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo isset( $class ) ? $class : ''; ?> yith-inline-fields" <?php echo $custom_attributes; ?>>
		<?php
		foreach ( $fields as $key => $field ) {
			$allowed_types  = array( 'select', 'select-buttons', 'number', 'text', 'slider', 'hidden', 'html' );
			$default_args   = array( 'type' => 'select' );
			$std            = isset( $field['std'] ) ? $field['std'] : '';
			$field['value'] = isset( $value[ $key ] ) ? maybe_unserialize( $value[ $key ] ) : $std;
			$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
			$field['id']    = $id . '_' . $key;
			$field['name']  = $name . '[' . $key . ']';

			if ( ! in_array( $field['type'], $allowed_types, true ) ) {
				continue;
			}

			if ( in_array( $field['type'], array( 'select', 'select-buttons' ), true ) && ( isset($field['select2']) && true === $field['select2'] ) ) {
				$field['class'] .= ' wc-enhanced-select';
			}
			?>
			<?php if ( isset( $field['inline-label'] ) && '' !== $field['inline-label'] ) : ?>
				<div class="option-element">
					<span><?php echo $field['inline-label']; ?></span>
				</div>
			<?php endif; ?>
			<div class="option-element <?php echo $field['type']; ?> <?php echo $field['class']; ?>">
				<?php if ( isset( $field['label'] ) && '' !== $field['label'] ) : ?>
					<label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
				<?php endif; ?>
				<?php yith_plugin_fw_get_field( $field, true ); ?>
			</div>
		<?php } ?>
	</div>
	<?php

}
