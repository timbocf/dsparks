<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Template Gallery Showcase
 * 
 * Access original fields: $mod_settings
 * @author Themify
 */

extract( $settings, EXTR_SKIP );
?>

<div class="gallery-showcase-image">
	<img src="<?php echo wp_get_attachment_url( $gallery_images[0]->ID ); ?>" alt="" />
</div>

<div class="gallery-images">

	<?php
	$i = 0;
	foreach ( $gallery_images as $image ):
		$link = wp_get_attachment_url( $image->ID );
		$link_before = '' != $link ? sprintf( '<a title="%s" href="%s">', esc_attr( $image->post_title ), esc_url( $link ) ) : '';
		$link_after = '' != $link ? '</a>' : '';

		if( $this->is_img_php_disabled() ) {
			$img = wp_get_attachment_image( $image->ID, 'thumbnail' );
		} else {
			$img = wp_get_attachment_image_src( $image->ID, 'full' );
			$img = themify_get_image( "ignore=true&src={$img[0]}&w={$thumb_w_gallery}&h={$thumb_h_gallery}" );
		}

		echo $link_before . $img . $link_after;

	endforeach; // end loop ?>
</div>