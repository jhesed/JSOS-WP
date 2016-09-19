<?php
/**
 * Maya Silk functions and definitions
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * When using a child theme (see https://codex.wordpress.org/Theme_Development and
 * https://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook.
 *
 * For more information on hooks, actions, and filters, @link https://codex.wordpress.org/Plugin_API
 *
 * @package WordPress
 * @subpackage Maya_Silk
 * @since Maya Silk 1.0
 */

// Set up the content width value based on the theme's design and stylesheet.
if ( ! isset( $content_width ) )
	$content_width = 625;

/**
 * Maya Silk setup.
 *
 * Sets up theme defaults and registers the various WordPress features that
 * Maya Silk supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
 * 	custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Maya Silk 1.0
 */
function mayasilk_setup() {
	/*
	 * Makes Maya Silk available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Maya Silk, use a find and replace
	 * to change 'mayasilk' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'mayasilk', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( "title-tag" );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'video', 'audio', 'image', 'link', 'quote', 'status' ) );

	add_theme_support( 'custom-logo' );
	
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
	'primary'=> __( 'Primary Menu', 'mayasilk' ),
	'footermenu'=> __( 'Footer Menu', 'mayasilk' )
	));

	/*
	 * This theme supports custom background color and image,
	 * and here we also set up the default background color.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => 'e6e6e6',
	) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1000, 1000 ); // Unlimited height, soft crop
	add_image_size('mayasilk_slider', 734, 400, true); // hard crop
	add_image_size('mayasilk_blog-post', 734, 400, true); // hard crop
	add_image_size('mayasilk_featured-post-thumb', 1080, 495, true); // hard crop
	add_image_size('mayasilk_next_prev_image_link', 70, 70, true); // hard crop
	add_image_size('mayasilk_recent_post_small', 80, 60, array( 'left', 'top' ) );
}
add_action( 'after_setup_theme', 'mayasilk_setup' );


// Add a class of "parent" to all menu items that have children - wp_nav_menu();

add_filter( 'wp_nav_menu_objects', 'mayasilk_menu_parent_class' );
function mayasilk_menu_parent_class( $items ) {
    $parents = array();
    foreach ( $items as $item ) {
	if ( $item->menu_item_parent && $item->menu_item_parent > 0 ) {
	    $parents[] = $item->menu_item_parent;
		}
    }	
    foreach ( $items as $item ) {
	if ( in_array( $item->ID, $parents ) ) {
	    $item->classes[] = 'parent-class';
		}
    }	
    return $items;    
}


/**
 * Return the Google font stylesheet URL if available.
 *
 * The use of Open Sans by default is localized. For languages that use
 * characters not supported by the font, the font can be disabled.
 *
 * @since Maya Silk 1.2
 *
 * @return string Font stylesheet or empty string if disabled.
 */

function mayasilk_add_google_fonts() {
		wp_enqueue_style( 'mayasilk-google-fonts', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,200,200italic,300,300italic,400italic,600,600italic,700,700italic,900,900italic', false ); 
		wp_enqueue_style( 'mayasilk3-google-fonts', 'https://fonts.googleapis.com/css?family=Merriweather', false ); 
}
add_action( 'wp_enqueue_scripts', 'mayasilk_add_google_fonts' );

/**
 * Enqueue scripts and styles for front-end.
 *
 * @since Maya Silk 1.0
 */
 
function mayasilk_scripts_styles() {
	global $wp_styles;

	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
		
	wp_enqueue_script( 'jquery' );
	// Loads our main JavaScript.
	wp_register_script( 'mayasilk_main_script', get_template_directory_uri() . '/js/main.min.js', '', '', true );

	// Localize the script with new data
	$translation_array = array(
		'some_string' => __( 'Show Navigation', 'mayasilk' ),
		'a_value' => '10'
	);
	wp_localize_script( 'mayasilk_main_script', 'object_name', $translation_array );
	
	// Enqueued script with localized data.
	wp_enqueue_script( 'mayasilk_main_script' );
	
	// Loads our main stylesheet.
	wp_enqueue_style( 'mayasilk-style', get_stylesheet_uri() );

	// Loads the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'mayasilk-ie', get_template_directory_uri() . '/css/ie.css');
	$wp_styles->add_data( 'mayasilk-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'mayasilk_scripts_styles' );


/**
 * Filter the page title.
 *
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @since Maya Silk 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function mayasilk_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name', 'display' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'mayasilk' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'mayasilk_wp_title', 10, 2 );

/**
 * Filter the page menu arguments.
 *
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 *
 * @since Maya Silk 1.0
 */
function mayasilk_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'mayasilk_page_menu_args' );

/**
 * Register sidebars.
 *
 * Registers our main widget area and the front page widget areas.
 *
 * @since Maya Silk 1.0
 */
function mayasilk_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'mayasilk' ),
		'id' => 'sidebar-1',
		'description' => __( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'mayasilk' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'mayasilk_widgets_init' );


 
if ( ! function_exists( 'mayasilk_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own mayasilk_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Maya Silk 1.0
 */
function mayasilk_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'mayasilk' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'mayasilk' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<header class="comment-meta comment-author vcard">
				<?php
					echo get_avatar( $comment, 60 );
					printf( '<div class="comment-meta-info"><cite><b class="fn">%1$s</b> %2$s</cite>',
						get_comment_author_link(),
						// If current post author is also comment author, make it known visually.
						( $comment->user_id === $post->post_author ) ? '<span>' . __( 'Post author', 'mayasilk' ) . '</span>' : ''
					);
					printf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a></div>',
						esc_url( get_comment_link( $comment->comment_ID ) ),
						get_comment_time( 'c' ),
						/* translators: 1: date, 2: time */
						sprintf( __( '%1$s at %2$s', 'mayasilk' ), get_comment_date(), get_comment_time() )
					);
				?>
			</header><!-- .comment-meta -->

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'mayasilk' ); ?></p>
			<?php endif; ?>

			<section class="comment-content comment">
				<?php comment_text(); ?>
				<?php edit_comment_link( __( 'Edit', 'mayasilk' ), '<p class="edit-link">', '</p>' ); ?>
			</section><!-- .comment-content -->

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'mayasilk' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;


/**
 * Extend the default WordPress body classes.
 *
 * Extends the default WordPress body class to denote:
 * 1. Using a full-width layout, when no active widgets in the sidebar
 *    or full-width template.
 * 2. Front Page template: thumbnail in use and number of sidebars for
 *    widget areas.
 * 3. White or empty background color to change the layout and spacing.
 * 4. Custom fonts enabled.
 * 5. Single or multiple authors.
 *
 * @since Maya Silk 1.0
 *
 * @param array $classes Existing class values.
 * @return array Filtered class values.
 */
function mayasilk_body_class( $classes ) {
	$background_color = get_background_color();
	$background_image = get_background_image();

	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'page-templates/full-width.php' ) )
		$classes[] = 'full-width';

	if ( empty( $background_image ) ) {
		if ( empty( $background_color ) )
			$classes[] = 'custom-background-empty';
		elseif ( in_array( $background_color, array( 'fff', 'ffffff' ) ) )
			$classes[] = 'custom-background-white';
	}

	// Enable custom font class only if the font CSS is queued to load.
	if ( wp_style_is( 'mayasilk-fonts', 'queue' ) )
		$classes[] = 'custom-font-enabled';

	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	return $classes;
}
add_filter( 'body_class', 'mayasilk_body_class' );

