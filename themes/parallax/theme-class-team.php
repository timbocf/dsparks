<?php
if ( ! class_exists( 'Themify_Team' ) ) {
	/**
	 * Class to create teams
	 */
	class Themify_Team extends Themify_Types {
		function __construct($args = array()) {
			parent::__construct($args);
			add_shortcode('progress_bar', array($this, 'progress_bar'));
			add_shortcode('themify_progress_bar', array($this, 'progress_bar'));
			add_shortcode($this->post_type . '-social', array($this, 'social'));
			add_shortcode('themify_'.$this->post_type . '-social', array($this, 'social'));
		}
		
		/**
		 * Progress bar shortcode rendering
		 * @param array $atts
		 * @return string
		 */
		function progress_bar( $atts = array() ) {
			extract( shortcode_atts( array(
				'label' => '',
				'color' => '#02daaf',
				'percentage' => '100'
				), $atts ) );
			$percentage .= strrpos($percentage, '%') === false ? '%' : '';
			return sprintf(
				'<div class="progress-bar">
					<i class="progress-bar-label" style="color:%s;">%s</i>
					<span class="progress-bar-bg" data-percent="%s" style="background-color:%s;width:%s;"></span>
				</div>',
				$color,
				$label,
				$percentage,
				$color,
				$percentage
			);
		}

		/**
		 * Social shortcode rendering
		 * @param array $atts
		 * @return string
		 */
		function social( $atts = array() ) {
			extract( shortcode_atts( array(
				'link' => '',
				'image' => '',
				'icon' => '',
				), $atts ) );

			if ( '' != $icon ) {
				$icon = '<i class="fa fa-' . $icon . '"></i>';
			} else {
				$icon = '';
			}

			if ( '' != $image ) {
				$image = '<img src="' . $image . '" alt="social-link" />';
			} else {
				$image = '';
			}

			return sprintf(
				'<a class="team-social-link" href="%s">%s %s</a>',
				$link,
				$icon,
				$image
			);
		}
		
		/**
		 * Main shortcode rendering
		 * @param array $atts
		 * @param $post_type
		 * @return string|void
		 */
		function shortcode( $atts = array(), $post_type ) {
			extract($atts);
			// Parameters to get posts
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => $limit,
				'order' => $order,
				'orderby' => $orderby,
				'suppress_filters' => false
			);
			$args['tax_query'] = $this->parse_category_args($category, $post_type);
			
			// Defines layout type
			$cpt_layout_class = $this->post_type.'-multiple clearfix type-multiple';
			$multiple = true;
	
			// Single post type or many single post types
			if( '' != $id ){
				if(strpos($id, ',')){
					// Multiple ////////////////////////////////////
					$ids = explode(',', str_replace(' ', '', $id));
					foreach ($ids as $string_id) {
						$int_ids[] = intval($string_id);
					}
					$args['post__in'] = $int_ids;
					$args['orderby'] = 'post__in';
				} else {
					// Single ///////////////////////////////////////
					$args['p'] = intval($id);
					$cpt_layout_class = $this->post_type.'-single';
					$multiple = false;
				}
			}

			// Get posts according to parameters
			$posts = get_posts( apply_filters('themify_'.$post_type.'_shortcode_args', $args) );
	
			// Collect markup to be returned
			$out = '';
			
			if( $posts ) {
				global $themify;
				// save a copy
				$themify_save = clone $themify;
			
				// override $themify object

				// set image link
				$themify->unlink_image =  ( '' == $unlink_image || 'no' == $unlink_image )? 'no' : 'yes';
				$themify->hide_image = 'yes' == $image? 'no' : 'yes';

				// set title link
				$themify->unlink_title =  ( '' == $unlink_title || 'no' == $unlink_title )? 'no' : 'yes';
				$themify->hide_title = 'yes' == $title? 'no' : 'yes';

				if(!$multiple) {	
					if( '' == $image_w || get_post_meta($args['p'], 'image_width', true ) ){
						$themify->width = get_post_meta($args['p'], 'image_width', true );
					}
					if( '' == $image_h || get_post_meta($args['p'], 'image_height', true ) ){
						$themify->height = get_post_meta($args['p'], 'image_height', true );
					}
				} else {
					$themify->width = $image_w;
					$themify->height = $image_h;
				}
				$themify->use_original_dimensions = 'yes' == $use_original_dimensions? 'yes': 'no';
				$themify->display_content = $display;
				$themify->more_link = $more_link;
				$themify->more_text = $more_text;
				$themify->post_layout = $style;
				$themify->col_class = $this->column_class($style);

				if ( is_singular( 'team' ) ) {
					$teampre = 'setting-default_team_single_';
					$themify->hide_image = themify_check( $teampre.'hide_image' )? themify_get( $teampre.'hide_image' ) : 'no';
					$themify->hide_title = themify_check( $teampre.'hide_title' )? themify_get( $teampre.'hide_title' ) : 'no';
					$themify->unlink_image = themify_check( $teampre.'unlink_image' )? themify_get( $teampre.'unlink_image' ) : 'no';
					$themify->unlink_title = themify_check( $teampre.'unlink_title' )? themify_get( $teampre.'unlink_title' ) : 'no';
					$themify->width = themify_check( $teampre.'image_post_width' )? themify_get( $teampre.'image_post_width' ) : 144;
					$themify->height = themify_check( $teampre.'image_post_height' )? themify_get( $teampre.'image_post_height' ) : 144;
				}
				
				$out .= '<div class="loops-wrapper shortcode ' . $post_type  . ' ' . $style . ' '. $cpt_layout_class .'">';
					$out .= themify_get_shortcode_template($posts, 'includes/loop-team', 'index');
					$out .= $this->section_link($more_link, $more_text, $post_type);
				$out .= '</div>';
				
				$themify = clone $themify_save; // revert to original $themify state
			}
			return $out;
		}
	}
}

