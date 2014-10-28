<?php if(!is_single()){ global $more; $more = 0; } //enable more link ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php themify_post_before(); // hook ?>

<article itemscope itemtype="http://schema.org/Article" id="post-<?php the_ID(); ?>" <?php post_class("post clearfix " . $themify->theme->get_categories_as_classes(get_the_ID())); ?>>
	
	<?php themify_post_start(); // hook ?>
	
	<?php if('above' == $themify->media_position) get_template_part( 'includes/post-media', 'loop'); ?>
		
	<div class="post-content">
		
		<?php if($themify->hide_title != 'yes'): ?>
			<?php themify_before_post_title(); // Hook ?>
			<?php if($themify->unlink_title == 'yes'): ?>
				<h2 class="post-title entry-title" itemprop="name"><?php the_title(); ?></h2>
			<?php else: ?>
				<h2 class="post-title entry-title" itemprop="name"><a href="<?php echo themify_get_featured_image_link(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			<?php endif; //unlink post title ?>
			<?php themify_after_post_title(); // Hook ?> 
		<?php endif; //post title ?>
		
		<?php if($themify->hide_meta != 'yes'): ?>
			<p class="post-meta entry-meta">
				<?php if($themify->hide_meta_author != 'yes'): ?>
					<span class="post-author"><?php echo themify_get_author_link() ?></span>
					<span class="separator">/</span>
				<?php endif; ?>
		
				<?php if($themify->hide_meta_category != 'yes'): ?>
					<?php echo get_the_term_list( get_the_ID(), 'category', ' <span class="post-category">', ', ', '</span> <span class="separator">/</span>' ); ?>
				<?php endif; ?>
		
				<?php if($themify->hide_meta_tag != 'yes'): ?>
					<?php the_tags('<span class="post-tag">', ', ', '</span> <span class="separator">/</span>'); ?>
				<?php endif; ?>
				
				<?php  if( !themify_get('setting-comments_posts') && comments_open() && $themify->hide_meta_comment != 'yes' ) : ?>
					<span class="post-comment"><?php comments_popup_link( __( '0 comments', 'themify' ), __( '1 comment', 'themify' ), __( '% comments', 'themify' ) ); ?></span>
				<?php endif; ?>
			</p>
		<?php endif; //post meta ?>
		
		<?php if($themify->hide_date != 'yes'): ?>
			<time datetime="<?php the_time('o-m-d') ?>" class="post-date entry-date updated" itemprop="datePublished"><?php the_time(apply_filters('themify_loop_date', 'M j, Y')) ?></time>
		<?php endif; //post date ?>
		
		<?php if('above' != $themify->media_position) get_template_part( 'includes/post-media', 'loop'); ?>
		
		<div class="entry-content" itemprop="articleBody">

		<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>
	
			<?php the_excerpt(); ?>

			<?php if( themify_check('setting-excerpt_more') ) : ?>
				<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute('echo=0'); ?>" class="more-link"><?php echo themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify') ?></a></p>
			<?php endif; ?>
	
		<?php elseif ( 'none' == $themify->display_content && ! is_attachment() ) : ?>
	
		<?php else: ?>
		
			<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>
		
		<?php endif; //display content ?>

		</div><!-- /.entry-content -->
		
		<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>
		
	</div>
	<!-- /.post-content -->
	
	<?php themify_post_end(); // hook ?>
	
</article>
<?php themify_post_after(); // hook ?>

<!-- /.post -->