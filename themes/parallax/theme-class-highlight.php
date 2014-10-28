<?php
if(!class_exists('Themify_Highlight')){
	/**
	 * Class to create highlights
	 */
	class Themify_Highlight extends Themify_Types {
		function shortcode($atts = array(), $post_type){
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
					$ids = explode(',', str_replace(' ', '', $id));
					foreach ($ids as $string_id) {
						$int_ids[] = intval($string_id);
					}
					$args['post__in'] = $int_ids;
					$args['orderby'] = 'post__in';
				} else {
					$args['p'] = intval($id);
					$cpt_layout_class = $this->post_type.'-single';
					$multiple = false;
				}
			}
			
			// Get posts according to parameters
			$posts = get_posts( apply_filters('themify_'.$post_type.'_shortcode_args', $args) );
	
			// Collect markup to be returned
			$out = '';

			if ($posts) {
				global $themify;
				$themify_save = clone $themify; // save a copy
			
				// override $themify object
				$themify->hide_title = $title;
				$themify->hide_image = $image;
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
				$themify->display_content = $display;
				$themify->more_link = $more_link;
				$themify->more_text = $more_text;
				$themify->post_layout = $style;
				$themify->col_class = $this->column_class($style);
				
				$out .= '<div class="loops-wrapper shortcode ' . $post_type  . ' ' . $style . ' '. $cpt_layout_class .'">';
					$out .= themify_get_shortcode_template($posts, 'includes/loop-highlight', 'index');
					$out .= $this->section_link($more_link, $more_text, $post_type);
				$out .= '</div>';
				
				$themify = clone $themify_save; // revert to original $themify state
			}
			return $out;
		}
	}
}
?>