<?php if(!is_single()){ global $more; $more = 0; } //enable more link ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify, $post, $themify_section;

$section_type = themify_check( 'section_type' ) ? themify_get( 'section_type' ) : 'standard';
$section_width = themify_check( 'section_width' ) ? themify_get( 'section_width' ) : '';
$section_type_class = ('gallery_posts' == $section_type) ? 'gallery' : $section_type;
?>

<?php themify_post_before(); // hook ?>

<section id="<?php echo apply_filters('editable_slug', $post->post_name); ?>" <?php $themify->theme->section_background("post clearfix section-post".themify_theme_section_category_classes($post->ID)." ".$section_type_class." ".$section_width); ?>>
	
	<div class="section-inner clearfix">
		<?php themify_post_start(); // hook ?>
	
		<?php if ( ( themify_is_query_page() && $themify->hide_title != 'yes' ) && ( 'yes' != themify_get( 'hide_section_title' ) ) ): ?>
			<?php themify_before_post_title(); // Hook ?>
			
			<h1 class="section-title"><?php the_title(); ?></h1>
			
			<?php themify_after_post_title(); // Hook ?>
		<?php endif; //section title ?>
		
		<?php if ( ( themify_is_query_page() && $themify->hide_subtitle != 'yes' ) && ( 'yes' != themify_get( 'hide_section_subtitle' ) ) ): ?>
			<?php if(themify_check('subtitle')): ?>
				<h2 class="section-subhead"><?php echo themify_get('subtitle'); ?></h2>
			<?php endif; // end check for subtitle text ?>
		<?php endif; // end check for hide_subtitle ?>
	
		<div class="section-content post-content clearfix">
		
			<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>
			
			<?php edit_post_link(__('Edit Section', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

			<?php
			/**
			 * SECTION TYPE: VIDEO
			 */
			if ( 'video' == $section_type && themify_get( 'video_url' ) != '' ) : ?>

				<?php
				global $wp_embed;
				echo $wp_embed->run_shortcode('[embed]' . themify_get('video_url') . '[/embed]');
				?>

			<?php endif; // video section type ?>
			
			<?php
			/**
			 * SECTION TYPE: SLIDER
			 */
			if ( 'slider' == $section_type && themify_get( 'slider_gallery' ) != '' ) : ?>

				<?php
				$gallery_shortcode = themify_get( 'slider_gallery' );
				$image_ids = preg_replace( '#\[gallery(.*)ids="([0-9|,]*)"(.*)\]#i', '$2', $gallery_shortcode );
				
				$query_args = array(
					'post__in' => explode( ',', str_replace( ' ', '', $image_ids ) ),
					'post_type' => 'attachment',
					'post_mime_type' => 'image',
					'numberposts' => -1,
					'orderby' => stripos( $gallery_shortcode, 'rand' ) ? 'rand': 'post__in',
					'order' => 'ASC'
				);
				$images = get_posts( apply_filters( 'themify_theme_get_gallery_images', $query_args ) );
				
				if($images){
					echo '
					<div id="section-slider-'.get_the_ID().'" class="slider-section">
						<div class="slider">
							<ul class="slides clearfix">';
					foreach( $images as $image ){
						// Get large size for background
						$image_data = wp_get_attachment_image_src( $image->ID, 'large' );
						echo '<li data-bg="',$image_data[0],'"><span class="slider-dot"></span></li>';
					}
					echo '		</ul>
								<div class="carousel-nav-wrap">
									<a href="#" class="carousel-prev" style="display: block; ">&lsaquo;</a>
									<a href="#" class="carousel-next" style="display: block; ">&rsaquo;</a>
								</div>

						</div>
					</div>
					<!-- /slider-section -->';
				}
				?>

			<?php endif; // gallery section type ?>

		</div>
		<!-- /.section-content -->

		<?php themify_post_end(); // hook ?>
	</div> <!-- /.section-inner -->
	
	<div class="section-overlay"></div>
	<!-- /section-overlay -->
	
</section>
<?php themify_post_after(); // hook ?>
<!-- /.section-post -->