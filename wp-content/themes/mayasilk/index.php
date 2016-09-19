<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Maya_Silk
 * @since Maya Silk 1.0
 */

get_header(); ?>
	<?php $args = array( 'posts_per_page' => 5, 'meta_key' => '_mayasilk_post_location', 'meta_value' => 'featured', 'post__not_in' => get_option( 'sticky_posts' ) );
	$featured = new WP_Query( $args );	
	if ( $featured->have_posts() ) : ?>
	<div class="featured-post-slider">
		<div class="site">
			<div class="postslider">
				<ul class="slides">	
				<?php while ( $featured->have_posts() ) : $featured->the_post(); ?>
					<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'mayasilk_featured-post-thumb' ); ?>
					<li class="item-set" style="background-image:url('<?php echo $image[0]; ?>');">
						<div class="featured-post-content">
							<div class="featured-center">
								<div class="featured-center-bg">
									<div class="entry-meta">
										<span class="category-list"><?php the_category(','); ?></span>
									</div>
								<div class="featured-post-title">
									<h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
								</div>
								<div class="featured-date-and-time"><?php the_time( get_option('date_format') ); ?></div>
								<a class="featured-read-more" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Read More', 'mayasilk' ); ?></a>
								</div>
							</div>
						</div>
					</li>
			<?php endwhile; ?>
				</ul>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div id="primary" class="site-content">

		<div id="content" role="main">
		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', get_post_format() ); ?>
			<?php endwhile; ?>

			<?php mayasilk_numeric_posts_nav(); ?>

		<?php else : ?>

			<article id="post-0" class="post no-results not-found">

			<?php if ( current_user_can( 'edit_posts' ) ) :
				// Show a different message to a logged-in user who can add posts.
			?>
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'No posts to display', 'mayasilk' ); ?></h1>
				</header>

				<div class="entry-content">
					<p><?php printf( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'mayasilk' ), admin_url( 'post-new.php' ) ); ?></p>
				</div><!-- .entry-content -->

			<?php else :
				// Show the default message to everyone else.
			?>
				<header class="entry-header">
					<h1 class="entry-title"><?php _e( 'Nothing Found', 'mayasilk' ); ?></h1>
				</header>

				<div class="entry-content">
					<p><?php _e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'mayasilk' ); ?></p>
					<?php get_search_form(); ?>
				</div><!-- .entry-content -->
			<?php endif; // end current_user_can() check ?>

			</article><!-- #post-0 -->

		<?php endif; // end have_posts() check ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
