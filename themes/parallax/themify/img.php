<?php
/**
 * Routines for generation of custom image sizes and deletion of these sizes.
 *
 * @since 1.9.0
 * @package themify
 */

if ( ! function_exists( 'themify_do_img' ) ) {
	/**
	 * Resize images dynamically using wp built in functions
	 * Original by Victor Teixeira
	 * Modified by Elio Rivero: multisite check, usage of WP Image Editor class
	 *
	 * @global int $blog_id
	 * @param int $attach_id
	 * @param string $img_url
	 * @param int $width
	 * @param int $height
	 * @param bool $crop
	 * @return array
	 */
	function themify_do_img( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {

		$upload_info = wp_upload_dir();
		$upload_url = $upload_info['baseurl'];

		// this is an attachment, so we have the ID
		if ( $attach_id ) {

			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$file_path = get_attached_file( $attach_id );

			// this is not an attachment, let's use the image url
		} elseif ( $img_url ) {

			$http = 'http://';
			$https = 'https://';

			if ( false !== stripos( $img_url, $https ) ) {
				// if image url begins with https:// make $upload_url match the scheme
				$upload_url = str_replace( $http, $https, $upload_url );
			} elseif ( false !== stripos( $img_url, $http ) ) {
				// if image url begins with http:// make $upload_url match the scheme
				$upload_url = str_replace( $https, $http, $upload_url );
			}

			// If image is remote, return the original image.
			$url_host = parse_url( $img_url, PHP_URL_HOST );
			$this_host = parse_url( get_site_url(), PHP_URL_HOST );
			// Return URL as is if it's an image from a different domain
			if ( str_replace( 'www.', '', $url_host ) != str_replace( 'www.', '', $this_host ) ) {
				return array( 'url' => $img_url );
			}

			// Define path of image.
			if ( is_multisite() && ( false === stripos( $img_url, $upload_url ) ) ) {
				$rel_path = preg_replace( "#http(.*?)files#", '', $img_url );
			} else {
				$rel_path = str_replace( $upload_url, '', $img_url );
			}
			$file_path = $upload_info['basedir'] . $rel_path;

			if ( is_file( $file_path ) ) {
				/**
				 * Keeps width and height of original image.
				 * @var array $orig_size
				 */
				$orig_size = getimagesize( $file_path );

				$image_src[0] = $img_url;
				$image_src[1] = $orig_size[0];
				$image_src[2] = $orig_size[1];
			} else {
				return array( 'url' => $img_url );
			}
		} else {
			// if nothing was provided, return original image url
			return array( 'url' => $img_url );
		}

		$file_info = pathinfo( $file_path );
		$extension = isset( $file_info['extension'] ) ? '.'. $file_info['extension'] : '';

		// the image path without the extension
		if ( isset( $file_info['dirname'] ) && isset( $file_info['filename'] ) ) {
			$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];
		} else {
			// we can't do anything, return original image url
			return array( 'url' => $img_url );
		}

		add_filter( 'image_resize_dimensions', 'themify_img_resize_dimensions', 10, 5 );

		$dims = image_resize_dimensions($image_src[1], $image_src[2], $width, $height, $crop);
		$new_w = $dims[4];
		$new_h = $dims[5];

		// Expected image path
		$cropped_img_path = $no_ext_path.'-'.$new_w.'x'.$new_h.$extension;

		if ( $image_src[1] == $new_w && $image_src[2] == $new_h ) {
			// default output - without resizing
			$ql_image = array (
				'url' => $image_src[0],
				'width' => $image_src[1],
				'height' => $image_src[2]
			);
			return $ql_image;
		}

		// check if the resized version exists (for $crop = true, also works for $crop = false if the sizes match)
		if ( is_file( $cropped_img_path ) ) {
			$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

			if ( is_multisite() && ( false === stripos( $img_url, $upload_url ) ) ) {
				$cropped_img_url = preg_replace( "#http(.*?)files#", $upload_url, $cropped_img_url );
			}

			if ( themify_maybe_do_retina_size() ) {
				$img_file = str_replace( $new_w.'x'.$new_h, $new_w.'x'.$new_h.'@2x', $cropped_img_path );
				themify_do_retina_img( $img_url, $width, $height, $crop, $new_w, $new_h, $image_src, $img_file, $file_path );
			}

			$ql_image = array (
				'url' => $cropped_img_url,
				'width' => $new_w,
				'height' => $new_h
			);
			return $ql_image;
		}

		// no cache files - let's finally resize it
		$image = wp_get_image_editor( $file_path );

		if ( ! is_wp_error( $image ) ) {
			$image->set_quality(95);
			$image->resize( $new_w, $new_h, $crop );
			$img_file = $image->generate_filename();

			$new_image = $image->save( $img_file );

			$new_img = str_replace( basename( $image_src[0] ), basename( $new_image['path'] ), $image_src[0] );

			if ( is_multisite() && ( false === stripos( $img_url, $upload_url ) ) ) {
				$new_img = preg_replace( "#http(.*?)files#", $upload_url, $new_img );
			}

			remove_filter( 'image_resize_dimensions', 'themify_img_resize_dimensions' );

			// resized output
			$ql_image = array (
				'url' => $new_img,
				'width' => $new_image['width'],
				'height' => $new_image['height']
			);

			// Add the resized dimensions to original image metadata,
			// so we can delete resized images if the original image is deleted from Media Library
			$attachment_id = themify_get_attachment_id_from_url( $img_url );
			if ( $attachment_id ) {
				$metadata = wp_get_attachment_metadata( $attachment_id );
				if ( isset( $metadata['image_meta'] ) ) {
					$metadata['image_meta']['resized_images'][] = $new_w .'x'. $new_h;
					wp_update_attachment_metadata( $attachment_id, $metadata );
				}
			}

			if ( themify_maybe_do_retina_size() ) {
				themify_do_retina_img( $img_url, $width, $height, $crop, $new_w, $new_h, $image_src, $img_file, $file_path );
			}

			// Return resized image
			return $ql_image;
		} else {
			// there was an error, return original image url
			return array( 'url' => $img_url );
		}
	}
}