if ( ! function_exists( 'mayasilk_the_custom_logo' ) ) :
/**
 * Displays the optional custom logo.
 *
 * Does nothing if the custom logo is not available.
 *
 * @since Mayasilk 1.0.3
 */
function mayasilk_the_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}
endif;

/**
 * Register postMessage support.
 *
 * Add postMessage support for site title and description for the Customizer.
 *
 * @since Maya Silk 1.0
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */

function mayasilk_customize_register( $wp_customize ) {
	$wp_customize->add_section(
    'mayasilk_advanced_options',
    array(
        'title'     => esc_html__( 'Footer Settings', 'mayasilk'  ),
        'priority'  => 201
		)
	);
    $wp_customize->add_setting( 'footer_logo', 'sanitize_callback' == 'esc_url_raw'); // Add setting for logo uploader 
    // Add control for logo uploader (actual uploader)
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'footer_logo', array(
        'label'    => esc_html__( 'Footer Logo', 'mayasilk' ),
        'section'  => 'mayasilk_advanced_options',
        'settings' => 'footer_logo',
    ) ) );
    $wp_customize->add_setting( 'footer_content', 'sanitize_callback' == 'mayasilk_sanitize_text' ); // Add setting for footer Contnet
    // Add control for Footer Content
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_content', array(
        'label'    => esc_html__( 'Footer Content', 'mayasilk' ),
        'section'  => 'mayasilk_advanced_options',
        'settings' => 'footer_content',
		'type' => 'textarea',
    ) ) );
    $wp_customize->add_setting( 'footer_social_facebook', 'sanitize_callback' == 'esc_url_raw' ); // Add setting for facebook url
    // Add control for facebook url
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_social_facebook', array(
        'label'    => esc_html__( 'Facebook URL', 'mayasilk' ),
        'section'  => 'mayasilk_advanced_options',
        'settings' => 'footer_social_facebook',
		'type' => 'text',
    ) ) );
    $wp_customize->add_setting( 'footer_social_twitter', 'sanitize_callback' == 'esc_url_raw' ); // Add setting for twitter url
    // Add control for twitter url
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_social_twitter', array(
        'label'    => esc_html__( 'Twitter URL', 'mayasilk' ),
        'section'  => 'mayasilk_advanced_options',
        'settings' => 'footer_social_twitter',
		'type' => 'text',
    ) ) );
    $wp_customize->add_setting( 'footer_social_youtube', 'sanitize_callback' == 'esc_url_raw' ); // Add setting for youtube url
    // Add control for youtube url
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_social_youtube', array(
        'label'    => esc_html__( 'Youtube URL', 'mayasilk' ),
        'section'  => 'mayasilk_advanced_options',
        'settings' => 'footer_social_youtube',
		'type' => 'text',
    ) ) );
    $wp_customize->add_setting( 'footer_social_linkedin', 'sanitize_callback' == 'esc_url_raw' ); // Add setting for Linkedin url
    // Add control for Linkedin url
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_social_linkedin', array(
        'label'    => esc_html__( 'Linkedin URL', 'mayasilk' ),
        'section'  => 'mayasilk_advanced_options',
        'settings' => 'footer_social_linkedin',
		'type' => 'text',
    ) ) );
    $wp_customize->add_setting( 'footer_social_instagram', 'sanitize_callback' == 'esc_url_raw' ); // Add setting for Instagram url
    // Add control for Instagram url
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_social_instagram', array(
        'label'    => esc_html__( 'Instagram URL', 'mayasilk' ),
        'section'  => 'mayasilk_advanced_options',
        'settings' => 'footer_social_instagram',
		'type' => 'text',
    ) ) );
    $wp_customize->add_setting( 'footer_social_googleplus', 'sanitize_callback' == 'esc_url_raw' ); // Add setting for Google Plus url
    // Add control for Google Plus url
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_social_googleplus', array(
        'label'    => esc_html__( 'Google Plus URL', 'mayasilk' ),
        'section'  => 'mayasilk_advanced_options',
        'settings' => 'footer_social_googleplus',
		'type' => 'text',
    ) ) );
    $wp_customize->add_setting( 'footer_social_pinterest', 'sanitize_callback' == 'esc_url_raw' ); // Add setting for Pinterest url
    // Add control for Pinterest url
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_social_pinterest', array(
        'label'    => esc_html__( 'Pinterest URL', 'mayasilk' ),
        'section'  => 'mayasilk_advanced_options',
        'settings' => 'footer_social_pinterest',
		'type' => 'text',
    ) ) );
    $wp_customize->add_setting( 'footer_copyright_text', 'sanitize_callback' == 'mayasilk_sanitize_text' ); // Add setting for Pinterest url
    // Add control for Pinterest url
    $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'footer_copyright_text', array(
        'label'    => esc_html__( 'Footer Copyright', 'mayasilk' ),
        'section'  => 'mayasilk_advanced_options',
        'settings' => 'footer_copyright_text',
		'type' => 'textarea',
    ) ) );
}
add_action( 'customize_register', 'mayasilk_customize_register' );

