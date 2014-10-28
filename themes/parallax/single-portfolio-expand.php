<!doctype html>
<html <?php language_attributes(); ?>>

	<head>
		<meta charset="<?php bloginfo('charset'); ?>">

		<title><?php echo is_home() || is_front_page()? get_bloginfo('name') : wp_title('');?></title>

		<!-- wp_header -->
		<?php wp_head(); ?>

		<base target="_parent" />
	</head>

	<body class="single-portfolio-expanded">
		
		<?php if (have_posts()) while (have_posts()) : the_post(); ?>

			<?php get_template_part('includes/loop-portfolio', 'index'); ?>

		<?php endwhile; ?>

		<a href="#" class="close-expanded"></a>
		
		<!-- wp_footer -->
		<?php wp_footer(); ?>

	</body>

</html>