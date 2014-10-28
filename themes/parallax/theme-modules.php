<?php

/*
To add custom modules to the theme, create a new 'custom-modules.php' file in the theme folder.
They will be added to the theme automatically.
*/

/* 	Custom Modules
/***************************************************************************/

if(!function_exists('themify_header_background_slider')){
	/**
	 * @param array $data
	 * @return string
	 */
	function themify_header_background_slider($data=array()){
		$data = themify_get_data();

		$auto_options = array(__('Yes', 'themify') => 'yes', __('No', 'themify') => 'no');
		$autotimeout_options = apply_filters('themify_footer_slider_autotimeout', array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10));
		if ( ! themify_get( 'setting-footer_slider_auto' ) ) $data['setting-footer_slider_auto'] = 'yes';

		if ( ! themify_get( 'setting-footer_slider_autotimeout' ) ) $data['setting-footer_slider_autotimeout'] = 5;

		$speed_options = apply_filters( 'themify_footer_slider_speed', array(
			__('Normal', 'themify') => 500,
			__('Fast', 'themify') => 300,
			__('Slow', 'themify') => 1500
		) );

		$output = '<p>
						<span class="label">' . __('Autoplay', 'themify') . '</span>
						<select name="setting-footer_slider_auto">';
						foreach($auto_options as $label => $option){
							if ( isset( $data['setting-footer_slider_auto'] ) && ( $option == $data['setting-footer_slider_auto'] ) ) {
								$output .= '<option value="'.$option.'" selected="selected">'.$label.'</option>';
							} else {
								$output .= '<option value="'.$option.'">'.$label.'</option>';
							}
						}
		$output .= '	</select>
					</p>';

		$output .= '<p>
						<span class="label">' . __('Autoplay Timeout', 'themify') . '</span>
						<select name="setting-footer_slider_autotimeout">';
						foreach($autotimeout_options as $option){
							$label = sprintf(__('%s secs', 'themify'), $option);
							if($option == $data['setting-footer_slider_autotimeout']){
								$output .= '<option value="'.$option.'" selected="selected">'.$label.'</option>';
							} else {
								$output .= '<option value="'.$option.'">'.$label.'</option>';
							}
						}
		$output .= '	</select>
					</p>';

		$output .= '<p>
						<span class="label">' . __('Transition Speed', 'themify') . '</span>
						<select name="setting-footer_slider_speed">';
						foreach($speed_options as $name => $val){
							if ( isset( $data['setting-footer_slider_speed'] ) && ( $data['setting-footer_slider_speed'] == $val ) ) {
								$output .= '<option value="'.$val.'" selected="selected">'.$name.'</option>';
							} else {
								$output .= '<option value="'.$val.'">'.$name.'</option>';
							}
						}
		$output .= '	</select>
					</p>';
		return $output;
	}
}

if(!function_exists('themify_background_mode')){
	/**
	 * Background Cover/Repeat Module
	 * @param array $data Theme settings data.
	 * @return string Module markup.
	 */
	function themify_background_mode($data=array()){
		$data['value'] = isset( $data['value']['value'] ) ? $data['value']['value'] : '';
		$options = array(
				array(
					'value' => 'repeat',
					'name' => __('Repeat', 'themify')
				),
				array(
					'value' => 'fullcover',
					'name' => __('Fullcover', 'themify')
				)
			);
		// styling-background-header_wrap_background-background_mode-value-value
		$output = '<div class="themify_field_row">
					<span class="label">' . __('Background Mode', 'themify') . '</span>
					<select name="styling-'.$data['category'].'-'.$data['title'].'-background_mode-value-value"><option> </option>';
		foreach ( $options as $option ) {
			if ( isset( $data['value']['value'] ) && $option['value'] == $data['value']['value'] ) {
				$output .= '<option value="'.$option['value'].'" selected="selected">'.$option['name'].'</option>';
			} else {
				$output .= '<option value="'.$option['value'].'">'.$option['name'].'</option>';
			}
		}
		$output .=	'</select>
				   </div>';	
		return $output;
	}
}