//Text
function mayasilk_sanitize_text( $input ) {
    return wp_kses_post( force_balance_tags( $input ) );
}

function mayasilk_color_register( $wp_customize ) {
	$wp_customize->add_section(
    'mayasilk_color_options',
    array(
        'title'     =>  esc_html__( 'Color Scheme' , 'mayasilk' ),
        'priority'  => 100,
		'description' => esc_html__( 'Color Scheme include h1, h2, h3, h4, h5, h6, a, continue-reading text background, search submit backgournd all anchor & anchor hover, menu active link.' , 'mayasilk' ),
		)
	);
	$wp_customize->add_setting( 'color_scheme', array(
			'default' => '#C69F73',
			'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'color_scheme', array(
            array('label' => esc_html__( 'Color Settings', 'mayasilk' )),
            'section' => 'mayasilk_color_options',
            'settings' => 'color_scheme',
    ) ) );
}
add_action( 'customize_register', 'mayasilk_color_register' );

add_action('wp_head' , 'mayasilk_dynamic_style');
function mayasilk_dynamic_style(){
    $color_scheme = get_theme_mod( 'color_scheme' , '#C69F73' );
    ?>
    <style>
        .topbar-right span a:hover,.search-icon.search-bar .search-trigger:hover,a:hover,.entry-meta .category-list a,.comments-link a:hover, .entry-meta a:hover,.post-tags a.tag-list,.about-read-more,.social-right i:hover,nav#mayasilk-main ul li a:hover,nav#mayasilk-main .sub-menu > li > a:hover,a.comment-reply-link, a.comment-edit-link,a.comment-reply-link, a.comment-edit-link,.comments-link a, .entry-meta a,.entry-meta i,.date-and-time,.current_page_item a,.footer-social-icon span a,.archive-header span{
            color: <?php echo esc_attr($color_scheme) ?>;
        }
		.menu-toggle, input[type="submit"], input[type="button"], input[type="reset"], article.post-password-required input[type=submit], .bypostauthor cite span,.continue-reading,.navigation li a, .navigation li a:hover, .navigation li.active a, .navigation li.disabled,.tagcloud a:hover,.featured-read-more:hover,.menu-toggle:hover, .menu-toggle:focus, button:hover, input[type="submit"]:hover, input[type="button"]:hover, input[type="reset"]:hover, article.post-password-required input[type=submit]:hover,.flex-control-paging li a.flex-active,.flex-control-paging li a:hover,button{
			background-color: <?php echo esc_attr($color_scheme) ?>;
		}
		button, input, select, textarea,.tagcloud a:hover,.featured-read-more,.footer-social-icon span a,.bypostauthor cite span{
			border: 1px solid <?php echo esc_attr($color_scheme) ?>;
		}
		.search-right .search-bar{
			border-top: 4px solid <?php echo esc_attr($color_scheme) ?>;
		}
		.search-bar.search-bar-arrow:before{
			border-color: transparent transparent <?php echo esc_attr($color_scheme) ?> transparent;
		}
		.entry-content blockquote, .comment-content blockquote,.entry-summary blockquote{
			border-left: 3px solid <?php echo esc_attr($color_scheme) ?>;
		}
		article.format-aside .aside{
			border-left: 22px solid <?php echo esc_attr($color_scheme) ?>;
		}
    </style>
      <?php
 }
 
// Search 
 
function mayasilk_search_form( $form ) {
    $form = '<form method="get" id="searchform" action="' . home_url( '/' ) . '" >
				<input class="form-control" type="text" placeholder="' . esc_attr__('Type Here...', 'mayasilk') . '" name="s" id="s">
				<input type="submit" id="searchsubmit" value="' . esc_attr__('Search', 'mayasilk') . '" class="top-search-submit ">
			</form>';
    return $form;
}
add_filter( 'get_search_form', 'mayasilk_search_form' );

// Overright Default Gallery 

