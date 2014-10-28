<?php
if(!class_exists('Themify_Section')){
	/**
	 * Class to create sections
	 */
	class Themify_Section extends Themify_Types {

		function manage_and_filter() {
			parent::manage_and_filter();
			add_action( 'admin_init', array( $this, 'add_menu_meta_box' ) );
		}

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
				$themify->display_content = $display;
				$themify->more_link = $more_link;
				$themify->more_text = $more_text;
				$themify->post_layout = $style;
				
				$out .= '<div class="loops-wrapper shortcode ' . $post_type  . ' ' . $style . ' '. $cpt_layout_class .'">';
					$out .= themify_get_shortcode_template($posts, 'includes/loop-section', 'index');
					$out .= $this->section_link($more_link, $more_text, $post_type);
				$out .= '</div>';
				
				$themify = clone $themify_save; // revert to original $themify state
			}
			return $out;
		}

		/**************************************************************************************************
		 * Section meta box for Menus screen
		 **************************************************************************************************/

		/**
 		 * Add section ID to menu. Useful for query section pages pointing to IDs in the page.
		 */
		function add_menu_meta_box() {
			add_meta_box( $this->post_type . '-menu', __( 'Sections', 'themify' ), array( $this, 'meta_box' ), 'nav-menus', 'side', 'high' );
		}

		/**
 		 * Meta box for section post type in menus screen.
		 */
		function meta_box() {
			global $_nav_menu_placeholder, $nav_menu_selected_id;

			$sections = get_posts( array( 'post_type' => 'section', 'posts_per_page' => -1 ) );
			?><div id="posttype-section" class="posttypediv">
				<div id="tabs-panel-posttype-section-most-recent" class="tabs-panel tabs-panel-active">
					<ul id="sectionchecklist-most-recent" class="categorychecklist form-no-clear">
					<?php if( ! empty( $sections ) ) : foreach( $sections as $section ) : ?>
						<li><label class="menu-item-title"><input type="checkbox" class="menu-item-checkbox" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $section->ID ); ?>"> <?php echo esc_attr( $section->post_title ); ?></label><input type="hidden" class="menu-item-db-id" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-db-id]" value="0"><input type="hidden" class="menu-item-object" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-object]" value="custom"><input type="hidden" class="menu-item-parent-id" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-parent-id]" value="0"><input type="hidden" class="menu-item-type" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-type]" value="custom"><input type="hidden" class="menu-item-title" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-title]" value="<?php echo esc_attr( $section->post_title ); ?>"><input type="hidden" class="menu-item-url" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-url]" value="#<?php echo esc_attr( $section->post_name ); ?>"><input type="hidden" class="menu-item-target" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-target]" value=""><input type="hidden" class="menu-item-attr_title" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-attr_title]" value=""><input type="hidden" class="menu-item-classes" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-classes]" value=""><input type="hidden" class="menu-item-xfn" name="menu-item[-<?php echo esc_attr( $section->ID ); ?>][menu-item-xfn]" value=""></li>
					<?php endforeach; endif; ?>
					</ul>
				</div><!-- /.tabs-panel -->

				<p class="button-controls">
					<span class="list-controls">
						<a href="<?php echo add_query_arg( array( 'selectall' => 1 ), admin_url( 'nav-menus.php' ) ); ?>#posttype-section" class="select-all"><?php _e( 'Select All', 'themify' ) ?></a>
					</span>

					<span class="add-to-menu">
						<input type="submit" class="button-secondary submit-add-to-menu right" value="<?php _e( 'Add to Menu', 'themify' ); ?>" name="add-post-type-menu-item" id="submit-posttype-section">
						<span class="spinner"></span>
					</span>
				</p>
			</div><!-- /.posttypediv --><?php
		}
	}
}
?>