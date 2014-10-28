<!doctype html>
<html <?php echo themify_get_html_schema(); ?> <?php language_attributes(); ?>>
<head>
<?php
/** Themify Default Variables
 *  @var object */
	global $themify; ?>
<meta charset="<?php bloginfo( 'charset' ); ?>">

<title itemprop="name"><?php wp_title( '' ); ?></title>

<?php
/**
 *  Stylesheets and Javascript files are enqueued in theme-functions.php
 */
?>

<!-- wp_header -->
<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
<?php themify_body_start(); // hook ?>
<div id="pagewrap" class="hfeed site">

	<?php if( 'yes' != $themify->hide_header ) : ?>

		<div id="headerwrap" <?php $themify->theme->custom_header_background(); ?> >

			<?php themify_header_before(); // hook ?>
			<header id="header" class="pagewidth">
				<?php themify_header_start(); // hook ?>
				<hgroup>
					<?php echo themify_logo_image('site_logo'); ?>
		
					<?php if ( $site_desc = get_bloginfo( 'description' ) ) : ?>
						<?php global $themify_customizer; ?>
						<div id="site-description" class="site-description"><?php echo class_exists( 'Themify_Customizer' ) ? $themify_customizer->site_description( $site_desc ) : $site_desc; ?></div>
					<?php endif; ?>
				
					<div class="social-widget">
						<?php dynamic_sidebar('social-widget'); ?>

						<?php if(!themify_check('setting-exclude_rss')): ?>
							<div class="rss">
								<a href="<?php if(themify_get('setting-custom_feed_url') != ""){ echo themify_get('setting-custom_feed_url'); } else { bloginfo('rss2_url'); } ?>"></a>
							</div>
						<?php endif; ?>
					</div>
					<!-- /.social-widget -->
						
					<?php if(!themify_check('setting-exclude_search_form')): ?>
						<?php get_search_form(); ?>
					<?php endif ?>

					<?php
						// If there's a header background slider, show it.
						global $themify_bg_gallery;
						$themify_bg_gallery->create_controller();
					?>
				</hgroup>
				
				<div id="nav-bar" class="clearfix">
					<nav>
						<div id="menu-icon" class="mobile-button"><span><?php _e('Menu', 'themify'); ?></span></div>
						<?php themify_theme_menu_nav(); ?>
						<!-- /#main-nav --> 
					</nav>
				</div>

				<?php themify_header_end(); // hook ?>
			</header>
			<!-- /#header -->
			<?php themify_header_after(); // hook ?>
					
		</div>
		<!-- /#headerwrap -->

	<?php else : ?>

		<div id="nav-bar" class="clearfix">
			<nav>
				<div id="menu-icon" class="mobile-button"><span><?php _e('Menu', 'themify'); ?></span></div>
				<?php themify_theme_menu_nav(); ?>
				<!-- /#main-nav --> 
			</nav>
		</div>

	<?php endif; // hide_header check ?>
	
	<div id="body" class="clearfix">
    <?php themify_layout_before(); //hook ?>