if(!function_exists('themify_pagination_infinite')){
	/**
	 * Choose pagination or infinite scroll
	 * @param array $data
	 * @return string
	 */
	function themify_pagination_infinite( $data = array() ) {
		$data = themify_get_data();
		
		$html = '<p>';
	
			//Infinite Scroll
			$html .= '<input ' . checked( themify_check( 'setting-more_posts' ) ? themify_get( 'setting-more_posts' ) : 'infinite', 'infinite', false ) . ' type="radio" name="setting-more_posts" value="infinite" /> ';
			$html .= __('Infinite Scroll (posts are loaded on the same page)', 'themify');
			$html .= '<br/>';
			$html .= '<label for="setting-autoinfinite"><input class="disable-autoinfinite" type="checkbox" id="setting-autoinfinite" name="setting-autoinfinite" '.checked( themify_get( 'setting-autoinfinite' ), 'on', false ).'/> ' . __('Disable automatic infinite scroll', 'themify').'</label>';
			$html .= '<br/><br/>';

			//Numbered pagination
			$html .= '<input ' . checked( themify_get( 'setting-more_posts' ), 'pagination', false ) . ' type="radio" name="setting-more_posts" value="pagination" /> ';
			$html .= __('Standard Pagination', 'themify');

		$html .= '</p>';
		return $html;
	}
}

if(!function_exists('themify_default_page_layout')){
	/**
	 * Default Page Layout Module - Action
	 * @param array $data
	 * @return string
	 */
	function themify_default_page_layout($data=array()){
		$data = themify_get_data();
		
		$options = array(
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'selected' => true, 'title' => __('No Sidebar', 'themify')),
		);
		
		$default_options = array(
			array('name'=>'','value'=>''),
			array('name'=>__('Yes', 'themify'),'value'=>'yes'),
			array('name'=>__('No', 'themify'),'value'=>'no')
		);
							 
		$val = isset( $data['setting-default_page_layout'] ) ? $data['setting-default_page_layout'] : '';
		
		$output = '<p>
						<span class="label">' . __('Page Sidebar Option', 'themify') . '</span>';
		foreach ( $options as $option ) {
			if(( '' == $val || !$val || !isset($val)) && $option['selected']){ 
				$val = $option['value'];
			}
			if ( $val == $option['value'] ) { 
				$class = 'selected';
			} else {
				$class = '';	
			}
			$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';	
		}
		$output .= '<input type="hidden" name="setting-default_page_layout" class="val" value="'.$val.'" /></p>';
		$output .= '<p>
						<span class="label">' . __('Hide Title in All Pages', 'themify') . '</span>
						
						<select name="setting-hide_page_title">';
							foreach ( $default_options as $title_option ) {
								if ( isset( $data['setting-hide_page_title'] ) && ( $title_option['value'] == $data['setting-hide_page_title'] ) ) {
									$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
								} else {
									$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
								}
							}
		$output .=	'	</select>
					</p>';

		// Disable page comments
		$pages_checked = '';
		if ( isset( $data['setting-comments_pages'] ) && $data['setting-comments_pages'] ) {
			$pages_checked = 'checked="checked"';	
		}
		$output .= '<p><span class="label">' . __('Page Comments', 'themify') . '</span><label for="setting-comments_pages"><input type="checkbox" id="setting-comments_pages" name="setting-comments_pages" '.checked( themify_get( 'setting-comments_pages' ), 'on', false ).' /> ' . __('Disable comments in all Pages', 'themify') . '</label></p>';	
		
		return $output;													 
	}
}