function mayasilk_gallery($output, $attr) {
    global $post;

    static $instance = 0;
    $instance++;


    /**
     *  will remove this since we don't want an endless loop going on here
     */
    // Allow plugins/themes to override the default gallery template.
    //$output = apply_filters('post_gallery', '', $attr);

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( !$attr['orderby'] )
            unset( $attr['orderby'] );
    }

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'itemtag'    => 'li',
        'icontag'    => 'dt',
        'captiontag' => 'dd',
        'columns'    => 1,
        'size'       => 'thumbnail',
        'include'    => '',
        'exclude'    => ''
    ), $attr));

    $id = intval($id);
    if ( 'RAND' == $order )
        $orderby = 'none';

    if ( !empty($include) ) {
        $include = preg_replace( '/[^0-9,]+/', '', $include );
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty($exclude) ) {
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
        return '';

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $columns = intval($columns);
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $gallery_style = $gallery_div = '';
    if ( apply_filters( 'use_default_gallery_style', true ) )
    $size_class = sanitize_html_class( $size );
    $gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'><div id='custom_slider' class='flexslider'><ul class='slides'>";
    $output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

    $i = 0;
    foreach ( $attachments as $id => $attachment ) {
        $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_image($attachment->ID, 'mayasilk_slider') : wp_get_attachment_image($attachment->ID, 'mayasilk_slider');

        $output .= "<{$itemtag} class='gallery-item'>";
        $output .= "
            <{$icontag} class='gallery-slides'>
                $link
            </{$icontag}>";
			
        if ( $captiontag && trim($attachment->post_excerpt) ) {
            $output .= "
                <{$captiontag} class='wp-caption-text gallery-caption'>
                " . wptexturize($attachment->post_excerpt) . "
                </{$captiontag}>";
        }
        $output .= "</{$itemtag}>";
  
    }

    /**
     * this is the extra br you want to remove so we change it to jus closing div tag
     * #3 in question
     */
    /*$output .= "
            <br style='clear: both;' />
        </div>\n";
     */

    $output .= "</ul></div></div>\n";
    return $output;
}
add_filter("post_gallery", "mayasilk_gallery",10,2);

/**
 * Extend Recent Posts Widget 
 *
 * Adds different formatting to the default WordPress Recent Posts Widget
 */

Class Mayasilk_Recent_Posts_Widget extends WP_Widget_Recent_Posts {

	function widget($args, $instance) {
	
		extract( $args );
		
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts', 'mayasilk') : $instance['title'], $instance, $this->id_base);
				
		if( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
			$number = 10;
					
		$r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
		if( $r->have_posts() ) :
			
			echo $before_widget;
			if( $title ) echo $before_title . $title . $after_title; ?>
			   <?php     
					$args = array( 'posts_per_page' => $number );				
                    $mayarecent=new WP_Query( $args );
                    if( $mayarecent->have_posts() ):
                        $skip_flag=1;                       
                ?>
                <?php
                        while( $mayarecent->have_posts() ):
                            $mayarecent->the_post();
                ?>
						<div class="short-recent-post">
                            <a href="<?php the_permalink(); ?>"><?php if(has_post_format('video')): ?>	
							<?php if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'mayasilk_recent_post_small', array('class' => 'img-responsive'));
							}?>
							<a href='<?php the_permalink(); ?>'><img class='video-post-sidebar' src="<?php echo get_template_directory_uri(); ?>/img/video-sidebar.png" alt=''></a>
							<?php else : ?>
							<?php if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'mayasilk_recent_post_small', array('class' => 'img-responsive'));
							}
							?><?php endif; ?></a>
							<h5><a title="<?php get_the_title() ? the_title() : the_ID(); ?>" href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a></h5>
							<p class="post-time recent-post-time"><?php the_time( get_option('date_format') ); ?></p>
						</div>

                <?php                                    
                            
                        endwhile;
                ?>
				
                <?php
                    endif;
                    wp_reset_postdata();
                ?>
			 
			<?php
			echo $after_widget;
		
		wp_reset_postdata();
		
		endif;
	}
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries']) )
			delete_option('widget_recent_entries');
		return $instance;
	}

	/**
	 * @param array $instance
	 */
	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mayasilk' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:', 'mayasilk' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}
function mayasilk_recent_widget_registration() {
  unregister_widget('WP_Widget_Recent_Posts');
  register_widget('Mayasilk_Recent_Posts_Widget');
}
add_action('widgets_init', 'mayasilk_recent_widget_registration');

// Custom Read More link

function mayasilk_excerpt_read_more_link($output) {
 global $post;
 return $output . '<a class="continue-reading" href="'. get_permalink($post->ID) . '">' . __('Continue Reading', 'mayasilk') . '</a>';

}
add_filter('the_excerpt', 'mayasilk_excerpt_read_more_link');

function mayasilk_custom_wp_trim_excerpt($text) {
$raw_excerpt = $text;
if ( '' == $text ) {
    //Retrieve the post content. 
    $text = get_the_content('');
 
    //Delete all shortcode tags from the content. 
    $text = strip_shortcodes( $text );
 
    $text = apply_filters('the_content', $text);
    $text = str_replace(']]>', ']]&gt;', $text);
     
    $allowed_tags = '<p>,<a>,<em>,<strong>,<img>,<i>'; /*** MODIFY THIS. Add the allowed HTML tags separated by a comma.***/
    $text = strip_tags($text, $allowed_tags);
     
    $excerpt_word_count = 55; /*** MODIFY THIS. change the excerpt word count to any integer you like.***/
    $excerpt_length = apply_filters('excerpt_length', $excerpt_word_count); 
     
    $excerpt_end = '...'; /*** MODIFY THIS. change the excerpt endind to something else.***/
    $excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_end);
     
    $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
    if ( count($words) > $excerpt_length ) {
        array_pop($words);
        $text = implode(' ', $words);
        $text = $text . $excerpt_more;
    } else {
        $text = implode(' ', $words);
    }
}
return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'mayasilk_custom_wp_trim_excerpt');

// Featured Post Meta Boxes


