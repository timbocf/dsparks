<?php
/**
 * Theme Classes
 *
 * Classes that provides special functions for the theme front end and admin.
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Load base shortcode class
require_once( 'theme-class-types.php' );

// Load shortcode classes
foreach ( array( 'highlight', 'team', 'portfolio', 'section' ) as $type ) {
	require_once ( 'theme-class-'.$type.'.php' );
}

if ( ! class_exists( 'Themify_ThemeClass' ) ) {
/**
 * Themify_ThemeClass
 * Class for theme front end
 *
 * @class 		Themify_ThemeClass
 * @author 		Themify
 */
class Themify_ThemeClass {
	
	// Custom Post Types
	static $section = 'section';
	static $portfolio = 'portfolio';
	static $highlight = 'highlight';
	static $team = 'team';

	public $google_fonts = '';
	
	function __construct() {

		add_action( 'init', array($this, 'register_post_types') );
		add_action( 'init', array($this, 'register_custom_taxonomies') );
		add_filter( 'themify_post_types', array($this, 'extend_post_types') );
		add_filter( 'themify_default_social_links', array($this, 'themify_default_social_links') );
		add_filter( 'themify_get_featured_image_link', array($this, 'porto_expand_link') );
		
		add_action( 'admin_init', array($this, 'admin_init') );
		add_action( 'themify_post_end', array($this, 'custom_post_css') );
		add_action( 'save_post', array($this, 'set_default_term'), 100, 2 );
		add_action( 'template_redirect', array($this, 'porto_expand_content'), 10 );

		add_action( 'wp_footer', array($this, 'enqueue_google_fonts') );

		//Create shortcode
		$cpts = array(
			// Portfolio shortcode parameters
			self::$portfolio => array(
				'id' => '',
				'title' => '',
				'unlink_title' => '',
				'unlink_image' => '',
				'image' => 'yes', // no
				'image_w' => '',
				'image_h' => '',
				'display' => 'none', // excerpt, content
				'post_meta' => '', // yes
				'post_date' => '', // yes
				'more_link' => false, // true goes to post type archive, and admits custom link
				'more_text' => __('More &rarr;', 'themify'),
				'limit' => 4,
				'category' => 'all', // integer category ID
				'order' => 'DESC', // ASC
				'orderby' => 'date', // title, rand
				'style' => '', // grid4, grid3, grid2
				'sorting' => 'no', // yes
				'paged' => '0', // internal use for pagination
			),
			// Highlight shortcode parameters
			self::$highlight => array(
				'id' => '',
				'title' => 'yes', // no
				'image' => 'yes', // no
				'image_w' => 125,
				'image_h' => 125,
				'display' => 'content', // excerpt, none
				'more_link' => false, // true goes to post type archive, and admits custom link
				'more_text' => __('More &rarr;', 'themify'),
				'limit' => 6,
				'category' => 'all', // integer category ID
				'order' => 'DESC', // ASC
				'orderby' => 'date', // title, rand
				'style' => 'grid3', // grid4, grid2, list-post
				'section_link' => false // true goes to post type archive, and admits custom link
			),
			// Team shortcode parameters
			self::$team => array(
				'id' => '',
				'title' => 'yes', // no
				'image' => 'yes', // no
				'image_w' => 150,
				'image_h' => 150,
				'unlink_title' => '',
				'unlink_image' => '',
				'display' => 'content', // excerpt, none
				'more_link' => false, // true goes to post type archive, and admits custom link
				'more_text' => __('More &rarr;', 'themify'),
				'limit' => 4,
				'category' => 'all', // integer category ID
				'order' => 'DESC', // ASC
				'orderby' => 'date', // title, rand
				'style' => 'grid4', // grid3, grid2, list-post
				'section_link' => false, // true goes to post type archive, and admits custom link
				'use_original_dimensions' => 'no'
			),
			self::$section => array(
				'id' => '',
				'category' => 'all'
			)
		);
		foreach ($cpts as $shortcode => $options) {
			$class_name = apply_filters('themify_theme_class_shortcodes', 'Themify_' . ucwords($shortcode));
			$new_class = new $class_name(array(
				'post_type' => $shortcode,
				'atts' => $options
			));
		}
	}
	
