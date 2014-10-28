<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Module Name: Map
 * Description: Display Map
 */
class TB_Map_Module extends Themify_Builder_Module {
	function __construct() {
		parent::__construct(array(
			'name' => __('Map', 'themify'),
			'slug' => 'map'
		));

		add_action( 'wp_enqueue_scripts', array( $this, 'load_map_script' ) );
	}

	function load_map_script() {
		//Register map scripts
		wp_register_script( 'themify-builder-map-script', themify_https_esc( 'http://maps.google.com/maps/api/js' ) . '?sensor=false', array(), false, true );
	}
}

///////////////////////////////////////
// Module Options
///////////////////////////////////////
$zoom_opt = array();
for ( $i=1; $i < 17 ; $i++ ) { 
 array_push( $zoom_opt, $i );
}
Themify_Builder_Model::register_module( 'TB_Map_Module', 
	apply_filters( 'themify_builder_module_map', array(
		'options' => array(
			array(
				'id' => 'mod_title_map',
				'type' => 'text',
				'label' => __('Module Title', 'themify'),
				'class' => 'large'
			),
			array(
				'id' => 'address_map',
				'type' => 'textarea',
				'value' => '',
				'class' => 'fullwidth',
				'label' => __('Address', 'themify')
			),
			array(
				'id' => 'zoom_map',
				'type' => 'selectbasic',
				'label' => __('Zoom', 'themify'),
				'default' => 8,
				'options' => $zoom_opt
			),
			array(
				'id' => 'w_map',
				'type' => 'text',
				'class' => 'xsmall',
				'label' => __('Width', 'themify'),
				'unit' => array(
					'id' => 'unit_w',
					'selected' => '%',
					'options' => array(
						array( 'id' => 'pixel_unit_w', 'value' => 'px'),
						array( 'id' => 'percent_unit_w', 'value' => '%')
					)
				),
				'value' => 100
			),
			array(
				'id' => 'h_map',
				'type' => 'text',
				'label' => __('Height', 'themify'),
				'class' => 'xsmall',
				'unit' => array(
					'id' => 'unit_h',
					'options' => array(
						array( 'id' => 'pixel_unit_h', 'value' => 'px')
					)
				),
				'value' => 300
			),
			array(
				'id' => 'multi_map_border',
				'type' => 'multi',
				'label' => __('Border', 'themify'),
				'fields' => array(
					array(
						'id' => 'b_style_map',
						'type' => 'select',
						'label' => '',
						'options' => array(
							'solid' => __( 'Solid', 'themify' ),
							'dotted' => __( 'Dotted', 'themify' ),
							'dashed' => __( 'Dashed', 'themify' ),
						)
					),
					array(
						'id' => 'b_width_map',
						'type' => 'text',
						'label' => '',
						'class' => 'medium',
						'after' => 'px'
					),
					array(
						'id' => 'b_color_map',
						'type' => 'text',
						'colorpicker' => true,
						'class' => 'large',
						'label' => ''
					),
				)
			),
			array(
				'id' => 'type_map',
				'type' => 'select',
				'label' => __('Type', 'themify'),
				'options' => array(
					'ROADMAP' => __( 'Road Map', 'themify' ),
					'SATELLITE' => __( 'Satellite', 'themify' ),
					'HYBRID' => __( 'Hybrid', 'themify' ),
					'TERRAIN' => __( 'Terrain', 'themify' )
				)
			),
			array(
				'id' => 'scrollwheel_map',
				'type' => 'select',
				'label' => __( 'Scrollwheel', 'themify' ),
				'options' => array(
					'enable' => __( 'Enable', 'themify' ),
					'disable' => __( 'Disable', 'themify' )
				)
			),
			array(
				'id' => 'draggable_map',
				'type' => 'select',
				'label' => __( 'Draggable', 'themify' ),
				'options' => array(
					'enable' => __( 'Enable', 'themify' ),
					'disable' => __( 'Disable', 'themify' )
				)
			),
			array(
				'id' => 'draggable_disable_mobile_map',
				'type' => 'select',
				'label' => __( 'Disable draggable on mobile', 'themify' ),
				'options' => array(
					'yes' => __( 'Yes', 'themify' ),
					'no' => __( 'No', 'themify' )
				)
			),
			array(
				'id' => 'info_window_map',
				'type' => 'textarea',
				'value' => '',
				'class' => 'fullwidth',
				'label' => __('Info window', 'themify'),
				'help' => __('Additional info that will be shown when clicking on map marker', 'themify')
			)
		),
		// Styling
		'styling' => array(
			// Animation
			array(
				'id' => 'separator_animation',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Animation', 'themify').'</h4>'),
			),
			array(
				'id' => 'animation_effect',
				'type' => 'animation_select',
				'label' => __( 'Effect', 'themify' ),
				'class' => ''
			),
			// Background
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_image_background',
				'title' => '',
				'description' => '',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Background', 'themify').'</h4>'),
			),
			array(
				'id' => 'background_color',
				'type' => 'color',
				'label' => __('Background Color', 'themify'),
				'class' => 'small'
			),
			// Padding
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_padding',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Padding', 'themify').'</h4>'),
			),
			array(
				'id' => 'multi_padding_top',
				'type' => 'multi',
				'label' => __('Padding', 'themify'),
				'fields' => array(
					array(
						'id' => 'padding_top',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'padding_top_unit',
						'type' => 'select',
						'description' => __('top', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_padding_right',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'padding_right',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'padding_right_unit',
						'type' => 'select',
						'description' => __('right', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_padding_bottom',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'padding_bottom',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'padding_bottom_unit',
						'type' => 'select',
						'description' => __('bottom', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_padding_left',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'padding_left',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'padding_left_unit',
						'type' => 'select',
						'description' => __('left', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			// Margin
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_margin',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Margin', 'themify').'</h4>'),
			),
			array(
				'id' => 'multi_margin_top',
				'type' => 'multi',
				'label' => __('Margin', 'themify'),
				'fields' => array(
					array(
						'id' => 'margin_top',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'margin_top_unit',
						'type' => 'select',
						'description' => __('top', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_margin_right',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'margin_right',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'margin_right_unit',
						'type' => 'select',
						'description' => __('right', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_margin_bottom',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'margin_bottom',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'margin_bottom_unit',
						'type' => 'select',
						'description' => __('bottom', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			array(
				'id' => 'multi_margin_left',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'margin_left',
						'type' => 'text',
						'class' => 'xsmall'
					),
					array(
						'id' => 'margin_left_unit',
						'type' => 'select',
						'description' => __('left', 'themify'),
						'meta' => array(
							array('value' => 'px', 'name' => __('px', 'themify')),
							array('value' => '%', 'name' => __('%', 'themify'))
						)
					),
				)
			),
			// Border
			array(
				'type' => 'separator',
				'meta' => array('html'=>'<hr />')
			),
			array(
				'id' => 'separator_border',
				'type' => 'separator',
				'meta' => array('html'=>'<h4>'.__('Border', 'themify').'</h4>'),
			),
			array(
				'id' => 'multi_border_top',
				'type' => 'multi',
				'label' => __('Border', 'themify'),
				'fields' => array(
					array(
						'id' => 'border_top_color',
						'type' => 'color',
						'class' => 'small'
					),
					array(
						'id' => 'border_top_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall'
					),
					array(
						'id' => 'border_top_style',
						'type' => 'select',
						'description' => __('top', 'themify'),
						'meta' => array(
							array( 'value' => '', 'name' => '' ),
							array( 'value' => 'solid', 'name' => __( 'Solid', 'themify' ) ),
							array( 'value' => 'dashed', 'name' => __( 'Dashed', 'themify' ) ),
							array( 'value' => 'dotted', 'name' => __( 'Dotted', 'themify' ) ),
							array( 'value' => 'double', 'name' => __( 'Double', 'themify' ) )
						)
					)
				)
			),
			array(
				'id' => 'multi_border_right',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'border_right_color',
						'type' => 'color',
						'class' => 'small'
					),
					array(
						'id' => 'border_right_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall'
					),
					array(
						'id' => 'border_right_style',
						'type' => 'select',
						'description' => __('right', 'themify'),
						'meta' => array(
							array( 'value' => '', 'name' => '' ),
							array( 'value' => 'solid', 'name' => __( 'Solid', 'themify' ) ),
							array( 'value' => 'dashed', 'name' => __( 'Dashed', 'themify' ) ),
							array( 'value' => 'dotted', 'name' => __( 'Dotted', 'themify' ) ),
							array( 'value' => 'double', 'name' => __( 'Double', 'themify' ) )
						)
					)
				)
			),
			array(
				'id' => 'multi_border_bottom',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'border_bottom_color',
						'type' => 'color',
						'class' => 'small'
					),
					array(
						'id' => 'border_bottom_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall'
					),
					array(
						'id' => 'border_bottom_style',
						'type' => 'select',
						'description' => __('bottom', 'themify'),
						'meta' => array(
							array( 'value' => '', 'name' => '' ),
							array( 'value' => 'solid', 'name' => __( 'Solid', 'themify' ) ),
							array( 'value' => 'dashed', 'name' => __( 'Dashed', 'themify' ) ),
							array( 'value' => 'dotted', 'name' => __( 'Dotted', 'themify' ) ),
							array( 'value' => 'double', 'name' => __( 'Double', 'themify' ) )
						)
					)
				)
			),
			array(
				'id' => 'multi_border_left',
				'type' => 'multi',
				'label' => '',
				'fields' => array(
					array(
						'id' => 'border_left_color',
						'type' => 'color',
						'class' => 'small'
					),
					array(
						'id' => 'border_left_width',
						'type' => 'text',
						'description' => 'px',
						'class' => 'xsmall'
					),
					array(
						'id' => 'border_left_style',
						'type' => 'select',
						'description' => __('left', 'themify'),
						'meta' => array(
							array( 'value' => '', 'name' => '' ),
							array( 'value' => 'solid', 'name' => __( 'Solid', 'themify' ) ),
							array( 'value' => 'dashed', 'name' => __( 'Dashed', 'themify' ) ),
							array( 'value' => 'dotted', 'name' => __( 'Dotted', 'themify' ) ),
							array( 'value' => 'double', 'name' => __( 'Double', 'themify' ) )
						)
					)
				)
			),
			// Additional CSS
			array(
				'type' => 'separator',
				'meta' => array( 'html' => '<hr/>') 
			),
			array(
				'id' => 'css_map',
				'type' => 'text',
				'label' => __('Additional CSS Class', 'themify'),
				'class' => 'large exclude-from-reset-field',
				'description' => sprintf( '<br/><small>%s</small>', __('Add additional CSS class(es) for custom styling', 'themify') )
			)
		),
		'styling_selector' => array(
			'.module-map' => array(
				'background_color', 'padding', 'margin', 'border_top', 'border_right', 'border_bottom', 'border_left'
			)
		)
	) )
);