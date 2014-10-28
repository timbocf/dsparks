<?php
/**
 * Framework Name: Themify Builder
 * Framework URI: http://themify.me/
 * Description: Page Builder with interactive drag and drop features
 * Version: 1.0
 * Author: Themify
 * Author URI: http://themify.me
 *
 *
 * @package ThemifyBuilder
 * @category Core
 * @author Themify
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Layouts Constant
 */
define( 'THEMIFY_BUILDER_LAYOUTS_VERSION', '1.0.2' );

/**
 * Define builder constant
 */
define( 'THEMIFY_BUILDER_DIR', dirname(__FILE__) );
define( 'THEMIFY_BUILDER_MODULES_DIR', THEMIFY_BUILDER_DIR . '/modules' );
define( 'THEMIFY_BUILDER_TEMPLATES_DIR', THEMIFY_BUILDER_DIR . '/templates' );
define( 'THEMIFY_BUILDER_CLASSES_DIR', THEMIFY_BUILDER_DIR . '/classes' );
define( 'THEMIFY_BUILDER_INCLUDES_DIR', THEMIFY_BUILDER_DIR . '/includes' );
define( 'THEMIFY_BUILDER_LIBRARIES_DIR', THEMIFY_BUILDER_INCLUDES_DIR . '/libraries' );

// URI Constant
define( 'THEMIFY_BUILDER_URI', THEMIFY_URI . '/themify-builder' );

/**
 * Include builder class
 */
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-model.php' );
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-form.php' );
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-layouts.php' );
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-module.php' );
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder.php' );
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-import-export.php' );
require_once( THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-plugin-compat.php' );

/**
 * Init themify builder class
 */
add_action( 'after_setup_theme', 'themify_builder_init', 15 );
function themify_builder_init() {
	global $ThemifyBuilder, $Themify_Builder_Layouts;
	if ( class_exists( 'Themify_Builder') ) {
		if ( Themify_Builder_Model::builder_check() ) {
			$Themify_Builder_Layouts = new Themify_Builder_Layouts();

			$ThemifyBuilder = new Themify_Builder();
			$ThemifyBuilder->init();
			$themify_builder_plugin_compat = new Themify_Builder_Plugin_Compat();
			$themify_builder_import_export = new Themify_Builder_Import_Export();
			// Themify Shortcodes Editor Button: load required files.
			themify_wpeditor_add_shortcodes_button();
		}
	} // class_exists check

	if( is_admin() && current_user_can( 'update_plugins' ) ) {
		include THEMIFY_BUILDER_DIR . '/themify-builder-updater.php';
	}
}

if ( ! function_exists('themify_builder_edit_module_panel') ) {
	/**
	 * Hook edit module frontend panel
	 * @param $mod_name
	 * @param $mod_settings
	 */
	function themify_builder_edit_module_panel( $mod_name, $mod_settings ) {
		do_action( 'themify_builder_edit_module_panel', $mod_name, $mod_settings );
	}
}

if ( ! function_exists('themify_builder_col_detection') ) {
	/**
	 * Create rows and cols markup to used by jquery to calculate width grid column
	 */
	function themify_builder_col_detection() {
		global $ThemifyBuilder;
		include 'includes/themify-builder-col-detection.php';
	}
}

if(!function_exists('themify_manage_builder')) {
	/**
	 * Builder Settings
	 * @param array $data
	 * @return string
	 * @since 1.2.7
	 */
	function themify_manage_builder($data=array()) {
		global $ThemifyBuilder;
		$data = themify_get_data();
		$pre = 'setting-page_builder_';
		$output = '';
		$modules = $ThemifyBuilder->get_modules();

		foreach ($modules as $m) {
			$exclude = $pre.'exc_'.$m['name'];
			$checked = isset($data[$exclude]) ? 'checked="checked"' : '';
			$output .= '<p>
						<span><input id="builder_module_'.$m['name'].'" type="checkbox" name="'.$exclude.'" value="1" '.$checked.'/> <label for="builder_module_'.$m['name'].'">' . sprintf(__('Exclude %s module', 'themify'), ucfirst($m['name']) ) . '</label></span>
					</p>';	
		}
		
		return $output;
	}
}

if(!function_exists('themify_manage_builder_active')) {
	/**
	 * Builder Settings
	 * @param array $data
	 * @return string
	 * @since 1.2.7
	 */
	function themify_manage_builder_active($data=array()) {
		$pre = 'setting-page_builder_';
		$output = '';
		$options = array(
			array('name' => __('Enable', 'themify'), 'value' => 'enable'),
			array('name' => __('Disable', 'themify'), 'value' =>'disable')
		);

		$output .= '<p>
						<span class="label">' . __('Themify Builder:', 'themify') . '</span>
						<select name="'.$pre.'is_active">'.
						themify_options_module($options, $pre.'is_active') . '
						</select>
					</p>';

		return $output;
	}
}

if(!function_exists('themify_manage_builder_animation')) {
	/**
	 * Builder Setting Animations
	 * @param array $data
	 * @return string
	 * @since 2.0.0
	 */
	function themify_manage_builder_animation($data=array()) {
		$opt_data = themify_get_data();
		$pre = 'setting-page_builder_animation_';
		$mobile_checked = '';
		$disabled_checked = '';

		if ( isset( $opt_data[ $pre.'mobile_exclude' ] ) && $opt_data[ $pre.'mobile_exclude' ] ) 
			$mobile_checked = " checked='checked'";
		
		if ( isset( $opt_data[ $pre.'disabled' ] ) && $opt_data[ $pre . 'disabled' ] ) 
			$disabled_checked = " checked='checked'";

		$out = '';
		$out .= sprintf( '<p><label for="%s"><input type="checkbox" id="%s" name="%s"%s> %s</label></p>',
			$pre . 'mobile_exclude',
			$pre . 'mobile_exclude',
			$pre . 'mobile_exclude',
			$mobile_checked,
			__( 'Disable Builder animation on mobile and tablet only', 'themify')
		);
		$out .= sprintf( '<p><label for="%s"><input type="checkbox" id="%s" name="%s"%s> %s</label></p>',
			$pre . 'disabled',
			$pre . 'disabled',
			$pre . 'disabled',
			$disabled_checked,
			__( 'Disable Builder animation on all devices (all row and module animation will not have any effect)', 'themify')
		);

		return $out;
	}
}

/**
 * Add Builder to all themes using the themify_theme_config_setup filter.
 * @param $themify_theme_config
 * @return mixed
 * @since 1.4.2
 */
function themify_framework_theme_config_add_builder($themify_theme_config) {
	$themify_theme_config['panel']['settings']['tab']['page_builder'] = array(
		'title' => __('Themify Builder', 'themify'),
		'id' => 'themify-builder',
		'custom-module' => array(
			array(
				'title' => __('Enable/Disable Themify Builder', 'themify'),
				'function' => 'themify_manage_builder_active'
			),
		)
	);
	if ( 'disable' != apply_filters( 'themify_enable_builder', themify_get('setting-page_builder_is_active') ) ) {
		$themify_theme_config['panel']['settings']['tab']['page_builder']['custom-module'][] = array(
			'title' => __('Animation Effects', 'themify'),
			'function' => 'themify_manage_builder_animation'
		);

		$themify_theme_config['panel']['settings']['tab']['page_builder']['custom-module'][] = array(
			'title' => __('Exclude Builder Modules', 'themify'),
			'function' => 'themify_manage_builder'
		);
	}
	return $themify_theme_config;
};
add_filter('themify_theme_config_setup', 'themify_framework_theme_config_add_builder');