	/**
	 * Initialize general admin functions
	 */
	function admin_init($hook) {
		if('section' == themify_get_current_post_type()){
			add_action( 'edit_form_advanced', array(&$this, 'themify_subtitle_field') );
			add_action( 'save_post', array(&$this, 'themify_subtitle_meta_save') );
			add_action( 'admin_enqueue_scripts', array(&$this, 'themify_subtitle_script_style') );
		}
		add_filter( 'get_sample_permalink_html', array($this, 'hide_view_post'), '', 4 );
	}

	/**
	 * Hides View Section/Team/Highlight button in edit screen
	 * @param string $return
	 * @param string $id
	 * @param string $new_title
	 * @param string $new_slug
	 * @return string Markup without the button
	 */
	function hide_view_post($return, $id, $new_title, $new_slug){
		global $post;
		if(in_array($post->post_type, array(self::$section, self::$highlight, self::$team))) {
			return preg_replace('/<span id=\'view-post-btn\'>.*<\/span>/i', '', $return);;
		} else {
			return $return;
		}
	}
	
	function themify_subtitle_field(){
		wp_nonce_field( 'themify_subtitle', 'themify_subtitle_nonce');
		global $post;
		$post_id = isset($_GET['post'])? $_GET['post']: $post->ID;
		$label = __('Enter subtitle here', 'themify');
		if($val = get_post_meta($post_id, 'subtitle', true)) {
			$text = $val;
		} else {
			$text = '';
		}
		echo "<span id='themify-subtitle-group'><label id='subtitle-prompt-text' for='themify_subtitle' class='screen-reader-text'>$label</label><input type='text' name='subtitle' value='$text' id='themify_subtitle'/></span>";
	}

	function themify_subtitle_meta_save($post_id){
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if (!isset($_POST['themify_subtitle_nonce']) || !wp_verify_nonce( $_POST['themify_subtitle_nonce'], 'themify_subtitle' ) ) return;
		update_post_meta($post_id, 'subtitle', esc_html( $_POST['subtitle'] ));
		return;
	}


	function themify_subtitle_script_style() {
		wp_enqueue_style( 'themify-admin-style', THEME_URI . '/admin/css/admin-style.css' );
		wp_enqueue_script( 'themify-admin-script', THEME_URI . '/admin/js/admin-script.js' );
	}

	/**
	 * Set default term for custom taxonomy and assign to post
	 * @param number
	 * @param object
	 */
	function set_default_term($post_id, $post) {
		if ('publish' === $post->post_status) {
			$defaults = array(
				'section-category' => array(__('Uncategorized', 'themify')),
				'portfolio-category' => array(__('Uncategorized', 'themify'))
			);
			foreach (get_object_taxonomies($post->post_type) as $taxonomy) {
				$terms = wp_get_post_terms($post_id, $taxonomy);
				if (empty($terms) && array_key_exists($taxonomy, $defaults)) {
					wp_set_object_terms($post_id, $defaults[$taxonomy], $taxonomy);
				}
			}
		}
	}
	