if(!function_exists('themify_default_layout')){
	/**
	 * Default Index Layout Module - Action
	 * @param array $data
	 * @return string
	 */
	function themify_default_layout($data=array()){
		$data = themify_get_data();
		$prefix = 'setting-default_';
		if ( ! isset( $data['setting-default_more_text'] ) || '' == $data['setting-default_more_text'] ) {
			$more_text = __('More', 'themify');
		} else {
			$more_text = $data['setting-default_more_text'];
		}
		$default_options = array(
			array('name'=>'','value'=>''),
			array('name'=>__('Yes', 'themify'),'value'=>'yes'),
			array('name'=>__('No', 'themify'),'value'=>'no')
		);
		$media_position = array(
			array('name'=>__('Above Post Title', 'themify'), 'value'=>'above'),
			array('name'=>__('Below Post Title', 'themify'), 'value'=>'below')
		);
		$default_layout_options = array(
			array('name' => __('Full Content', 'themify'),'value'=>'content'),
			array('name' => __('Excerpt', 'themify'),'value'=>'excerpt'),
			array('name' => __('None', 'themify'),'value'=>'none')
		);	
		$default_post_layout_options = array(
			array('value' => 'list-post', 'img' => 'images/layout-icons/list-post.png', 'title' => __('List Post', 'themify'), "selected" => true),
			array('value' => 'grid4', 'img' => 'images/layout-icons/grid4.png', 'title' => __('Grid 4', 'themify')),
			array('value' => 'grid3', 'img' => 'images/layout-icons/grid3.png', 'title' => __('Grid 3', 'themify')),
			array('value' => 'grid2', 'img' => 'images/layout-icons/grid2.png', 'title' => __('Grid 2', 'themify')),
			array('value' => 'grid2-thumb', 'img' => 'images/layout-icons/grid2-thumb.png', 'title' => __('Grid 2 Thumb', 'themify'))
		);
		$options = array(
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar', 'themify'), "selected" => true),
		);
						 
		$val = isset( $data['setting-default_layout'] ) ? $data['setting-default_layout'] : '';
		
		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<div class="themify-info-link">' . __( 'Here you can set the <a href="http://themify.me/docs/default-layouts">Default Layouts</a> for WordPress archive post layout (category, search, archive, tag pages, etc.), single post layout (single post page), and the static Page layout. The default single post and page layout can be override individually on the post/page > edit > Themify Custom Panel.', 'themify' ) . '</div>';

		$output .= '<p>
						<span class="label">' . __('Index Sidebar Option', 'themify') . '</span>';
		foreach ( $options as $option ) {
			if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) { 
				$val = $option['value'];
			}
			if ( $val == $option['value'] ) { 
				$class = "selected";
			} else {
				$class = "";	
			}
			$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';	
		}
		
		$output .= '<input type="hidden" name="setting-default_layout" class="val" value="'.$val.'" />';
		$output .= '</p>';
		$output .= '<p>
						<span class="label">' . __('Post Layout', 'themify') . '</span>';
						
		$val = isset( $data['setting-default_post_layout'] ) ? $data['setting-default_post_layout'] : '';
		
		foreach ( $default_post_layout_options as $option ) {
			if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) { 
				$val = $option['value'];
			}
			if ( $val == $option['value'] ) { 
				$class = "selected";
			} else {
				$class = "";	
			}
			$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';	
		}
		
		$output .= '<input type="hidden" name="setting-default_post_layout" class="val" value="'.$val.'" />
					</p>
					<p>
						<span class="label">' . __('Display Content', 'themify') . '</span> 
						<select name="setting-default_layout_display">';
						foreach ( $default_layout_options as $layout_option ) {
							if ( isset( $data['setting-default_layout_display'] ) && ( $layout_option['value'] == $data['setting-default_layout_display'] ) ) {
								$output .= '<option selected="selected" value="'.$layout_option['value'].'">'.$layout_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$layout_option['value'].'">'.$layout_option['name'].'</option>';
							}
						}
		$output .=	'	</select>
					</p>';
		
		/**
		 * More Text
		 */
		$output .= '<p>
						<span class="label">' . __('More Text', 'themify') . '</span>
						<input type="text" name="setting-default_more_text" value="'.$more_text.'">

						<span class="pushlabel vertical-grouped"><label for="setting-excerpt_more"><input type="checkbox" value="1" id="setting-excerpt_more" name="setting-excerpt_more" '.checked( themify_get( 'setting-excerpt_more' ), 1, false ).'/> ' . __('Display more link button in excerpt mode as well.', 'themify') . '</label></span>
					</p>';
					
		/**
		 * Order & OrderBy Options
		 */
		$output .= themify_post_sorting_options('setting-index_order', $data);
		
		/**
		 * Hide Post Title
		 */
		$output .=	'<p>
						<span class="label">' . __('Hide Post Title', 'themify') . '</span>
						
						<select name="setting-default_post_title">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_post_title'] ) && ( $title_option['value'] == $data['setting-default_post_title'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'	</select>
					</p>';
		
		$output .=	'<p>
						<span class="label">' . __('Unlink Post Title', 'themify') . '</span>
						
						<select name="setting-default_unlink_post_title">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_unlink_post_title'] ) && ( $title_option['value'] == $data['setting-default_unlink_post_title'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>';
		
		// Hide Post Meta /////////////////////////////////////////
		$output .= themify_post_meta_options('setting-default_post_meta', $data);
					
		/////////////////////////////////////////
		// Hide Post Date
		/////////////////////////////////////////
		$output .=	'<p>
						<span class="label">' . __('Hide Post Date', 'themify') . '</span>
						
						<select name="setting-default_post_date">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_post_date'] ) && ( $title_option['value'] == $data['setting-default_post_date'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'	</select>
					</p>
					
					<p>
						<span class="label">' . __('Auto Featured Image', 'themify') . '</span>

						<label for="setting-auto_featured_image"><input type="checkbox" value="1" id="setting-auto_featured_image" name="setting-auto_featured_image" '.checked( themify_get( 'setting-auto_featured_image' ), 1, false ).'/> ' . __('If no featured image is specified, display first image in content.', 'themify') . '</label>
					</p>';
					
		$output .= '<p>
						<span class="label">' . __('Media Position', 'themify') . '</span>
						<select name="'.$prefix.'media_position">' .
							themify_options_module($media_position, $prefix.'media_position') . '
						</select>
					</p>';
		
		$output .= '<p>
						<span class="label">' . __('Hide Featured Image', 'themify') . '</span>

						<select name="setting-default_post_image">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_post_image'] ) && ( $title_option['value'] == $data['setting-default_post_image'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>
					<p>
						<span class="label">' . __('Unlink Featured Image', 'themify') . '</span>
						
						<select name="setting-default_unlink_post_image">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_unlink_post_image'] ) && ( $title_option['value'] == $data['setting-default_unlink_post_image'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p>';
		
		$output .= themify_feature_image_sizes_select('image_post_feature_size');
		$data = themify_get_data();
		$options = array( 'left', 'right');

		$output .= '<p>
						<span class="label">' . __('Image Size', 'themify') . '</span>  
						<input type="text" class="width2" name="setting-image_post_width" value="' . themify_get( 'setting-image_post_width' ) . '" /> ' . __('width', 'themify') . ' <small>(px)</small>  
						<input type="text" class="width2" name="setting-image_post_height" value="' . themify_get( 'setting-image_post_height' ) . '" /> ' . __('height', 'themify') . ' <small>(px)</small>
						<br /><span class="pushlabel"><small>' . __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify') . '</small></span>
					</p>
					<p>
						<span class="label">' . __('Featured Image Alignment', 'themify') . '</span>
						<select name="setting-image_post_align">
							<option></option>';
		foreach ( $options as $option ) {
			if ( isset( $data['setting-image_post_align'] ) && ( $option == $data['setting-image_post_align'] ) ) {
				$output .= '<option value="'.$option.'" selected="selected">'.$option.'</option>';
			} else {
				$output .= '<option value="'.$option.'">'.$option.'</option>';
			}
		}
		$output .=	'	</select>
					</p>';
					
		return $output;
	}
}

