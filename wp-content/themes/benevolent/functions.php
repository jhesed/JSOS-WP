<?php
/**
 * Benevolent functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Benevolent
 */

if ( ! function_exists( 'benevolent_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function benevolent_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Benevolent, use a find and replace
	 * to change 'benevolent' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'benevolent', get_template_directory() . '/languages' );
    
    // Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary'   => esc_html__( 'Primary', 'benevolent' ),
        'secondary' => esc_html__( 'Secondary', 'benevolent' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See https://developer.wordpress.org/themes/functionality/post-formats/
	 */
	add_theme_support( 'post-formats', array(
		'aside',        
		'image',
		'video',
		'quote',
		'link',
        'gallery',
        'status',
        'audio', 
        'chat'
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'benevolent_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
    
    // Custom Image Size
    add_image_size( 'benevolent-slider', 1920, 967, true );
    add_image_size( 'benevolent-community', 960, 450, true );
    add_image_size( 'benevolent-blog', 350, 196, true );
    add_image_size( 'benevolent-with-sidebar', 780, 437, true );
    add_image_size( 'benevolent-without-sidebar', 1200, 437, true );
    add_image_size( 'benevolent-featured-post', 275, 275, true );
    add_image_size( 'benevolent-recent-post', 75, 75, true );
    
    /* Custom Logo */
    add_theme_support( 'custom-logo', array(
    	'width'       => 270,
		'height'      => 60,
    	'header-text' => array( 'site-title', 'site-description' ),
    ) );
    
}
endif;
add_action( 'after_setup_theme', 'benevolent_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function benevolent_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'benevolent_content_width', 780 );
}
add_action( 'after_setup_theme', 'benevolent_content_width', 0 );

/**
* Adjust content_width value according to template.
*
* @return void
*/
function benevolent_template_redirect_content_width() {

	// Full Width in the absence of sidebar.
	if( is_page() ){
	   $sidebar_layout = benevolent_sidebar_layout();
       if( ( $sidebar_layout == 'no-sidebar' ) || ! ( is_active_sidebar( 'right-sidebar' ) ) ) $GLOBALS['content_width'] = 1200;
        
	}elseif ( ! ( is_active_sidebar( 'right-sidebar' ) ) ) {
		$GLOBALS['content_width'] = 1200;
	}

}
add_action( 'template_redirect', 'benevolent_template_redirect_content_width' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function benevolent_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Right Sidebar', 'benevolent' ),
		'id'            => 'right-sidebar',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
    
    register_sidebar( array(
		'name'          => esc_html__( 'Footer One', 'benevolent' ),
		'id'            => 'footer-one',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
    
    register_sidebar( array(
		'name'          => esc_html__( 'Footer Two', 'benevolent' ),
		'id'            => 'footer-two',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
    
    register_sidebar( array(
		'name'          => esc_html__( 'Footer Three', 'benevolent' ),
		'id'            => 'footer-three',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
    
    register_sidebar( array(
		'name'          => esc_html__( 'Footer Four', 'benevolent' ),
		'id'            => 'footer-four',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'benevolent_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function benevolent_scripts() {
	
    $benevolent_query_args = array(
		'family' => 'Raleway:400,600,700,500',
	);
    wp_enqueue_style( 'benevolent-google-fonts', add_query_arg( $benevolent_query_args, "//fonts.googleapis.com/css" ) );
    wp_enqueue_style( 'benevolent-font-awesome', get_template_directory_uri() . '/css/font-awesome.css' );
    wp_enqueue_style( 'benevolent-sidr-style', get_template_directory_uri() . '/css/jquery.sidr.light.css' );
    wp_enqueue_style( 'benevolent-flexslider-style', get_template_directory_uri() . '/css/flexslider.css' );
    wp_enqueue_style( 'benevolent-style', get_stylesheet_uri() );
    
    wp_enqueue_script( 'benevolent-sidr', get_template_directory_uri() . '/js/jquery.sidr.js', array('jquery'), '20160125', true );
	wp_enqueue_script( 'benevolent-flexslider', get_template_directory_uri() . '/js/jquery.flexslider.js', array('jquery'), '2.6.0', true );
	wp_enqueue_script( 'benevolent-tab', get_template_directory_uri() . '/js/tab.js', array('jquery'), '20160125', true );
    wp_enqueue_script( 'benevolent-waypoint', get_template_directory_uri() . '/js/waypoint.js', array('jquery'), '2.0.3', true );
    wp_enqueue_script( 'benevolent-counterup', get_template_directory_uri() . '/js/jquery.counterup.js', array('jquery', 'benevolent-waypoint'), '1.0', true );
    wp_register_script( 'benevolent-custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), '20160125', true );
    
    $benevolent_slider_auto = get_theme_mod( 'benevolent_slider_auto', '1' );
    $benevolent_slider_loop = get_theme_mod( 'benevolent_slider_loop', '1' );
    $benevolent_slider_pager = get_theme_mod( 'benevolent_slider_pager', '1' );    
    $benevolent_slider_animation = get_theme_mod( 'benevolent_slider_animation', 'slide' );
    $benevolent_slider_speed = get_theme_mod( 'benevolent_slider_speed', '7000' );
    $benevolent_animation_speed = get_theme_mod( 'benevolent_animation_speed', '600' );
    
    $benevolent_array = array(
        'auto'      => esc_attr( $benevolent_slider_auto ),
        'loop'      => esc_attr( $benevolent_slider_loop ),
        'pager'   => esc_attr( $benevolent_slider_pager ),
        'animation' => esc_attr( $benevolent_slider_animation ),
        'speed'     => absint( $benevolent_slider_speed ),
        'a_speed'   => absint( $benevolent_animation_speed )
    );
    
    wp_localize_script( 'benevolent-custom', 'benevolent_data', $benevolent_array );
    wp_enqueue_script( 'benevolent-custom' );
    
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'benevolent_scripts' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Meta Box.
 */
require get_template_directory() . '/inc/metabox.php';

/**
 * Featured Post Widget
 */
require get_template_directory() . '/inc/widget-featured-post.php';

/**
 * Recent Post Widget
 */
require get_template_directory() . '/inc/widget-recent-post.php';

/**
 * Popular Post Widget
 */
require get_template_directory() . '/inc/widget-popular-post.php';

/**
 * Social Link Widget
 */
require get_template_directory() . '/inc/widget-social-links.php';