	/**
	 * Register custom post types
	 */
	function register_post_types() {
		/**
		 * @var array Custom Post Types to create with its plural and singular forms
		 */
		$cpts = array(
			self::$section => array(
				'plural' => __('Sections', 'themify'),
				'singular' => __('Section', 'themify'),
				'supports' => array('title', 'editor', 'author', 'custom-fields'),
				'show_in_nav_menus' => false,
			),
			self::$portfolio => array(
				'plural' => __('Portfolios', 'themify'),
				'singular' => __('Portfolio', 'themify'),
				'rewrite' => themify_check('themify_portfolio_slug')? themify_get('themify_portfolio_slug') : apply_filters('themify_portfolio_rewrite', 'project')
			),
			self::$highlight => array(
				'plural' => __('Highlights', 'themify'),
				'singular' => __('Highlight', 'themify'),
			),
			self::$team => array(
				'plural' => __('Teams', 'themify'),
				'singular' => __('Team', 'themify'),
				'rewrite' => themify_check('themify_team_slug')? themify_get('themify_team_slug') : apply_filters('themify_team_rewrite', 'team')
			)
		);
		$position = 52;
		foreach( $cpts as $key => $cpt ){
			register_post_type( $key, array(
				'labels' => array(
					'name' => $cpt['plural'],
					'singular_name' => $cpt['singular'],
					'add_new' => __( 'Add New', 'themify' ),
					'add_new_item' => sprintf(__( 'Add New %s', 'themify' ), $cpt['singular']),
					'edit_item' => sprintf(__( 'Edit %s', 'themify' ), $cpt['singular']),
					'new_item' => sprintf(__( 'New %s', 'themify' ), $cpt['singular']),
					'view_item' => sprintf(__( 'View %s', 'themify' ), $cpt['singular']),
					'search_items' => sprintf(__( 'Search %s', 'themify' ), $cpt['plural']),
					'not_found' => sprintf(__( 'No %s found', 'themify' ), $cpt['plural']),
					'not_found_in_trash' => sprintf(__( 'No %s found in Trash', 'themify' ), $cpt['plural']),
					'menu_name' => $cpt['plural']
				),
				'supports' => isset($cpt['supports'])? $cpt['supports'] : array('title', 'editor', 'thumbnail', 'custom-fields', 'excerpt'),
				'menu_position' => $position++,
				'hierarchical' => false,
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => isset( $cpt['show_in_nav_menus'] ) ? $cpt['show_in_nav_menus'] : true,
				'publicly_queryable' => true,
				'rewrite' => array( 'slug' => isset($cpt['rewrite'])? $cpt['rewrite']: strtolower($cpt['singular']) ),
				'query_var' => true,
				'can_export' => true,
				'capability_type' => 'post'
			));
		}
	}

	/**
	 * Register custom taxonomies
	 */
	function register_custom_taxonomies() {
		/**
		 * @var array Custom Post Types to create with its plural and singular forms
		 */
		$cpts = array(
			self::$section => array(
				'plural' => __('Sections', 'themify'),
				'singular' => __('Section', 'themify'),
				'supports' => array('title', 'editor', 'author', 'custom-fields'),
				'menus' => false,
			),
			self::$portfolio => array(
				'plural' => __('Portfolios', 'themify'),
				'singular' => __('Portfolio', 'themify'),
				'rewrite' => themify_check('themify_portfolio_slug')? themify_get('themify_portfolio_slug') : apply_filters('themify_portfolio_rewrite', 'project')
			),
			self::$highlight => array(
				'plural' => __('Highlights', 'themify'),
				'singular' => __('Highlight', 'themify'),
			),
			self::$team => array(
				'plural' => __('Teams', 'themify'),
				'singular' => __('Team', 'themify'),
				'rewrite' => themify_check('themify_team_slug')? themify_get('themify_team_slug') : apply_filters('themify_team_rewrite', 'team')
			)
		);
		foreach( $cpts as $key => $cpt ){
			register_taxonomy( $key.'-category', array($key), array(
				'labels' => array(
					'name' => sprintf(__( '%s Categories', 'themify' ), $cpt['singular']),
					'singular_name' => sprintf(__( '%s Category', 'themify' ), $cpt['singular']),
					'search_items' => sprintf(__( 'Search %s Categories', 'themify' ), $cpt['singular']),
					'popular_items' => sprintf(__( 'Popular %s Categories', 'themify' ), $cpt['singular']),
					'all_items' => sprintf(__( 'All Categories', 'themify' ), $cpt['singular']),
					'parent_item' => sprintf(__( 'Parent %s Category', 'themify' ), $cpt['singular']),
					'parent_item_colon' => sprintf(__( 'Parent %s Category:', 'themify' ), $cpt['singular']),
					'edit_item' => sprintf(__( 'Edit %s Category', 'themify' ), $cpt['singular']),
					'update_item' => sprintf(__( 'Update %s Category', 'themify' ), $cpt['singular']),
					'add_new_item' => sprintf(__( 'Add New %s Category', 'themify' ), $cpt['singular']),
					'new_item_name' => sprintf(__( 'New %s Category', 'themify' ), $cpt['singular']),
					'separate_items_with_commas' => sprintf(__( 'Separate %s Category with commas', 'themify' ), $cpt['singular']),
					'add_or_remove_items' => sprintf(__( 'Add or remove %s Category', 'themify' ), $cpt['singular']),
					'choose_from_most_used' => sprintf(__( 'Choose from the most used %s Category', 'themify' ), $cpt['singular']),
					'menu_name' => sprintf(__( '%s Category', 'themify' ), $cpt['singular']),
				),
				'public' => true,
				'show_in_nav_menus' => isset( $cpt['menus'] ) ? $cpt['menus'] : true,
				'show_ui' => true,
				'show_tagcloud' => true,
				'hierarchical' => true,
				'rewrite' => true,
				'query_var' => true
			));
			add_filter('manage_edit-'.$key.'-category_columns', array(&$this, 'taxonomy_header'), 10, 2);
			add_filter('manage_'.$key.'-category_custom_column', array(&$this, 'taxonomy_column_id'), 10, 3);
		}
	}

