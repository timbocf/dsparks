<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class to create a control and set the position of an element.
 *
 * @since 1.0.0
 */
class Themify_Position_Control extends Themify_Control {

	/**
	 * Type of this control.
	 * @access public
	 * @var string
	 */
	public $type = 'themify_position';

	/**
	 * Render the control's content.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		$v = $this->value();
		$values = json_decode( $v );
		wp_enqueue_script( 'json2' );

		// Units
		$current_unit = isset( $values->unit ) ? $values->unit : 'px';
		$units = array( 'px', '%', 'em' );

		// Positions
		$current_position = isset( $values->position ) ? $values->position : '';
		$positions = array(
			'static' => __( 'Static', 'themify' ),
			'relative' => __( 'Relative', 'themify' ),
			'fixed' => __( 'Fixed', 'themify' ),
			'absolute' => __( 'Absolute', 'themify' ),
		);

		// Coordinates
		$sides = array(
			'top' => __( 'Top', 'themify' ),
			'right' => __( 'Right', 'themify' ),
			'bottom' => __( 'Bottom', 'themify' ),
			'left' => __( 'Left', 'themify' ),
		);
		?>

		<?php if ( $this->show_label && ! empty( $this->label ) ) : ?>
			<span class="customize-control-title themify-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>

		<!-- Element Position -->
		<div class="themify-customizer-brick">

			<div class="custom-select">
				<select class="position">
					<option value=""></option>
					<?php foreach ( $positions as $position => $label ) : ?>
						<option value="<?php echo $position; ?>" <?php selected( $position, $current_position ); ?>><?php echo $label; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<label><?php _e( 'Position', 'themify' ); ?></label><a href="http://themify.me/docs/styling#properties-positioning" target="_blank" class="doc-link">(?)</a>

		</div>

		<?php foreach ( $sides as $side => $side_label ) :
			$width = isset( $values->{$side}->width ) ? $values->{$side}->width : '';
			?>
			<div class="themify-customizer-brick position-wrap">

				<div class="auto-prop-combo js-hide-<?php echo $side; ?> hcollapse">

					<input type="text" class="dimension-width" value="<?php echo $width; ?>" data-side="<?php echo $side;	?>"	/>

					<div class="custom-select">
						<select class="dimension-unit" data-side="<?php echo $side; ?>">
							<?php foreach ( $units as $unit ) :
								$unit_val = isset( $values->{$side}->unit ) ? $values->{$side}->unit : 'px'; ?>
								<option value="<?php echo $unit; ?>" <?php selected( $unit_val, $unit ); ?>><?php echo $unit; ?></option>
							<?php endforeach; ?>
						</select>
					</div>

				</div>

			<span class="auto-prop-label">
				<?php
				// CSS property value: auto
				$auto = isset( $values->{$side}->auto ) ? $values->{$side}->auto : '';
				$auto_id = $this->id . '_' . $side . '_auto';
				?>
				<label class="dimension-row-label"><?php echo $side_label; ?></label>
				<input id="<?php echo $auto_id; ?>" type="checkbox" class="auto-prop" <?php checked( $auto, 'auto' ); ?> value="auto" data-hide="js-hide-<?php echo $side; ?>" data-side="<?php echo $side; ?>"/>
				<label for="<?php echo $auto_id; ?>">
					<?php _e( 'Auto', 'themify' ); ?>
				</label>
			</span>

			</div>
		<?php endforeach; ?>

		<input <?php $this->link(); ?> value='<?php echo esc_attr( $v ); ?>' type="hidden" class="<?php echo $this->type; ?>_control themify-customizer-value-field"/>
		<?php
	}
}