function mayasilk_create_metabox() {
    add_meta_box( 'mayasilk_metabox', __( 'Location', 'mayasilk' ), 'mayasilk_metabox', 'post', 'side', 'low' );            
}
             
function mayasilk_metabox() {
	
	global $post;
	
	/* Retrieve metadata values if they already exist. */
	$mayasilk_post_location = get_post_meta( $post->ID, '_mayasilk_post_location', true ); ?>	
	
	<p><label><input type="radio" name="mayasilk_post_location" value="featured" <?php echo esc_attr( $mayasilk_post_location ) == 'featured' ? 'checked="checked"' : '' ?> /> <?php echo __( 'Featured', 'mayasilk' ) ?></label></p>
	<p><label><input type="radio" name="mayasilk_post_location" value="no-display" <?php echo esc_attr( $mayasilk_post_location ) == 'no-display' ? 'checked="checked"' : '' ?> /> <?php echo __( 'Do not display', 'mayasilk' ) ?></label></p>	
		
	<span class="description"><?php _e( 'Post location on the home page', 'mayasilk' ); ?>
	<?php           
}

/* Save post metadata. */
function mayasilk_save_meta( $post_id, $post ) {
	if ( isset( $_POST['mayasilk_post_location'] ) ) {
		update_post_meta( $post_id, '_mayasilk_post_location', strip_tags( $_POST['mayasilk_post_location'] ) );
	}
}
add_action( 'add_meta_boxes', 'mayasilk_create_metabox' );
add_action( 'save_post', 'mayasilk_save_meta', 1, 2 );