	/**
	 * Includes new post types registered in theme to array of post types managed by Themify
	 * @param Array
	 * @return Array
	 */
	function extend_post_types($types){
		return array_merge($types, $this->types_registered());
	}
	
	/**
	 * Returns new post types registered in theme
	 * @return Array
	 */
	function types_registered(){
		return array(self::$section, self::$portfolio, self::$highlight, self::$team);
	}
	
	/**
	 * Display an additional column in categories list
	 * @since 1.0.0
	 */
	function taxonomy_header($cat_columns){
	    $cat_columns['cat_id'] = 'ID';
	    return $cat_columns;
	}
	/**
	 * Display ID in additional column in categories list
	 * @since 1.0.0
	 */
	function taxonomy_column_id($null, $column, $termid){
		return $termid;
	}
	
	/**
	 * Displays the data-bg and class attributes, including the background image and fullcover class
	 * @param string $classes Additional CSS classes to output for this element
	 */
	function custom_header_background( $classes = '' ) {
		$class = '';
		$image = null;
		$repeat = '';
		$data_bg = '';
		$post_id = get_the_id();
		$nobg = '';

		$back_key = 'styling-background-header_wrap_background-background_image-value-value';
		$mode_key = 'styling-background-header_wrap_background-background_mode-value-value';
		$nobg_key = 'styling-background-header_wrap_background-background_image-value-none';
		$header_customizer = json_decode( get_theme_mod( 'headerwrap_background' ) );

		if( isset( $header_customizer->src ) && '' != $header_customizer->src ) {
			$image = $header_customizer->src;
			$repeat = isset( $header_customizer->style ) ? $header_customizer->style : 'fullcover';
		} elseif( themify_check( $back_key ) ) {
			$image = themify_get( $back_key );
			$repeat = themify_check( $mode_key )? themify_get( $mode_key ): 'fullcover';
		}
		$nobg = themify_get( $nobg_key ) || ( isset( $header_customizer->noimage ) && 'noimage' == $header_customizer->noimage );
		
		if( is_page() || is_singular( 'post' ) || is_singular( 'portfolio' ) ) {
			global $themify;

			if ( is_page() ) {
				$post_id = $themify->page_id;
			} else {
				$post_id = get_the_id();
			}

			$image_meta = get_post_meta($post_id, 'background_image', true);
			if( $image_meta ) {
				$image = $image_meta;
				$repeat_meta = get_post_meta($post_id, 'background_repeat', true);
				if( $repeat_meta ) {
					$repeat = $repeat_meta;
				}
			}
		}

		if ( themify_is_woocommerce_active() && is_shop() ) {
			$post_id = get_option( 'woocommerce_shop_page_id' );
			$image_meta = get_post_meta($post_id, 'background_image', true);
			if( $image_meta ) {
				$image = $image_meta;
				$repeat_meta = get_post_meta($post_id, 'background_repeat', true);
				if( $repeat_meta ) {
					$repeat = $repeat_meta;
				}
			}
		}

		if( $gallery = ((get_post_meta( $post_id, 'background_type', true ) == 'slider') || (get_post_meta( $post_id, 'background_type', true ) == '' && get_post_meta( $post_id, 'background_gallery', true ) != '')) ) {
			$repeat = 'fullcover header-gallery';
		} elseif( $nobg ) {
			$data_bg = '';
		} elseif( $image ) {
			$data_bg = "data-bg='$image'";
		} else {
			$data_bg = 'data-bg="http://themify.me/demo/themes/wp-content/uploads/2013/05/header.jpg"';
			$repeat = 'fullcover';
		}

		if( $video = get_post_meta($post_id, 'video_file', true) ) {
			$data_bg .= " data-fullwidthvideo='$video'";
		}

		if( $repeat || $classes ) {
			$class = "class='$repeat $classes'";
		}
		
		echo "$data_bg $class";
	}
	