if(!function_exists('themify_default_post_layout')){
	/**
	 * Default post layout settings module
	 * @param array $data
	 * @return string
	 */
	function themify_default_post_layout($data=array()){
		
		$data = themify_get_data();
		
		$default_options = array(
			array('name'=>'', 'value'=>''),
			array('name'=>__('Yes', 'themify'), 'value'=>'yes'),
			array('name'=>__('No', 'themify'),  'value'=>'no')
		);
		$media_position = array(
			array('name'=>__('Above Post Title', 'themify'), 'value'=>'above'),
			array('name'=>__('Below Post Title', 'themify'), 'value'=>'below')
		);
		
		$val = isset( $data['setting-default_page_post_layout'] ) ? $data['setting-default_page_post_layout'] : '';

		$output = '<p>
						<span class="label">' . __('Post Sidebar Option', 'themify') . '</span>';
						
		$options = array(
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify'), 'selected' => true),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar', 'themify')),
		);
										
		foreach ( $options as $option ) {
			if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) { 
				$val = $option['value'];
			}
			if ( $val == $option['value'] ) { 
				$class = "selected";
			} else {
				$class = '';
			}
			$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';	
		}
		
		$output .= '<input type="hidden" name="setting-default_page_post_layout" class="val" value="'.$val.'" />
					</p>';
		
		$output .= '<div class="themify_field_row">
						<span class="label">' . __('Hide Post Title', 'themify') . '</span>
						
						<select name="setting-default_page_post_title">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_post_title'] ) && ( $title_option['value'] == $data['setting-default_page_post_title'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</div>
					<div class="themify_field_row">
						<span class="label">' . __('Unlink Post Title', 'themify') . '</span>
						
						<select name="setting-default_page_unlink_post_title">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_unlink_post_title'] ) && ( $title_option['value'] == $data['setting-default_page_unlink_post_title'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</div>';

		// Hide Post Meta /////////////////////////////////////////
		$output .= themify_post_meta_options('setting-default_page_post_meta', $data);
		$output .= '<p>
						<span class="label">' . __('Hide Post Date', 'themify') . '</span>
						
						<select name="setting-default_page_post_date">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_post_date'] ) && ( $title_option['value'] == $data['setting-default_page_post_date'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
					
		$output .= '</select>
					</p>
					<p>
						<span class="label">' . __('Media Position', 'themify') . '</span>
						<select name="setting-default_page_post_media_position">' .
							themify_options_module($media_position, 'setting-default_page_post_media_position') . '
						</select>
					</p>';
					
		$output .=	'<p>
						<span class="label">' . __('Hide Featured Image', 'themify') . '</span>
						
						<select name="setting-default_page_post_image">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_post_image'] ) && ( $title_option['value'] == $data['setting-default_page_post_image'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .=	'</select>
					</p><p>
						<span class="label">' . __('Unlink Featured Image', 'themify') . '</span>
						
						<select name="setting-default_page_unlink_post_image">';
						foreach ( $default_options as $title_option ) {
							if ( isset( $data['setting-default_page_unlink_post_image'] ) && ( $title_option['value'] == $data['setting-default_page_unlink_post_image'] ) ) {
								$output .= '<option selected="selected" value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							} else {
								$output .= '<option value="'.$title_option['value'].'">'.$title_option['name'].'</option>';
							}
						}
		$output .= '</select></p>';
		$output .= themify_feature_image_sizes_select('image_post_single_feature_size');
		$output .= '<p>
				<span class="label">' . __('Image Size', 'themify') . '</span>
						<input type="text" class="width2" name="setting-image_post_single_width" value="' . themify_get( 'setting-image_post_single_width' ) . '" /> ' . __('width', 'themify') . ' <small>(px)</small>  
						<input type="text" class="width2" name="setting-image_post_single_height" value="' . themify_get( 'setting-image_post_single_height' ) . '" /> ' . __('height', 'themify') . ' <small>(px)</small>
						<br /><span class="pushlabel"><small>' . __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify') . '</small></span>
					</p>
					<p>
						<span class="label">' . __('Featured Image Alignment', 'themify') . '</span>
						<select name="setting-image_post_single_align">
							<option></option>';
		$options = array( 'left', 'right' );
		foreach ( $options as $option ) {
			if ( isset( $data['setting-image_post_single_align'] ) && ( $option == $data['setting-image_post_single_align'] ) ) {
				$output .= '<option value="'.$option.'" selected="selected">'.$option.'</option>';
			} else {
				$output .= '<option value="'.$option.'">'.$option.'</option>';
			}
		}
		$output .=	'</select>
				</p>';
		
		// Disable post comments
		$comments_posts_checked = '';	
		if ( themify_check( 'setting-comments_posts' ) ) {
			$comments_posts_checked = 'checked="checked"';	
		}
		$output .= '<p><span class="label">' . __('Post Comments', 'themify') . '</span><label for="setting-comments_posts"><input type="checkbox" id="setting-comments_posts" name="setting-comments_posts" '.checked( themify_get( 'setting-comments_posts' ), 'on', false ).' /> ' . __('Disable comments in all Posts', 'themify') . '</label></p>';	
		
		// Show author box
		$author_box_checked = '';
		if ( themify_check( 'setting-post_author_box' ) ) {
			$author_box_checked = 'checked="checked"';	
		}
		$output .= '<p><span class="label">' . __('Author Box', 'themify') . '</span><label for="setting-post_author_box"><input type="checkbox" id="setting-post_author_box" name="setting-post_author_box" '.checked( themify_get( 'setting-post_author_box' ), 'on', false ).' /> ' . __('Show author box in all Posts', 'themify') . '</label></p>';
		
		// Post Navigation
		$pre = 'setting-post_nav_';
		$output .= '
			<p>
				<span class="label">' . __('Post Navigation', 'themify') . '</span>
				<label for="'.$pre.'disable">
					<input type="checkbox" id="'.$pre.'disable" name="'.$pre.'disable" '. checked( themify_get( $pre.'disable' ), 'on', false ) .'/> ' . __('Remove Post Navigation', 'themify') . '
				</label>
				<span class="pushlabel vertical-grouped">
				<label for="'.$pre.'same_cat">
					<input type="checkbox" id="'.$pre.'same_cat" name="'.$pre.'same_cat" '. checked( themify_get( $pre.'same_cat' ), 'on', false ) .'/> ' . __('Show only posts in the same category', 'themify') . '
				</label>
				</span>
			</p>';
			
		return $output;
	}
}

