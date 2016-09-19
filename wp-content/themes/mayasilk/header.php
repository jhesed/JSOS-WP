<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Maya_Silk
 * @since Maya Silk 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.min.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div id="page" class="hfeed">
		<header id="masthead" class="site-header">
			<div id="site-top">
				<div class="top-container">
					<nav id="mayasilk-main">
						<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => '', 'menu_class' => 'nav-menu', 'menu_id' => 'mayasilk-menu', ) ); ?>
					</nav><!-- #site-navigation -->
					
					<div class="search-icon search-bar">			
						<div class="container-fluid">
					<div class="topbar-right">
						<?php maysilk_social(); ?>
					</div>
							<div class="search-right" data-trigger="click">
								<a href="#" id="top-search-trigger" class="search-trigger"><i class="fa fa-search"></i></a>
								<div class="search-bar search-bar-arrow">
									<?php
									if (get_search_form('mayasilk_search_form')) {
									  get_search_form('mayasilk_search_form');
									}
									?> 
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="logo">
				<div class="site">
					<div class="logo-center">
						<?php if ( get_theme_mod( 'custom_logo' )) : ?>
						<?php mayasilk_the_custom_logo(); ?>
						<?php else : ?>
						<hgroup>
							<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
						</hgroup>
						<?php endif; ?>
					</div>
				</div>
			</div><!-- #masthead -->
		</header>
		<div id="main" class="wrapper site">