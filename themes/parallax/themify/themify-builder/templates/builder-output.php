<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div id="themify_builder_content-<?php echo $builder_id; ?>" data-postid="<?php echo $builder_id; ?>" class="themify_builder_content themify_builder_content-<?php echo $builder_id; ?> themify_builder themify_builder_front">
	<?php 
		foreach ( $builder_output as $key => $row ) {
			$this->get_template_row( $key, $row, $builder_id, true );
		} // end row loop
		
		if ( $this->frontedit_active ) {
			themify_builder_col_detection();
		}
	?>
</div>
<!-- /themify_builder_content -->