if(!function_exists('themify_scrolling_effect')){
	/**
	 * Add scrolling effect option
	 * @return string
	 */
	function themify_scrolling_effect() {
		$data = themify_get_data();
		$prefix = 'setting-scrolling_effect_';
		$mobile_checked = '';
		$disabled_checked = '';
		
		if ( themify_check( $prefix.'mobile_exclude' ) ) {
			$mobile_checked = "checked='checked'";
		}
		if ( themify_check( $prefix.'all_disabled' ) ) {
			$disabled_checked = "checked='checked'";
		}

		$mobile_options = array(
			array('name' => __('Off', 'themify'),'value'=>'off'),
			array('name' => __('On', 'themify'),'value'=>'on')
		);

		$output = '<p>
						<span class="label" style="width:400px;">' . __('Turn off parallax scrolling on mobile/tablet for better performance', 'themify') . '</span> 
						<select name="'.$prefix.'mobile_exclude">';
						foreach($mobile_options as $mobile_option){
							$output .= '<option '.selected( themify_get( $prefix.'mobile_exclude' ), $mobile_option['value'], false ).' value="'.$mobile_option['value'].'">'.$mobile_option['name'].'</option>';
						}
		$output .=	'	</select>
					</p>';
					
		$easing_options = array(
			array('name' => __('Linear', 'themify'), 'value' => 'linear'),
			array('name' => __('Quadratic', 'themify'), 'value' => 'quadratic'),
			array('name' => __('Cubic', 'themify'), 'value' => 'cubic'),
			array('name' => __('Slow to Fast', 'themify'), 'value' => 'swing'),
			array('name' => __('Fast to Slow', 'themify'), 'value' => 'sqrt'),
			array('name' => __('Bounce', 'themify'), 'value' => 'bounce')
		);
		
		$output .= '<p>
						<span class="label" style="width: 400px;">' . __('Easing method for scroll', 'themify') . '</span>
						<select name="' . $prefix . 'easing">';
						foreach($easing_options as $easing_option) {
							$output .= '<option ' . selected( themify_get( $prefix.'easing' ), $easing_option['value'], false ) . ' value="' . $easing_option['value'] . '">' . $easing_option['name'] . '</option>';					
						}
		$output .= '	</select>
					</p>';

		$output .= '<p><label for="'.$prefix.'all_disabled"><input type="checkbox" id="'.$prefix.'all_disabled" name="'.$prefix.'all_disabled" '.$disabled_checked.'/> ' . __('Disable all parallax scrolling effect', 'themify') . '</label></p>';
		return $output;
	}
}