/***************************************************
 * Themify Theme Settings Module
 ***************************************************/

if ( ! function_exists( 'themify_default_team_single_layout' ) ) {
	/**
	 * Default Single Team Layout
	 * @param array $data
	 * @return string
	 */
	function themify_default_team_single_layout( $data=array() ) {
		/**
		 * Associative array containing theme settings
		 * @var array
		 */
		$data = themify_get_data();

		/**
		 * Sidebar Layout Options
		 * @var array
		 */
		$sidebar_options = array(
			array(
				'value' => 'sidebar1',
				'img' => 'images/layout-icons/sidebar1.png',
				'title' => __('Sidebar Right', 'themify')
			),
			array(
				'value' => 'sidebar1 sidebar-left',
				'img' => 'images/layout-icons/sidebar1-left.png',
				'title' => __('Sidebar Left', 'themify')
			),
			array(
				'value' => 'sidebar-none',
				'img' => 'images/layout-icons/sidebar-none.png',
				'title' => __('No Sidebar', 'themify'),
				'selected' => true
			)
		);

		/**
		 * Variable prefix key
		 * @var string
		 */
		$prefix = 'setting-default_team_single_';

		/**
		 * Sidebar Layout
		 * @var string
		 */
		$layout = themify_get( $prefix.'layout' );

		/**
		 * Basic default options '', 'yes', 'no'
		 * @var array
		 */
		$default_options = array(
			array('name'=>'','value'=>''),
			array('name'=>__('Yes', 'themify'),'value'=>'yes'),
			array('name'=>__('No', 'themify'),'value'=>'no')
		);

		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '';

		/**
		 * Sidebar Layout
		 */
		$output .= '<p>
						<span class="label">' . __('Team Sidebar Option', 'themify') . '</span>';
						foreach($sidebar_options as $option){
							if ( ( $layout == '' || ! $layout || ! isset( $layout ) ) && isset( $option['selected'] ) ) {
								$layout = $option['value'];
							}
							if($layout == $option['value']){
								$class = 'selected';
							} else {
								$class = '';
							}
							$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
						}
						$output .= '<input type="hidden" name="'.$prefix.'layout" class="val" value="'.$layout.'" />';
		$output .= '</p>';

		/**
		 * Hide Team Title
		 */
		$output .= '<p>
						<span class="label">' . __('Hide Team Title', 'themify') . '</span>
						<select name="'.$prefix.'hide_title">' .
							themify_options_module($default_options, $prefix.'hide_title') . '
						</select>
					</p>';

		/**
		 * Unlink Team Title
		 */
		$output .=	'<p>
						<span class="label">' . __('Unlink Team Title', 'themify') . '</span>
						<select name="'.$prefix.'unlink_title">' .
							themify_options_module($default_options, $prefix.'unlink_title') . '
						</select>
					</p>';
		/**
		 * Hide Featured Image
		 */
		$output .= '<p>
						<span class="label">' . __('Hide Featured Image', 'themify') . '</span>
						<select name="'.$prefix.'hide_image">' .
							themify_options_module($default_options, $prefix.'hide_image') . '
						</select>
					</p>';

		/**
		 * Unlink Featured Image
		 */
		$output .= '<p>
						<span class="label">' . __('Unlink Featured Image', 'themify') . '</span>
						<select name="'.$prefix.'unlink_image">' .
							themify_options_module($default_options, $prefix.'unlink_image') . '
						</select>
					</p>';

		/**
		 * Image Dimensions
		 */
		$output .= '
			<p>
				<span class="label">' . __('Image Size', 'themify') . '</span>
				<input type="text" class="width2" name="'.$prefix.'image_post_width" value="'.themify_get( $prefix.'image_post_width' ).'" /> ' . __('width', 'themify') . ' <small>(px)</small>
				<input type="text" class="width2" name="'.$prefix.'image_post_height" value="'.themify_get( $prefix.'image_post_height' ).'" /> ' . __('height', 'themify') . ' <small>(px)</small>
			</p>';

		return $output;
	}
}

