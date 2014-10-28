<?php 
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php themify_before_post_image(); // Hook ?>

<?php if ( themify_check( 'video_url' ) ) : ?>

	<div class="post-image">

		<?php $themify->theme->show_video(); ?>

	</div>

<?php elseif ( $post_image = themify_get_image( $themify->auto_featured_image . $themify->image_setting . 'w=' . $themify->width . '&h=' . $themify->height ) ) : ?>

	<?php if ( $themify->hide_image != 'yes' ) : ?>

		<figure class="post-image <?php echo $themify->image_align; ?>">

			<?php $themify->theme->show_image( $post_image ); ?>

		</figure>

	<?php endif; // hide post image ?>

<?php endif; // video url ?>

<?php themify_after_post_image(); // Hook ?>