if(!function_exists('themify_transition_effect')){
	/**
	 * Add transition effect
	 * FlyIn/FadeIn/disabled
	 * @return string
	 */
	function themify_transition_effect() {
		$data = themify_get_data();
		$prefix = 'setting-transition_effect_';
		$fadein_checked = '';
		$mobile_checked = '';
		$disabled_checked = '';

		$mobile_options = array(
			array('name' => __('Off', 'themify'),'value'=>'off'),
			array('name' => __('On', 'themify'),'value'=>'on')
		);

		if ( themify_check( $prefix.'fadein' ) ) {
			$fadein_checked = "checked='checked'";	
		}
		if ( themify_check( $prefix.'mobile_exclude' ) ) {
			$mobile_checked = "checked='checked'";
		}
		if ( themify_check( $prefix.'all_disabled' ) ) {
			$disabled_checked = "checked='checked'";
		}

		$output = '<p>
						<span class="label" style="width:400px;">' . __('Turn off fly-in animation on mobile/tablet for better performance', 'themify') . '</span> 
						<select name="'.$prefix.'mobile_exclude">';
						foreach($mobile_options as $mobile_option){
							$output .= '<option '.selected( themify_get( $prefix.'mobile_exclude' ), $mobile_option['value'], false ).' value="'.$mobile_option['value'].'">'.$mobile_option['name'].'</option>';
						}
		$output .=	'	</select>
					</p>';

		$output .= '<p><label for="'.$prefix.'fadein"><input type="checkbox" id="'.$prefix.'fadein" name="'.$prefix.'fadein" '.$fadein_checked.'/> ' . __('Use fade-in transition effect instead of fly-in', 'themify') . '</label></p>';
		$output .= '<p><label for="'.$prefix.'all_disabled"><input type="checkbox" id="'.$prefix.'all_disabled" name="'.$prefix.'all_disabled" '.$disabled_checked.'/> ' . __('Disable all transition effect', 'themify') . '</label></p>';

		return $output;
	}
}

?>