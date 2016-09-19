<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Benevolent
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function benevolent_body_classes( $classes ) {
	
    global $post;
    
    // Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
    
    // Adds a class of custom-background-image to sites with a custom background image.
	if ( get_background_image() ) {
		$classes[] = 'custom-background-image';
	}
    
    // Adds a class of custom-background-color to sites with a custom background color.
    if ( get_background_color() != 'ffffff' ) {
		$classes[] = 'custom-background-color';
	}
    
    if( !( is_active_sidebar( 'right-sidebar' )) || is_page_template( 'template-home.php' ) || is_search() ) {
		$classes[] = 'full-width';	
	}
    
    if( is_page() ){
		$sidebar_layout = get_post_meta( $post->ID, 'benevolent_sidebar_layout', true );
        if( $sidebar_layout == 'no-sidebar' )
		$classes[] = 'full-width';
	}
    
    if( get_theme_mod( 'benevolent_ed_slider' ) ){
	   $classes[] = 'has-slider';
	}
    
	return $classes;
}
add_filter( 'body_class', 'benevolent_body_classes' );

if( ! function_exists( 'benevolent_excerpt' ) ):  
/**
 * benevolent_excerpt can truncate a string up to a number of characters while preserving whole words and HTML tags
 *
 * @param string $text String to truncate.
 * @param integer $length Length of returned string, including ellipsis.
 * @param string $ending Ending to be appended to the trimmed string.
 * @param boolean $exact If false, $text will not be cut mid-word
 * @param boolean $considerHtml If true, HTML tags would be handled correctly
 *
 * @return string Trimmed string.
 * 
 * @link http://alanwhipple.com/2011/05/25/php-truncate-string-preserving-html-tags-words/
 */
function benevolent_excerpt($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true) {
	$text = strip_shortcodes( $text );
    $text = benevolent_strip_single( 'img', $text );
    $text = benevolent_strip_single( 'a', $text );
    
    if ($considerHtml) {
		// if the plain text is shorter than the maximum length, return the whole text
		if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
			return $text;
		}
		// splits all html-tags to scanable lines
		preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
		$total_length = strlen($ending);
		$open_tags = array();
		$truncate = '';
		foreach ($lines as $line_matchings) {
			// if there is any html-tag in this line, handle it and add it (uncounted) to the output
			if (!empty($line_matchings[1])) {
				// if it's an "empty element" with or without xhtml-conform closing slash
				if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
				// if tag is a closing tag
				} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
					// delete tag from $open_tags list
					$pos = array_search($tag_matchings[1], $open_tags);
					if ($pos !== false) {
					unset($open_tags[$pos]);
					}
				// if tag is an opening tag
				} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
					// add tag to the beginning of $open_tags list
					array_unshift($open_tags, strtolower($tag_matchings[1]));
				}
				// add html-tag to $truncate'd text
				$truncate .= $line_matchings[1];
			}
			// calculate the length of the plain text part of the line; handle entities as one character
			$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
			if ($total_length+$content_length> $length) {
				// the number of characters which are left
				$left = $length - $total_length;
				$entities_length = 0;
				// search for html entities
				if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
					// calculate the real length of all entities in the legal range
					foreach ($entities[0] as $entity) {
						if ($entity[1]+1-$entities_length <= $left) {
							$left--;
							$entities_length += strlen($entity[0]);
						} else {
							// no more characters left
							break;
						}
					}
				}
				$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
				// maximum lenght is reached, so get off the loop
				break;
			} else {
				$truncate .= $line_matchings[2];
				$total_length += $content_length;
			}
			// if the maximum length is reached, get off the loop
			if($total_length>= $length) {
				break;
			}
		}
	} else {
		if (strlen($text) <= $length) {
			return $text;
		} else {
			$truncate = substr($text, 0, $length - strlen($ending));
		}
	}
	// if the words shouldn't be cut in the middle...
	if (!$exact) {
		// ...search the last occurance of a space...
		$spacepos = strrpos($truncate, ' ');
		if (isset($spacepos)) {
			// ...and cut the text in this position
			$truncate = substr($truncate, 0, $spacepos);
		}
	}
	// add the defined ending to the text
	$truncate .= $ending;
	if($considerHtml) {
		// close all unclosed html-tags
		foreach ($open_tags as $tag) {
			$truncate .= '</' . $tag . '>';
		}
	}
	return $truncate;
}
endif; // End function_exists

