<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Layout Part
 * Description: Layout Part Module
 */
class TB_Layout_Part_Module extends Themify_Builder_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Layout Part', 'themify'),
			'slug' => 'layout-part'
		));

		add_action( 'themify_builder_lightbox_fields', array( $this, 'add_fields' ), 10, 2 );
	}

	function add_fields( $field, $mod_name ) {
		if ( $mod_name != 'layout-part' ) return;
		global $Themify_Builder_Layouts;
		$output = '';
		switch ( $field['type'] ) {
			case 'layout_part_select':
				$output .= '<select name="'.$field['id'].'" id="'.$field['id'].'" class="tfb_lb_option">';
				$output .= '<option></option>';
				$args = array(
					'post_type' => $Themify_Builder_Layouts->layout_part->post_type_name,
					'posts_per_page' => -1
				);
				$posts = get_posts( $args );
				foreach ( $posts as $part ) {
					$output .= '<option value="'.$part->post_name.'">'.$part->post_title.'</option>';
				}
				$output .= '</select><br/>';
				$output .= sprintf(__('<a href="%s" target="_blank" class="add_new"><span class="themify_builder_icon add"></span> New Layout Part</a>', 'themify'), admin_url('post-new.php?post_type=' . $Themify_Builder_Layouts->layout_part->post_type_name));
				$output .= sprintf(__('<a href="%s" target="_blank" class="add_new"><span class="themify_builder_icon ti-folder"></span> Manage Layout Part</a>', 'themify'), admin_url('edit.php?post_type=' . $Themify_Builder_Layouts->layout_part->post_type_name));
			break;
		}
		echo $output;
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
Themify_Builder_Model::register_module( 'TB_Layout_Part_Module', 
	apply_filters( 'themify_builder_module_layout_part', array(
		'options' => array(
			array(
				'id' => 'mod_title_layout_part',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large'
			),
			array(
				'id' => 'selected_layout_part',
				'type' => 'layout_part_select',
				'label' => __('Select Layout Part', 'themify'),
				'class' => ''
			)
		),
		// Styling
		'styling' => array(
			// Additional CSS
			array(
				'id' => 'add_css_layout_part',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'description' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling', 'themify') ),
				'class' => 'large exclude-from-reset-field'
			)
		)
	) )
);