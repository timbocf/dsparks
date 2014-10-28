<?php 
/**
 * Post Navigation.
 */
if(!themify_check('setting-post_nav_disable')):
	$in_same_cat = themify_check('setting-post_nav_same_cat')? true : false; ?>
	<!-- post-nav -->
	<div class="post-nav clearfix"> 
		<?php if(!is_attachment()): ?>
			<?php previous_post_link('<span class="prev">%link</span>', '<span class="arrow">' . _x( '&laquo;', 'Previous entry link arrow','themify') . '</span> %title', $in_same_cat) ?>
			<?php next_post_link('<span class="next">%link</span>', '<span class="arrow">' . _x( '&raquo;', 'Next entry link arrow','themify') . '</span> %title', $in_same_cat) ?>
		<?php else: ?>
			<span class="prev">
				<?php previous_image_link('large', '<span class="arrow">&laquo;</span>'.__('Previous Image', 'themify')) ?>
			</span>
			<span class="next">
				<?php next_image_link('large', '<span class="arrow">&raquo;</span>'.__('Next Image', 'themify')) ?>
			</span>
		<?php endif; ?>
	</div>
	<!-- /post-nav -->
<?php endif; ?>