	function section_background($classes = '') {
		$post_class = get_post_class();
		$post_id = get_the_ID();
		$out = '';
		$class = '';
		$class = join( ' ', get_post_class($classes) );
		$data_bg = '';
		$image = get_post_meta($post_id, 'background_image', true);
		if($image){
			$data_bg = "data-bg='$image'"; 
			$repeat = get_post_meta($post_id, 'background_repeat', true);
			if($repeat){
				$class .= " $repeat";
			}
		}
		if('' != $data_bg) {
			$out .= $data_bg;
		}
		if('' != $class) {
			$out .= " class='$class'";
		}
		echo $out;
	}
	
	function custom_post_css() {
		$post_id = get_the_ID();
		$css = array();
		$style = '';
		$rules = array(
			'.section-post.post-'.$post_id => array(
				array(	'prop' => 'font-size',
						'key' => array('font_size', 'font_size_unit')
				),
				array(	'prop' => 'font-family',
						'key' => 'font_family'
				),
				array(	'prop' => 'color',
						'key' => 'font_color'
				),
				array(	'prop' => 'background-color',
						'key' => 'background_color'
				)
			),
			'.section-post.post-'.$post_id.' a' => array(
				array(	'prop' => 'color',
						'key' => 'link_color'
				)
			)
		);
		foreach ($rules as $selector => $property) {
			foreach ($property as $val) {
				$prop = $val['prop'];
				$key = $val['key'];
				if(is_array($key)) {
					if('font-size' == $prop && themify_check($key[0])){
						$css[$selector][$prop] = $prop .': '. themify_get($key[0]) . themify_get($key[1]);
					}
					if('background-position' == $prop && themify_check($key[0])){
						$css[$selector][$prop] = $prop .': '. themify_get($key[0]) . ' ' . themify_get($key[1]);
					}
				} elseif(themify_check($key) && 'default' != themify_get($key)) {
					if('color' == $prop || stripos($prop, 'color')) {
						$css[$selector][$prop] = $prop .': #'.themify_get($key);
					}
					elseif('background-image' == $prop) {
						$css[$selector][$prop] = $prop .': url('.themify_get($key).')';
					}
					elseif( $prop == 'font-family' ) {
						$font = themify_get($key);
						$css[$selector][$prop] = $prop .': '. $font;
						if( ! in_array( $font, themify_get_web_safe_font_list(true) ) ) {
							$this->google_fonts .= str_replace(' ', '+', $font.'|');
						}
					}
					else {
						$css[$selector][$prop] = $prop .': '. themify_get($key);
					}
				}
			}
			if(!empty($css[$selector])){
				$style .= "$selector {\n\t" . implode(";\n\t", $css[$selector]) . "\n}\n";
			}
		}

		if('' != $style){
			echo "\n<!-- #$post_id Style -->\n<style>\n$style</style>\n<!-- #$post_id Style -->\n";
		}
	}

	function enqueue_google_fonts() {
		if( '' == $this->google_fonts ) return;
		$this->google_fonts = substr($this->google_fonts, 0, -1);
		wp_enqueue_style('section-styling-google-fonts', themify_https_esc('http://fonts.googleapis.com/css'). '?family='.$this->google_fonts);
	}
	
	// Replace default squared social link icons with circular versions
	function themify_default_social_links($data) {
		$pre = 'setting-link_img_themify-link-';
		$data[$pre.'0'] = THEME_URI . '/images/social/twitter.png';
		$data[$pre.'1'] = THEME_URI . '/images/social/facebook.png';
		$data[$pre.'2'] = THEME_URI . '/images/social/google-plus.png';
		$data[$pre.'3'] = THEME_URI . '/images/social/youtube.png';
		$data[$pre.'4'] = THEME_URI . '/images/social/pinterest.png';
		return $data;
	}
	