if ( ! function_exists( 'themify_team_slug' ) ) {
	/**
	 * Team Slug
	 * @param array $data
	 * @return string
	 */
	function themify_team_slug( $data=array() ) {
		$data = themify_get_data();
		$team_slug = isset($data['themify_team_slug'])? $data['themify_team_slug']: apply_filters('themify_team_rewrite', 'team');
		return '
			<p>
				<span class="label">' . __('Team Base Slug', 'themify') . '</span>
				<input type="text" name="themify_team_slug" value="'.$team_slug.'" class="slug-rewrite">
				<br />
				<span class="pushlabel"><small>' . __('Use only lowercase letters, numbers, underscores and dashes.', 'themify') . '</small></span>
				<br />
				<span class="pushlabel"><small>' . sprintf(__('After changing this, go to <a href="%s">permalinks</a> and click "Save changes" to refresh them.', 'themify'), admin_url('options-permalink.php')) . '</small></span><br />
			</p>';
	}
}

if ( ! function_exists( 'themify_single_team_layout_condition' ) ) {
	/**
	 * Catches condition to filter body class when it's a singular team view
	 * @param $condition
	 * @return bool
	 */
	function themify_single_team_layout_condition( $condition ) {
		return $condition || is_singular( 'team' );
	}
	add_filter('themify_default_layout_condition', 'themify_single_team_layout_condition', 13);
}
if ( ! function_exists( 'themify_single_team_default_layout' ) ) {
	/**
	 * Filters sidebar layout body class to output the correct one when it's a singular team view
	 * @param $class
	 * @return mixed|string
	 */
	function themify_single_team_default_layout( $class ) {
		if ( is_singular( 'team' ) ) {
			$layout = 'setting-default_team_single_layout';
			$class = themify_check( $layout )? themify_get( $layout ) : 'sidebar1';
		}
		return $class;
	}
	add_filter('themify_default_layout', 'themify_single_team_default_layout', 13);
}