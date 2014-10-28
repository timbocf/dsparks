<?php
/**
 * Portfolio Navigation
 */

if( themify_check( 'setting-portfolio_nav_disable' ) )
	return;

$same_cat = themify_check( 'setting-portfolio_nav_same_cat' ) ? true : false;
$excluded_terms = array();
?>
<!-- post-nav -->
<div class="post-nav clearfix">
	<?php previous_post_link( '<span class="prev">%link</span>', '<span class="arrow">' . _x( '&laquo;', 'Previous entry link arrow','themify') . '</span> %title', $same_cat, $excluded_terms, 'portfolio-category' ) ?>
	<?php next_post_link( '<span class="next">%link</span>', '%title <span class="arrow">&raquo;</span>', $same_cat, $excluded_terms, 'portfolio-category' ) ?>
</div>
<!-- /post-nav -->