	function show_image($post_image) {
		global $themify;
		?>

		<?php if( 'yes' == $themify->unlink_image) { ?>
			<?php echo $post_image; ?>
		<?php } else { ?>
			<a href="<?php echo themify_get_featured_image_link(); ?>">
				<?php if(themify_check('lightbox_icon')){ ?>
					<span class="zoom"></span>
				<?php } ?>
				<?php echo $post_image; ?>
			</a>
		<?php }
	}

	function show_video() {
		global $wp_embed;
		echo $wp_embed->run_shortcode('[embed]' . themify_get('video_url') . '[/embed]');
	}

	/**
	 * Returns post category IDs concatenated in a string
	 * @param number Post ID
	 * @return string Category IDs
	 */
	public function get_categories_as_classes($post_id) {
		$categories = wp_get_post_categories($post_id);
		$class = '';
		foreach($categories as $cat)
			$class .= ' cat-'.$cat;
		return $class;
	}
	 
	/**
	 * Returns category description
	 * @return string
	 */
	function get_category_description() {
	 	$category_description = category_description();
		if ( !empty( $category_description ) ){
			return '<div class="category-description">' . $category_description . '</div>';
		}
	}
	
	/**
	 * Returns all IDs from the given taxonomy
	 * @param string $tax Taxonomy to retrieve terms from.
	 * @return array $term_ids Array of all taxonomy terms
	 */
	function get_all_terms_ids($tax = 'category') {
		if ( ! $term_ids = wp_cache_get( 'all_'.$tax.'_ids', $tax ) ) {
			$term_ids = get_terms( $tax, array('fields' => 'ids', 'get' => 'all') );
			wp_cache_add( 'all_'.$tax.'_ids', $term_ids, $tax );
		}
		return $term_ids;
	}

	/**
	 * Returns the image for the portfolio slider
	 * @param int $attachment_id Image attachment ID
	 * @param int $width Width of the returned image
	 * @param int $height Height of the returned image
	 * @param string $size Size of the returned image
	 * @return string
	 * @since 1.1.0
	 */
	function portfolio_image($attachment_id, $width, $height, $size = 'large') {
		$size = apply_filters( 'themify_portfolio_image_size', $size );
		if ( themify_check( 'setting-img_settings_use' ) ) {
			// Image Script is disabled, use WP image
			$html = wp_get_attachment_image( $attachment_id, $size );
		} else {
			// Image Script is enabled, use it to process image
			$img = wp_get_attachment_image_src($attachment_id, $size);
			$html = themify_get_image('ignore=true&src='.$img[0].'&w='.$width.'&h='.$height);
		}
		return apply_filters( 'themify_portfolio_image_html', $html, $attachment_id, $width, $height, $size );
	}

	/**
	 * Add class and data attribute to portfolio section
	 * @param sting $link
	 * @return string
	 */
	function porto_expand_link($link) {
		global $post;
		if( ! is_singular( 'portfolio' ) && get_post_type() == 'portfolio' 
			&& themify_get('external_link') == '' && themify_get('lightbox_link') == '' 
			&& ! themify_check('setting-default_portfolio_index_disable_porto_expand') ) {
			$prefix_link = get_option('permalink_structure') != ''? '?' : '&';
			$link .= '" class="porto-expand-js" data-post-id="'.$post->ID.'" data-prefix-link="'.$prefix_link.'" data-post-type="'.get_post_type();
		}
		return $link;
	}

	/**
	 * Expand portfolio content with ajax
	 * @return void
	 */
	function porto_expand_content() {
		if( is_single() && isset( $_GET['porto_expand'] ) && $_GET['porto_expand'] == 1 ) {
			global $post, $wp, $wp_query;
			// remove admin bar inside iframe
			add_filter( 'show_admin_bar', '__return_false' );
			remove_action('wp_head', '_admin_bar_bump_cb');

			$templatefilename = 'single-portfolio-expand.php';

			$return_template = locate_template( $templatefilename );

			if ( have_posts() ) {
				include( $return_template );
				die();
			} else {
				$wp_query->is_404 = true;
			}
		}
	}
		
}// class end
}// end if class exists


?>