// About Me Widget

	
if( ! class_exists( 'Mayasilk_About_Me_Widget' ) ){
	class Mayasilk_About_Me_Widget extends WP_Widget {
		/**
		 * Sets up the widgets name etc
		 */
		public function __construct() {
			parent::__construct(
				'mayasilk_about_me_widget', 
				__( 'About Me', 'mayasilk' ),
				array( 'description' => __( 'Display About Me Block with Image, social icon, short description on your sidebar widget', 'mayasilk' ) 
				),
				array( 'width' => apply_filters( 'mayasilk_widget_width', 380 )  )
			);
		}

		/**
		 * Outputs the content of the widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			extract( $args );

			$instance['uniqid'] = time() .'-'. uniqid(false);
			$image_uri          = apply_filters( 'widget_image_uri', '');
			$subtitle          	= apply_filters( 'widget_subtitle', '');
			$facebook          	= apply_filters( 'widget_facebook', '');
			$twitter          	= apply_filters( 'widget_twitter', '');
			$youtube          	= apply_filters( 'widget_youtube', '');
			$linkedin           = apply_filters( 'widget_linkedin', '');
			$pinterest          = apply_filters( 'widget_pinterest', '');
			$instagram          = apply_filters( 'widget_instagram', '');
			$instagram          = apply_filters( 'widget_instagram', '');
			$description 		= '';
			ob_start();
			?>
				<?php 
					echo $before_widget;
					do_action( 'before_mayasilk_aboutimage_widget', $instance );
					if( isset( $instance['image_uri'] ) && !empty( $instance['image_uri'] ) ){
						$image_uri = $instance['image_uri'] ;
						echo apply_filters( 'mayasilk_image_url', '<img src="'.$instance['image_uri'].'" alt="'.esc_attr__('About Me', 'mayasilk').'"/>', $instance );
					}
					do_action( 'after_mayasilk_boutimage_widge', $instance );
					do_action( 'before_mayasilk_title_widget', $instance );
					if( isset( $instance['title'] ) ){
						$title = apply_filters( 'widget_title', $instance['title'] );
					}
					// print_r($instance);
					// Check if title is set
					if ( isset( $instance['title']  )) {
						echo $before_title . $instance['title'] . $after_title;
					} 
					do_action( 'after_mayasilk_title_widge', $instance );
					do_action( 'before_mayasilk_subtitle_widget', $instance );
					if( isset( $instance['subtitle'] ) && !empty( $instance['subtitle'] ) ){
						$subtitle = $instance['subtitle'] ;
						echo apply_filters( 'mayasilk_subtitle_name', '<h4>'.$subtitle.'</h4>', $instance );
					}
					do_action( 'after_mayasilk_subtitle_widge', $instance );
					do_action( 'before_mayasilk_widget_description', $instance );
					echo '<p>';
					if( isset( $instance['description'] )){
						$description = $instance['custom_description'] ;
					}
					echo apply_filters( 'easy_profile_widget_description', $description, $instance );
					echo '</p>';
					do_action( 'after_mayasilk_widget_description', $instance );
					echo '<div class="about-me-widget-social-icon">';
					do_action( 'after_mayasilk_extendedpage_widge', $instance );
					echo '<div class="extended-page">';
					if( isset( $instance['extended_page'] ) && !empty( $instance['extended_page'] ) ){
						echo ' <a class="about-read-more" href="'. esc_url( get_permalink( $instance['extended_page'] ) ) .'">'. $instance['extended_text'] .'</a>';
					}
					echo '</div>';
					do_action( 'after_mayasilk_extendedpage_widge', $instance );
					echo '<div class="social-right">';
					do_action( 'before_mayasilk_facebook_widge', $instance );					
					if( isset( $instance['facebook'] ) && !empty( $instance['facebook'] ) ){
						$facebook = $instance['facebook'] ;
						echo apply_filters( 'mayasilk_facebook_url', '<span><a target="_blank" href="'.$facebook.'" title="'.esc_attr__('Facebook', 'mayasilk').'"><i class="fa fa-facebook"></i></a></span>', $instance );
					}
					do_action( 'after_mayasilk_facebook_widge', $instance );
					do_action( 'before_mayasilk_twitter_widge', $instance );					
					if( isset( $instance['twitter'] ) && !empty( $instance['twitter'] ) ){
						$twitter = $instance['twitter'] ;
						echo apply_filters( 'mayasilk_twitter_url', '<span><a target="_blank" href="'.$twitter.'" title="'.esc_attr__('Twitter', 'mayasilk').'"><i class="fa fa-twitter"></i></a></span>', $instance );
					}
					do_action( 'after_mayasilk_twitter_widge', $instance );
					do_action( 'before_mayasilk_youtube_widge', $instance );					
					if( isset( $instance['youtube'] ) && !empty( $instance['youtube'] ) ){
						$youtube = $instance['youtube'] ;
						echo apply_filters( 'mayasilk_youtube_url', '<span><a target="_blank" href="'.$youtube.'" title="'.esc_attr__('Youtube', 'mayasilk').'"><i class="fa fa-youtube"></i></a></span>', $instance );
					}
					do_action( 'after_mayasilk_youtube_widge', $instance );
					do_action( 'before_mayasilk_linkedin_widge', $instance );					
					if( isset( $instance['linkedin'] ) && !empty( $instance['linkedin'] ) ){
						$linkedin = $instance['linkedin'] ;
						echo apply_filters( 'mayasilk_linkedin_url', '<span><a target="_blank" href="'.$linkedin.'" title="'.esc_attr__('Linkedin', 'mayasilk').'"><i class="fa fa-linkedin"></i></a></span>', $instance );
					}
					do_action( 'after_mayasilk_linkedin_widge', $instance );
					do_action( 'before_mayasilk_pinterest_widge', $instance );					
					if( isset( $instance['pinterest'] ) && !empty( $instance['pinterest'] ) ){
						$pinterest = $instance['pinterest'] ;
						echo apply_filters( 'mayasilk_pinterest_url', '<span><a target="_blank" href="'.$pinterest.'" title="'.esc_attr__('Pinterest', 'mayasilk').'"><i class="fa fa-pinterest"></i></a></span>', $instance );
					}
					do_action( 'after_mayasilk_pinterest_widge', $instance );
					do_action( 'before_mayasilk_instagram_widge', $instance );					
					if( isset( $instance['instagram'] ) && !empty( $instance['instagram'] ) ){
						$instagram = $instance['instagram'] ;
						echo apply_filters( 'mayasilk_instagram_url', '<span><a target="_blank" href="'.$instagram.'" title="'.esc_attr__('Instagram', 'mayasilk').'"><i class="fa fa-instagram"></i></a></span>', $instance );
					}
					do_action( 'after_mayasilk_instagram_widge', $instance );
					do_action( 'before_mayasilk_googleplus_widge', $instance );					
					if( isset( $instance['googleplus'] ) && !empty( $instance['googleplus'] ) ){
						$googleplus = $instance['googleplus'] ;
						echo apply_filters( 'mayasilk_googleplus_url', '<span><a target="_blank" href="'.$googleplus.'" title="'.esc_attr__('Google Plus', 'mayasilk').'"><i class="fa fa-google-plus"></i></a></span>', $instance );
					}
					do_action( 'after_mayasilk_googleplus_widge', $instance );
					echo '</div>';
				echo '</div>'; ?>
			<?php
			echo $after_widget;
			$html = ob_get_clean();

			echo apply_filters( 'do_easy_profile_widget', $html, $args, $instance );
		}	

		/**
		 * Ouputs the options form on admin
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			$uniqid 	= time().'-'.uniqid(true);
			if ( isset( $instance[ 'image_uri' ] ) ) {
				$image_uri = $instance[ 'image_uri' ];
			}
			else {
				$image_uri = __( '', 'mayasilk' );
			}
			?>
			<div class="mayasilk-widget-form">
				<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'mayasilk' ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php if ( isset ( $instance['title'] ) ) {echo esc_attr( $instance['title'] );} ?>" />
				</p>
				<p><label for="<?php echo $this->get_field_id( 'subtitle' ); ?>"><?php _e( 'Sub Title:', 'mayasilk' ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name( 'subtitle' ); ?>" value="<?php if ( isset ( $instance['subtitle'] ) ) {echo esc_attr( $instance['subtitle'] );} ?>" />
				</p>
				<div class="mayasilk-tabs">
					
						<p>
						  <label for="<?php echo $this->get_field_id('image_uri'); ?>"><?php _e( 'Image', 'mayasilk' ) ?></label><br />
							<img class="custom_media_image" src="<?php echo $image_uri; ?>" style="margin:0;padding:0;max-width:100px;float:left;display:inline-block" />
							<input type="text" class="widefat custom_media_url" name="<?php echo $this->get_field_name('image_uri'); ?>" id="<?php echo $this->get_field_id('image_uri'); ?>" value="<?php if ( isset ( $instance['image_uri'] ) ) {echo esc_url( $instance['image_uri'] );} ?>">
						   </p>
						   <p>
							<input type="button" value="<?php _e( 'Upload Image', 'mayasilk' ); ?>" class="button custom_media_upload" id="custom_image_uploader"/>
						</p>
					<div id="mayasilk-tab-<?php echo $uniqid;?>-2">
						<?php do_action( 'before_mayasilk_widget_description_tab', $instance );?>
						<textarea id="<?php echo $this->get_field_id( 'custom_description' ); ?>" name="<?php echo $this->get_field_name( 'custom_description' ); ?>" class="widefat" rows="6" cols="5" ><?php if ( isset ( $instance['custom_description'] ) ) { echo esc_attr( $instance['custom_description'] ); } ?></textarea>
						</p>
						<p><label for="<?php echo $this->get_field_id( 'extended_page' ); ?>"><?php _e( 'Choose your extended "About Me" page. This will be the page linked to at the end of your author description.', 'mayasilk' ); ?></label>
							<?php 
							wp_dropdown_pages( 
								array( 
									'name' 				=> $this->get_field_name( 'extended_page' ), 
									'id' 				=> $this->get_field_id( 'extended_page' ), 
									'class' 			=> 'widefat', 
									'show_option_none' 	=> __( 'None', 'mayasilk'), 
									'selected' 			=> ( isset ( $instance['extended_page'] ) ) ? esc_attr( $instance['extended_page'] ) : ''
								) 
							); ?>
						</p>
						<p><label for="<?php echo $this->get_field_id( 'extended_text' ); ?>"><?php _e( 'Extended page link text:', 'mayasilk' ) ?></label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'extended_text' ); ?>" name="<?php echo $this->get_field_name( 'extended_text' ); ?>" value="<?php if ( isset ( $instance['extended_text'] ) ) { echo esc_attr( $instance['extended_text'] ); }else{ _e( 'Read More&#46;&#46;&#46;', 'mayasilk' ); } ?>" />
						</p>
						<?php do_action( 'after_mayasilk_widget_description_tab', array( 'id' => $uniqid, 'instance' => $instance, 'this' => ( isset( $this ) ) ? $this : array() ) );?>
					</div> <!-- end tab 2 -->
					<?php do_action( 'do_mayasilk_widget_tabcontent', array( 'id' => $uniqid, 'instance' => $instance, 'this' => ( isset( $this ) ) ? $this : array() ) );?>
				</div>	
				<p><label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php _e( 'Facebook:', 'mayasilk' ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" value="<?php if ( isset ( $instance['facebook'] ) ) {echo esc_attr( $instance['facebook'] );} ?>" />
				</p>
				<p><label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php _e( 'Twitter:', 'mayasilk' ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" value="<?php if ( isset ( $instance['twitter'] ) ) {echo esc_attr( $instance['twitter'] );} ?>" />
				</p>
				<p><label for="<?php echo $this->get_field_id( 'youtube' ); ?>"><?php _e( 'Youtube:', 'mayasilk' ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'youtube' ); ?>" name="<?php echo $this->get_field_name( 'youtube' ); ?>" value="<?php if ( isset ( $instance['youtube'] ) ) {echo esc_attr( $instance['youtube'] );} ?>" />
				</p>
				<p><label for="<?php echo $this->get_field_id( 'linkedin' ); ?>"><?php _e( 'Linkedin:', 'mayasilk' ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'linkedin' ); ?>" name="<?php echo $this->get_field_name( 'linkedin' ); ?>" value="<?php if ( isset ( $instance['linkedin'] ) ) {echo esc_attr( $instance['linkedin'] );} ?>" />
				</p>
				<p><label for="<?php echo $this->get_field_id( 'pinterest' ); ?>"><?php _e( 'Pinterest:', 'mayasilk' ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'pinterest' ); ?>" name="<?php echo $this->get_field_name( 'pinterest' ); ?>" value="<?php if ( isset ( $instance['pinterest'] ) ) {echo esc_attr( $instance['pinterest'] );} ?>" />
				</p>
				<p><label for="<?php echo $this->get_field_id( 'instagram' ); ?>"><?php _e( 'Instagram:', 'mayasilk' ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'instagram' ); ?>" name="<?php echo $this->get_field_name( 'instagram' ); ?>" value="<?php if ( isset ( $instance['instagram'] ) ) {echo esc_attr( $instance['instagram'] );} ?>" />
				</p>
				<p><label for="<?php echo $this->get_field_id( 'googleplus' ); ?>"><?php _e( 'Google Plus:', 'mayasilk' ) ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'googleplus' ); ?>" name="<?php echo $this->get_field_name( 'googleplus' ); ?>" value="<?php if ( isset ( $instance['googleplus'] ) ) {echo esc_attr( $instance['googleplus'] );} ?>" />
				</p>
			</div>
			<?php
		}


		/**
		 * Processing widget options on save
		 *
		 * @param array $new_instance The new options
		 * @param array $old_instance The previous options
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			// Fields
			$instance['title'] 				= ( isset( $new_instance['title'] ) ) ? strip_tags($new_instance['title']) : '';			
			$instance['subtitle'] 				= ( isset( $new_instance['subtitle'] ) ) ? strip_tags($new_instance['subtitle']) : '';
			$instance['description']		= ( isset( $new_instance['description'] ) ) ? strip_tags($new_instance['description']) : '';
			$instance['custom_description']	= ( isset( $new_instance['custom_description'] ) ) ? strip_tags($new_instance['custom_description']) : '';
			$instance['extended_page']		= ( isset( $new_instance['extended_page'] ) ) ? strip_tags($new_instance['extended_page']) : '';
			$instance['extended_text']		= ( isset( $new_instance['extended_text'] ) ) ? strip_tags($new_instance['extended_text']) : '';
			$instance['image_uri'] = ( ! empty( $new_instance['image_uri'] ) ) ? esc_url_raw( 
			$new_instance['image_uri'] ) : '';
			$instance['facebook'] = ( ! empty( $new_instance['facebook'] ) ) ? esc_url_raw( 
			$new_instance['facebook'] ) : '';
			$instance['twitter'] = ( ! empty( $new_instance['twitter'] ) ) ? esc_url_raw( 
			$new_instance['twitter'] ) : '';
			$instance['youtube'] = ( ! empty( $new_instance['youtube'] ) ) ? esc_url_raw( 
			$new_instance['youtube'] ) : '';
			$instance['linkedin'] = ( ! empty( $new_instance['linkedin'] ) ) ? esc_url_raw( 
			$new_instance['linkedin'] ) : '';
			$instance['pinterest'] = ( ! empty( $new_instance['pinterest'] ) ) ? esc_url_raw( 
			$new_instance['pinterest'] ) : '';
			$instance['instagram'] = ( ! empty( $new_instance['instagram'] ) ) ? esc_url_raw( 
			$new_instance['instagram'] ) : '';
			$instance['googleplus'] = ( ! empty( $new_instance['googleplus'] ) ) ? esc_url_raw( 
			$new_instance['googleplus'] ) : '';


			$instance = apply_filters( 'save_mayasilk_widget_instance', $instance, $new_instance );

			return $instance;
		}
	}

	// register widget
	function register_mayasilk_widget() {
	    register_widget( 'Mayasilk_About_Me_Widget' );
	}
	add_action( 'widgets_init', 'register_mayasilk_widget' );
	
	function mayasilk_wdScript($hook) {
		if ( 'widgets.php' != $hook ) {
			return;
		}
			wp_enqueue_media();
			wp_enqueue_script( 'helloscript', get_template_directory_uri() . '/js/image-upload-widget.js' , __FILE__ );
	}
	add_action( 'admin_enqueue_scripts', 'mayasilk_wdScript' );
}

// Add class to tag list

function mayasilk_add_class_the_tags($html){
    $postid = get_the_ID();
    $html = str_replace('<a','<a class="tag-list"',$html);
    return $html;
}
add_filter('the_tags','mayasilk_add_class_the_tags');

// Pagination

function mayasilk_numeric_posts_nav() {

	if( is_singular() )
		return;

	global $wp_query;

	/** Stop execution if there's only 1 page */
	if( $wp_query->max_num_pages <= 1 )
		return;

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = intval( $wp_query->max_num_pages );

	/**	Add current page to the array */
	if ( $paged >= 1 )
		$links[] = $paged;

	/**	Add the pages around the current page to the array */
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<div class="navigation"><ul>' . "\n";

	/**	Previous Post Link */
	if ( get_previous_posts_link() )
		printf( '<li>%s</li>' . "\n", get_previous_posts_link() );

	/**	Link to first page, plus ellipses if necessary */
	if ( ! in_array( 1, $links ) ) {
		$class = 1 == $paged ? ' class="active"' : '';

		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

		if ( ! in_array( 2, $links ) )
			echo '<li></li>';
	}

	/**	Link to current page, plus 2 pages in either direction if necessary */
	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

	/**	Link to last page, plus ellipses if necessary */
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) )
			echo '<li></li>' . "\n";

		$class = $paged == $max ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}

	/**	Next Post Link */
	if ( get_next_posts_link() )
		printf( '<li>%s</li>' . "\n", get_next_posts_link() );

	echo '</ul></div>' . "\n";

}