/**
 * Custom Bread Crumb
 *
 * @link http://www.qualitytuts.com/wordpress-custom-breadcrumbs-without-plugin/
 */
function benevolent_breadcrumbs_cb() {
 
  $showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
  $delimiter = esc_html( get_theme_mod( 'benevolent_breadcrumb_separator', __( '>', 'benevolent' ) ) ); // delimiter between crumbs
  $home = esc_html( get_theme_mod( 'benevolent_breadcrumb_home_text', __( 'Home', 'benevolent' ) ) ); // text for the 'Home' link
  $showCurrent = get_theme_mod( 'benevolent_ed_current', '1' ); // 1 - show current post/page title in breadcrumbs, 0 - don't show
  $before = '<span class="current">'; // tag before the current crumb
  $after = '</span>'; // tag after the current crumb
 
  global $post;
  $homeLink = esc_url( home_url( ) );
 
  if (is_home() || is_front_page()) {
 
    if ($showOnHome == 1) echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a></div>';
 
  } else {
 
    echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
 
    if ( is_category() ) {
      $thisCat = get_category(get_query_var('cat'), false);
      if ($thisCat->parent != 0) echo get_category_parents($thisCat->parent, TRUE, ' ' . $delimiter . ' ');
      echo $before . single_cat_title('', false) . $after;
 
    } elseif ( is_search() ) {
      echo $before . esc_html__( 'Search Result', 'benevolent' ) . $after;
 
    } elseif ( is_day() ) {
      echo '<a href="' . esc_url( get_year_link( get_the_time('Y') ) ) . '">' . esc_html( get_the_time('Y') ) . '</a> ' . $delimiter . ' ';
      echo '<a href="' . esc_url( get_month_link( get_the_time('Y'), get_the_time('m') ) ) . '">' . esc_html( get_the_time('F') ) . '</a> ' . $delimiter . ' ';
      echo $before . esc_html( get_the_time('d') ) . $after;
 
    } elseif ( is_month() ) {
      echo '<a href="' . esc_url( get_year_link( get_the_time('Y') ) ) . '">' . esc_html( get_the_time('Y') ) . '</a> ' . $delimiter . ' ';
      echo $before . esc_html( get_the_time('F') ) . $after;
 
    } elseif ( is_year() ) {
      echo $before . esc_html( get_the_time('Y') ) . $after;
 
    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . esc_html( $post_type->labels->singular_name ) . '</a>';
        if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . esc_html( get_the_title() ) . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        $cats = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        if ($showCurrent == 0) $cats = preg_replace("#^(.+)\s$delimiter\s$#", "$1", $cats);
        echo $cats;
        if ($showCurrent == 1) echo $before . esc_html( get_the_title() ) . $after;
      }
 
    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . esc_html( $post_type->labels->singular_name ) . $after;
 
    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo '<a href="' . esc_url( get_permalink($parent) ) . '">' . esc_html( $parent->post_title ) . '</a>';
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . esc_html( get_the_title() ) . $after;
 
    } elseif ( is_page() && !$post->post_parent ) {
      if ($showCurrent == 1) echo $before . esc_html( get_the_title() ) . $after;
 
    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . esc_url( get_permalink($page->ID) ) . '">' . esc_html( get_the_title( $page->ID ) ) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      for ($i = 0; $i < count($breadcrumbs); $i++) {
        echo $breadcrumbs[$i];
        if ($i != count($breadcrumbs)-1) echo ' ' . $delimiter . ' ';
      }
      if ($showCurrent == 1) echo ' ' . $delimiter . ' ' . $before . esc_html( get_the_title() ) . $after;
 
    } elseif ( is_tag() ) {
      echo $before . esc_html( single_tag_title('', false) ) . $after;
 
    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . esc_html( $userdata->display_name ) . $after;
 
    } 
 
    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __( 'Page', 'benevolent' ) . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }
 
    echo '</div>';
 
  }
} // end benevolent_breadcrumbs()
add_action( 'benevolent_breadcrumbs', 'benevolent_breadcrumbs_cb' );

/**
 * Social Links Callback 
 */
