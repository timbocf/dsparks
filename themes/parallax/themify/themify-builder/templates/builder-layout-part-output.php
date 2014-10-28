<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
	<?php foreach ( $builder_output as $rows => $row ): ?>
	<!-- module_row -->
	<?php
	$row['row_order'] = isset( $row['row_order'] ) ? $row['row_order'] : '1';
	$row_classes = array( 'module_row', 'module_row_' . $row['row_order'], 'clearfix' );
	$class_fields = array( 'custom_css_row', 'background_repeat', 'animation_effect', 'row_width', 'row_height' );
	foreach( $class_fields as $field ) {
		if ( isset( $row['styling'][ $field ] ) && ! empty( $row['styling'][ $field ] ) ) array_push( $row_classes, $row['styling'][ $field ] );
	}
	?>
	<div class="themify_builder_content-<?php echo $builder_id; ?> themify_builder">
		<div class="<?php echo implode(' ', $row_classes ); ?>">

			<div class="row_inner">

				<?php do_action('themify_builder_row_start', $builder_id, $row ); ?>

				<?php if ( isset( $row['cols'] ) && count( $row['cols'] ) > 0 ):
					
					$count = count( $row['cols'] );

					switch ( $count ) {
						
						case 4:
							$order_classes = array( 'first', 'second', 'third', 'last' );
						break;

						case 3:
							$order_classes = array( 'first', 'middle', 'last' );
						break;

						case 2:
							$order_classes = array( 'first', 'last' );
						break;

						default:
							$order_classes = array( 'first' );
						break;
					}

					foreach ( $row['cols'] as $cols => $col ):
						$columns_class = array();
						$grid_class = explode(' ', $col['grid_class'] );
						$dynamic_class[0] = $order_classes[ $cols ];
						$columns_class = array_merge( $columns_class, $grid_class );
						foreach( $dynamic_class as $class ) {
							array_push( $columns_class, $class );
						}
						$columns_class = array_unique( $columns_class );
						// remove class "last" if the column is fullwidth
						if ( 1 == $count ) {
							if ( ( $key = array_search( 'last', $columns_class ) ) !== false) {
								unset( $columns_class[ $key ] );
							}
						}
						$print_column_classes = implode( ' ', $columns_class );
						?>

				<div class="<?php echo $print_column_classes; ?>">	
					<?php
						if ( isset( $col['modules'] ) && count( $col['modules'] ) > 0 ) { 
							foreach ( $col['modules'] as $modules => $mod ) { 
								$identifier = array( $rows, $cols, $modules ); // define module id
								$this->get_template_module( $mod, $builder_id, true, false, '', $identifier );
							}
						}
					?>
				</div>
				<!-- /col -->
				<?php endforeach; endif; // end col loop ?>
				
				<?php do_action('themify_builder_row_end', $builder_id, $row ); ?>

			</div>
			<!-- /row_inner -->
		</div>
		<!-- /module_row -->
	</div>

	<?php endforeach; // end row loop ?>