// Social Share link

function maysilk_social(){
	?>
		<?php if ( get_theme_mod( 'footer_social_facebook' ) ) : ?>
		<span><a target="_blank" href="<?php echo esc_url(get_theme_mod( 'footer_social_facebook' )); ?>" title="<?php esc_attr__( 'Facebook', 'mayasilk' ) ?>"><i class="fa fa-facebook"></i></a></span>
		<?php endif; ?>
		<?php if ( get_theme_mod( 'footer_social_linkedin' ) ) : ?>
		<span><a target="_blank" href="<?php echo esc_url(get_theme_mod( 'footer_social_linkedin' )); ?>" title="<?php esc_attr__( 'Linkedin', 'mayasilk' ) ?>"><i class="fa fa-linkedin"></i></a></span>
		<?php endif; ?>
		<?php if ( get_theme_mod( 'footer_social_instagram' ) ) : ?>
		<span><a target="_blank" href="<?php echo esc_url(get_theme_mod( 'footer_social_instagram' )); ?>" title="<?php esc_attr__( 'Instagram', 'mayasilk' ) ?>"><i class="fa fa-instagram"></i></a></span>
		<?php endif; ?>
		<?php if ( get_theme_mod( 'footer_social_googleplus' ) ) : ?>
		<span><a target="_blank" href="<?php echo esc_url(get_theme_mod( 'footer_social_googleplus' )); ?>" title="<?php esc_attr__( 'Google Plus', 'mayasilk' ) ?>"><i class="fa fa-google-plus"></i></a></span>
		<?php endif; ?>
		<?php if ( get_theme_mod( 'footer_social_twitter' ) ) : ?>
		<span><a target="_blank" href="<?php echo esc_url(get_theme_mod( 'footer_social_twitter' )); ?>" title="<?php esc_attr__( 'Twitter', 'mayasilk' ) ?>"><i class="fa fa-twitter"></i></a></span>
		<?php endif; ?>
		<?php if ( get_theme_mod( 'footer_social_youtube' ) ) : ?>
		<span><a target="_blank" href="<?php echo esc_url(get_theme_mod( 'footer_social_youtube' )); ?>" title="<?php esc_attr__( 'Youtube', 'mayasilk' ) ?>"><i class="fa fa-youtube-play"></i></a></span>
		<?php endif; ?>
		<?php if ( get_theme_mod( 'footer_social_pinterest' ) ) : ?>
		<span><a target="_blank" href="<?php echo esc_url(get_theme_mod( 'footer_social_pinterest' )); ?>" title="<?php esc_attr__( 'Pinterest', 'mayasilk' ) ?>"><i class="fa fa-pinterest"></i></a></span>
		<?php endif; ?>
<?php
}