if ( ! function_exists( 'themify_do_retina_img' ) ) {
	/**
	 * Generate image for high resolution devices.
	 *
	 * @since 1.9.0
	 *
	 * @param string $img_url
	 * @param int $width
	 * @param int $height
	 * @param bool $crop
	 * @param int $new_w
	 * @param int $new_h
	 * @param array $image_src
	 * @param string $img_file
	 * @param string $file_path
	 *
	 * @return array
	 */
	function themify_do_retina_img( $img_url, $width, $height, $crop, $new_w, $new_h, $image_src, $img_file, $file_path ) {
		// @2x image file path
		$destfilename = preg_replace( '/([0-9]+)x([0-9]+)/', '$1x$2@2x', $img_file );

		// check if retina image file exists
		if ( ! is_file( $destfilename ) || ! getimagesize( $destfilename ) ) {

			// Retina dimensions
			$retina_w = $width*2;
			$retina_h = $height*2;

			// Get expected image size after cropping
			$dims_x2 = image_resize_dimensions($image_src[1], $image_src[2], $retina_w, $retina_h, $crop);
			$dst_x2_w = $dims_x2[4];
			$dst_x2_h = $dims_x2[5];

			// If possible, make the @2x image
			if ( $dst_x2_h ) {

				$retina_img = wp_get_image_editor( $file_path );

				if ( ! is_wp_error( $retina_img ) ) {

					$retina_img->resize( $retina_w, $retina_h, $crop );
					$retina_img->set_quality( 95 );
					$suffix = $new_w . 'x' . $new_h . '@2x';
					$filename = $retina_img->generate_filename( $suffix );
					$retina_img = $retina_img->save($filename);

					// Add the resized dimensions to original image metadata,
					// so we can delete resized images if the original image is deleted from Media Library
					$attachment_id = themify_get_attachment_id_from_url( $img_url );
					if ( $attachment_id ) {
						$metadata = wp_get_attachment_metadata( $attachment_id );
						if ( isset( $metadata['image_meta'] ) ) {
							$metadata['image_meta']['resized_images'][] = $suffix;
							wp_update_attachment_metadata( $attachment_id, $metadata );
						}
					}
				}
			}
		}
	}
}

