<?php
if (!class_exists('Themify_Types')) {
	/**
	 * Base class for type creation
	 */
	class Themify_Types {

		var $instance = 0;
		var $atts = array();
		var $post_type = '';
		static $run_once = true;

		function __construct($args = array()) {
			$defaults = array('post_type' => '');
			$args = wp_parse_args($args, $defaults);
			$this->post_type = $args['post_type'];

			$this->atts = $args['atts'];
			add_shortcode( $this->post_type, array( $this, 'init_shortcode' ) );
			add_shortcode( 'themify_'.$this->post_type.'_posts', array( $this, 'init_shortcode' ) );
			add_action('wp_enqueue_scripts', array($this, 'register_scripts_styles'));
			$this->manage_and_filter();
		}

		/**
		 * Overridable function that runs at the beginning of the shortcode output
		 */
		function shortcode_start() {
		}

		/**
		 * Initialization function
		 */
		function init() {

		}

		/**
		 * Add shortcode to WP
		 * @param $atts Array shortcode attributes
		 * @return String
		 * @since 1.0.0
		 */
		function init_shortcode($atts) {
			$this->shortcode_start();
			$this->instance++;
			return do_shortcode($this->shortcode(shortcode_atts($this->atts, $atts), $this->post_type));
		}

		/**
		 * Register and/or enqueue scripts and stylesheets to use later
		 * @since 1.0.0
		 */
		function register_scripts_styles() {
		}

		/**
		 * Function to override in inherited classes
		 * @param array $atts Shortcode attributes that have already been parsed with shortcode_atts.
		 * @param string $post_type The shortcode tag, which is the same than the post type.
		 */
		function shortcode( $atts = array(), $post_type ) {
		}

		/**
		 * Trigger at the end of __construct of this shortcode
		 */
		function manage_and_filter() {
			if (is_admin()) {
				add_filter("manage_edit-{$this->post_type}_columns", array(&$this, "type_column_header"), 10, 2);
				add_filter("manage_{$this->post_type}_posts_custom_column", array(&$this, "type_column"), 10, 3);
				add_action('load-edit.php', array(&$this, 'filter_load'));
			}
		}

		/**
		 * Display an additional column in list
		 * @param array
		 * @return array
		 */
		function type_column_header($columns) {
			switch ($_GET['post_type']) {
				case 'section' :
					
					break;
				case 'portfolio' :
					$columns['shortcode'] = __('Shortcode', 'themify');
					break;
				case 'highlight' :
				case 'team' :
					$columns['icon'] = __('Icon', 'themify');
					$columns['shortcode'] = __('Shortcode', 'themify');
					break;
				default :
					$columns['shortcode'] = __('Shortcode', 'themify');
					break;
			}
			return $columns;
		}

		/**
		 * Display shortcode, type, size and color in columns in tiles list
		 * @param string column key
		 * @param number post id
		 */
		function type_column($column, $post_id) {
			if ('portfolio' == $this->post_type) {
				switch( $column ) {
					case 'shortcode' :
						echo $this->shortcode_column($post_id);
						break;
				}
			}
			if ('section' == $this->post_type) {
				switch( $column ) {
					case 'shortcode' :
						echo $this->shortcode_column($post_id);
						break;
				}
			}
			if ('highlight' == $this->post_type || 'team' == $this->post_type) {
				switch( $column ) {
					case 'icon' :
						the_post_thumbnail(array(50, 50));
						break;
					case 'shortcode' :
						echo $this->shortcode_column($post_id);
						break;
				}
			}
		}

		function shortcode_column($post_id = '') {
			return '<code>[' . $this->post_type . ' id="' . $post_id . '"]</code>';
		}

		/**
		 * Filter request to sort
		 */
		function filter_load() {
			add_action(current_filter(), array($this, 'setup_vars'), 20);
			add_action('restrict_manage_posts', array($this, 'get_select'));
			add_filter("manage_taxonomies_for_{$this->post_type}_columns", array($this, 'add_columns'));
		}

		/**
		 * Add columns when filtering posts in edit.php
		 */
		public function add_columns($taxonomies) {
			return array_merge($taxonomies, $this->taxonomies);
		}

		/**
		 * Parses the arguments given as category to see if they are category IDs or slugs and returns a proper tax_query
		 * @param $category
		 * @param $post_type
		 * @return array
		 */
		function parse_category_args($category, $post_type) {
			if ('all' != $category) {
				$tax_query_terms = explode(',', $category);
				if (preg_match('#[a-z]#', $category)) {
					return array( array('taxonomy' => $post_type . '-category', 'field' => 'slug', 'terms' => $tax_query_terms));
				} else {
					return array( array('taxonomy' => $post_type . '-category', 'field' => 'id', 'terms' => $tax_query_terms));
				}
			}
		}

		/**
		 * Select form element to filter the post list
		 * @return string HTML
		 */
		public function get_select() {
			if (!self::$run_once) {
				return;
			}
			self::$run_once = false;
			$html = '';
			foreach ($this->taxonomies as $tax) {
				$options = sprintf('<option value="">%s %s</option>', __('View All', 'themify'),
				get_taxonomy($tax)->label);
				$class = is_taxonomy_hierarchical($tax) ? ' class="level-0"' : '';
				foreach (get_terms( $tax ) as $taxon) {
					$options .= sprintf('<option %s%s value="%s">%s%s</option>', isset($_GET[$tax]) ? selected($taxon->slug, $_GET[$tax], false) : '', '0' !== $taxon->parent ? ' class="level-1"' : $class, $taxon->slug, '0' !== $taxon->parent ? str_repeat('&nbsp;', 3) : '', "{$taxon->name} ({$taxon->count})");
				}
				$html .= sprintf('<select name="%s" id="%s" class="postform">%s</select>', $tax, $tax, $options);
			}
			return print $html;
		}

		/**
		 * Setup vars when filtering posts in edit.php
		 */
		function setup_vars() {
			$this->post_type =  get_current_screen()->post_type;
			$this->taxonomies = array_diff(get_object_taxonomies($this->post_type), get_taxonomies(array('show_admin_column' => 'false')));
		}

		/**
		 * Returns link wrapped in paragraph either to the post type archive page or a custom location
		 * @param bool|string False does nothing, true goes to archive page, custom string sets custom location
		 * @param string Text to link
		 * @return string
		 */
		function section_link($more_link = false, $more_text, $post_type) {
			if ($more_link) {
				if ('true' == $more_link) {
					$more_link = get_post_type_archive_link($post_type);
				}
				return '<p class="more-link-wrap"><a href="' . esc_url($more_link) . '" class="more-link">' . $more_text . '</a></p>';
			}
			return '';
		}
		
		/**
		 * Returns class to add in columns when querying multiple entries
		 * @param string $style Entries layout
		 * @return string $col_class CSS class for column
		 */
		function column_class($style) {
			$col_class = '';
			switch ($style) {
				case 'grid4':
					$col_class = 'col4-1';
					break;
				case 'grid3':
					$col_class = 'col3-1';
					break;
				case 'grid2':
					$col_class = 'col2-1';
					break;
				default:
					$col_class = '';
					break;
			}
			return $col_class;
		}

	}

}
?>