function benevolent_social_links_cb(){
    
    $facebook  = get_theme_mod( 'benevolent_facebook' );
    $twitter   = get_theme_mod( 'benevolent_twitter' );
    $pinterest = get_theme_mod( 'benevolent_pinterest' );
    $linkedin  = get_theme_mod( 'benevolent_linkedin' );
    $gplus     = get_theme_mod( 'benevolent_gplus' );
    $instagram = get_theme_mod( 'benevolent_instagram' );
    $youtube   = get_theme_mod( 'benevolent_youtube' );
    
    if( $facebook || $twitter || $pinterest || $linkedin || $gplus || $instagram || $youtube ){
    
    ?>
	<ul class="social-networks">
		<?php if( $facebook ){ ?>
        <li><a href="<?php echo esc_url( $facebook ); ?>" class="fa fa-facebook" target="_blank" title="<?php esc_attr_e( 'Facebook', 'benevolent' );?>"></a></li>
		<?php } if( $twitter ){ ?>
        <li><a href="<?php echo esc_url( $twitter ); ?>" class="fa fa-twitter" target="_blank" title="<?php esc_attr_e( 'Twitter', 'benevolent' );?>"></a></li>
        <?php } if( $pinterest ){ ?>
        <li><a href="<?php echo esc_url( $pinterest ); ?>" class="fa fa-pinterest" target="_blank" title="<?php esc_attr_e( 'Pinterest', 'benevolent' );?>"></a></li>
		<?php } if( $linkedin ){ ?>
        <li><a href="<?php echo esc_url( $linkedin ); ?>" class="fa fa-linkedin" target="_blank" title="<?php esc_attr_e( 'LinkedIn', 'benevolent' );?>"></a></li>
        <?php } if( $gplus ){ ?>
        <li><a href="<?php echo esc_url( $gplus ); ?>" class="fa fa-google-plus" target="_blank" title="<?php esc_attr_e( 'Google Plus', 'benevolent' );?>"></a></li>
        <?php } if( $instagram ){ ?>
        <li><a href="<?php echo esc_url( $instagram ); ?>" class="fa fa-instagram" target="_blank" title="<?php esc_attr_e( 'Instagram', 'benevolent' );?>"></a></li>
		<?php } if( $youtube ){ ?>
        <li><a href="<?php echo esc_url( $youtube ); ?>" class="fa fa-youtube-play" target="_blank" title="<?php esc_attr_e( 'YouTube', 'benevolent' );?>"></a></li>
        <?php } ?>
	</ul>
    <?php
    }
}
add_action( 'benevolent_social_links' , 'benevolent_social_links_cb' );

/** 
 * Hook to move comment text field to the bottom in WP 4.4 
 *
 * @link http://www.wpbeginner.com/wp-tutorials/how-to-move-comment-text-field-to-bottom-in-wordpress-4-4/  
 */
function benevolent_move_comment_field_to_bottom( $fields ) {
    $comment_field = $fields['comment'];
    unset( $fields['comment'] );
    $fields['comment'] = $comment_field;
    return $fields;
}
add_filter( 'comment_form_fields', 'benevolent_move_comment_field_to_bottom' );

/**
 * Callback function for Comment List *
 * 
 * @link https://codex.wordpress.org/Function_Reference/wp_list_comments 
 */