/**
 * Disable the min commands to choose the minimum dimension, thus enabling image enlarging.
 *
 * @param $default
 * @param $orig_w
 * @param $orig_h
 * @param $dest_w
 * @param $dest_h
 * @return array
 */
function themify_img_resize_dimensions( $default, $orig_w, $orig_h, $dest_w, $dest_h ) {
	// set portion of the original image that we can size to $dest_w x $dest_h
	$aspect_ratio = $orig_w / $orig_h;
	$new_w = $dest_w;
	$new_h = $dest_h;

	if ( !$new_w ) {
		$new_w = intval( $new_h * $aspect_ratio );
	}

	if ( !$new_h ) {
		$new_h = intval( $new_w / $aspect_ratio );
	}

	$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

	$crop_w = round( $new_w / $size_ratio );
	$crop_h = round( $new_h / $size_ratio );

	$s_x = floor( ( $orig_w - $crop_w ) / 2 );
	$s_y = floor( ( $orig_h - $crop_h ) / 2 );

	// the return array matches the parameters to imagecopyresampled()
	// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
	return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
}

/**
 * Get attachment ID for image from its url.
 *
 * @param string $url
 * @return bool|null|string
 */
function themify_get_attachment_id_from_url( $url = '' ) {
	global $wpdb;
	$attachment_id = false;

	// If there is no url, return.
	if ( '' == $url ) {
		return false;
	}

	// Get the upload directory paths
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $url, $upload_dir_paths['baseurl'] ) ) {

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		$url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $url );

		// Remove the upload path base directory from the attachment URL
		$url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $url );

		// Finally, run a custom database query to get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $url ) );

	}

	return $attachment_id;
}

if ( ! function_exists( 'themify_delete_extra_image_sizes' ) ) {
	/**
	 * Deletes the resized images when the original image is deleted from the WordPress Media Library.
	 *
	 * @since 1.9.0
	 */
	function themify_delete_extra_image_sizes( $post_id ) {
		// Get attachment image metadata
		$metadata = wp_get_attachment_metadata( $post_id );
		if ( ! $metadata ) {
			return;
		}

		// Do some bailing if we cannot continue
		if ( ! isset( $metadata['file'] ) || ! isset( $metadata['image_meta']['resized_images'] ) ) {
			return;
		}
		$pathinfo = pathinfo( $metadata['file'] );
		$resized_images = $metadata['image_meta']['resized_images'];

		// Get WordPress uploads directory (and bail if it doesn't exist)
		$wp_upload_dir = wp_upload_dir();
		$upload_dir = $wp_upload_dir['basedir'];
		if ( ! is_dir( $upload_dir ) ) {
			return;
		}

		// Delete the resized images
		foreach ( $resized_images as $dims ) {
			// Get the resized images filename
			$file = $upload_dir .'/'. $pathinfo['dirname'] .'/'. $pathinfo['filename'] . '-'. $dims .'.'. $pathinfo['extension'];
			$file_retina = $upload_dir .'/'. $pathinfo['dirname'] .'/'. $pathinfo['filename'] . '-'. $dims .'@2x.'. $pathinfo['extension'];

			// Delete the resized image
			@unlink( $file );
			@unlink( $file_retina );
		}
	}
}
add_action( 'delete_attachment', 'themify_delete_extra_image_sizes' );