function benevolent_theme_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
	extract($args, EXTR_SKIP);

	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
	<<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ) ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
	<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php endif; ?>
	
    <footer class="comment-meta">
    
        <div class="comment-author vcard">
    	<?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
    	<?php printf( __( '<b class="fn">%s</b>', 'benevolent' ), get_comment_author_link() ); ?>
    	</div>
    	<?php if ( $comment->comment_approved == '0' ) : ?>
    		<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'benevolent' ); ?></em>
    		<br />
    	<?php endif; ?>
    
    	<div class="comment-metadata commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ); ?>"><time datetime="<?php comment_date(); ?>">
    		<?php
    			/* translators: 1: date, 2: time */
    			printf( __( '%s', 'benevolent' ), get_comment_date() ); ?></time></a><?php edit_comment_link( __( '(Edit)', 'benevolent' ), '  ', '' );
    		?>
    	</div>
    </footer>
    
    <div class="comment-content"><?php comment_text(); ?></div>

	<div class="reply">
	<?php comment_reply_link( array_merge( $args, array( 'add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
	</div>
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; ?>
<?php
}

/**
 * Fuction to get Sections 
 */
function benevolent_get_sections(){
    
    $sections = array( 
        'intro-section' => array(
            'class' => 'intro',
            'id'    => 'intro'    
        ),
        'community-section' => array(
            'class' => 'our-community',
            'id'    => 'community'
        ),
        'stats-section' => array(
            'class' => 'stats',
            'id'    => 'stats'
        ),
        'blog-section' => array(
            'class' => 'blog-section',
            'id'    => 'blog'
        ),
        'sponsor-section' => array(
            'class' => 'sponsors',
            'id'    => 'sponsor'
        )              
    );
        
    $enabled_section = array();
    foreach ( $sections as $section ) {
        
        if ( esc_attr( get_theme_mod( 'benevolent_ed_' . $section['id'] . '_section' ) ) == 1 ){
            $enabled_section[] = array(
                'id' => $section['id'],
                'class' => $section['class']
            );
        }
    }
    return $enabled_section;
}
 
/**
 * Callback for Banner Slider 
 */
function benevolent_slider_cb(){

    $slider_caption  = get_theme_mod( 'benevolent_slider_caption', '1' );
    $slider_readmore = get_theme_mod( 'benevolent_slider_readmore', __( 'Learn More', 'benevolent' ) );
    $slider_cat      = get_theme_mod( 'benevolent_slider_cat' );
    
    if( $slider_cat ){
        $slider_qry = new WP_Query( array( 
            'post_type'             => 'post', 
            'post_status'           => 'publish',
            'posts_per_page'        => -1,                    
            'cat'                   => $slider_cat,
            'ignore_sticky_posts'   => true
        ) );
        if( $slider_qry->have_posts() ){
            echo '<div class="banner"><div class="flexslider"><ul class="slides">';
            
            while( $slider_qry->have_posts()) {
                $slider_qry->the_post();
                if( has_post_thumbnail() ){
                ?>
    			<li>
    				<?php 
                    the_post_thumbnail( 'benevolent-slider' ); 
                    if( $slider_caption ){
                    ?>
                    <div class="banner-text">
    					<div class="container">
    						<div class="text">
    							<strong class="main-title"><?php the_title(); ?></strong>
    							<?php if( has_excerpt() ) the_excerpt(); ?>
    							<a href="<?php the_permalink(); ?>" class="btn-learn"><?php echo esc_html( $slider_readmore );?></a>
    						</div>
    					</div>
    				</div>
                    <?php
                    }
                    ?>
    			</li>
                <?php 
                }
            } 
            echo '</ul></div></div>';
            wp_reset_postdata(); 
        }
    }   
}
add_action( 'benevolent_slider', 'benevolent_slider_cb' );
 
/**
 * Callback Function for Promotional Block 
 */
function benevolent_promotional_cb(){

    $ed_promotional_section    = get_theme_mod( 'benevolent_ed_promotional_section' );
    $promotional_section_title = get_theme_mod( 'benevolent_promotional_section_title' );
    $promotional_button_text   = get_theme_mod( 'benevolent_promotional_button_text' );
    $promotional_button_url    = get_theme_mod( 'benevolent_promotional_button_url' );
    $promotional_section_bg    = get_theme_mod( 'benevolent_promotional_section_bg' );
    
    if( $ed_promotional_section ){
    ?>
    <div class="promotional-block" <?php if( $promotional_section_bg ) echo 'style="background: url(' . esc_url( $promotional_section_bg ) . '); background-size: cover; background-repeat: no-repeat; background-position: center;"';?>>
			<div class="container">
				<div class="text">
					<?php 
                    if( $promotional_section_title ) echo '<h3 class="title">' . esc_html( $promotional_section_title ) . '</h3>';
					if( $promotional_button_url && $promotional_button_text ) echo '<a href="' . esc_url( $promotional_button_url ) . '" class="btn-donate" target="_blank">' . esc_html( $promotional_button_text ) . '</a>';
                    ?>
				</div>
			</div>
		</div>
    <?php
    }
}
add_action( 'benevolent_promotional', 'benevolent_promotional_cb' );

/**
 * Helper function for listing Intro section
*/
function benevolent_intro_helper( $image, $logo, $title, $link, $url ){
    
    if( $image ){
        $img = wp_get_attachment_image_src( $image, 'full' );
        $log = wp_get_attachment_image_src( $logo, 'full' );
        
        echo '<div class="columns-3">';
        echo '<div class="img-holder"><img src="' . esc_url( $img[0] ) . '" alt="' . esc_attr( $title ) . '" /></div>';
        
        if( $logo ) echo '<div class="icon-holder"><img src="' . esc_url( $log[0] ) .'" alt="' . esc_attr( $title ) . '" /></div>';
        
		if( $title || $url ){ 
            echo '<div class="text-holder">';
			if( $title ) echo '<strong class="title">' . esc_html( $title ) . '</strong>'; 
			if( $url && $link ) echo '<a class="btn" href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $link ) . '<span class="fa fa-angle-right"></span></a>';
            echo '</div>';
        } 
        echo '</div>';
    }
    
}

/**
 * Helper function for listing sponsor 
*/
function benevolent_sponsor_helper( $logo, $url ){
    
    if( $url ) echo '<a href="' . esc_url( $url ) . '" target="_blank">'; 
    if( $logo ) echo '<div class="columns-5"><img src="' . esc_url( $logo ) . '" alt=""></div>';
    if( $url ) echo '</a>';
     
}

/**
 * Helper function for listing stat counter
*/
function benevolent_stat_helper( $title, $counter ){
    if( $counter ){ ?>
        <div class="columns-4">
			<strong class="number"><?php echo absint( $counter );?></strong>
			<?php if( $title ) echo '<span>' . esc_html( $title ) . '</span>'; ?>
		</div>
    <?php }
}

/**
 * Custom CSS
*/
function benevolent_custom_css(){
    $custom_css = get_theme_mod( 'benevolent_custom_css' );
    if( !empty( $custom_css ) ){
		echo '<style type="text/css">';
		echo wp_strip_all_tags( $custom_css );
		echo '</style>';
	}
}
add_action( 'wp_head', 'benevolent_custom_css', 100 );

if ( ! function_exists( 'benevolent_excerpt_more' ) && ! is_admin() ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... * 
 */
function benevolent_excerpt_more() {
	return ' &hellip; ';
}
add_filter( 'excerpt_more', 'benevolent_excerpt_more' );
endif;

if ( ! function_exists( 'benevolent_excerpt_length' ) ) :
/**
 * Changes the default 55 character in excerpt 
*/
function benevolent_excerpt_length( $length ) {
	return 60;
}
add_filter( 'excerpt_length', 'benevolent_excerpt_length', 999 );
endif;

/**
 * Footer Credits 
*/
function benevolent_footer_credit(){
        
    $text  = '<div class="site-info"><div class="container"><span class="copyright">';
    $text .=  esc_html__( '&copy; ', 'benevolent' ) . date('Y'); 
    $text .= ' <a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>.</span>';
    $text .= '<span class="by">';
    $text .= '<a href="' . esc_url( 'http://raratheme.com/wordpress-themes/benevolent/' ) .'" rel="author" target="_blank">' . esc_html__( 'Benevolent by Rara Theme', 'benevolent' ) . '</a>. ';
    $text .= sprintf( esc_html__( 'Powered by %s', 'benevolent' ), '<a href="'. esc_url( __( 'https://wordpress.org/', 'benevolent' ) ) .'" target="_blank">WordPress</a>.' );
    $text .= '</span></div></div>';
    echo apply_filters( 'benevolent_footer_text', $text );    
}
add_action( 'benevolent_footer', 'benevolent_footer_credit' );

/**
 * Return sidebar layouts for pages
*/
function benevolent_sidebar_layout(){
    global $post;
    
    if( get_post_meta( $post->ID, 'benevolent_sidebar_layout', true ) ){
        return get_post_meta( $post->ID, 'benevolent_sidebar_layout', true );    
    }else{
        return 'right-sidebar';
    }
}

/**
 * Strip specific tags from string
 * @link http://www.altafweb.com/2011/12/remove-specific-tag-from-php-string.html
*/
function benevolent_strip_single( $tag, $string ){
    $string = preg_replace('/<'.$tag.'[^>]*>/i', '', $string);
    $string = preg_replace('/<\/'.$tag.'>/i', '', $string);
    return $string;
} 