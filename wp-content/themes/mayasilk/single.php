<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Maya_Silk
 * @since Maya Silk 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', get_post_format() ); ?>

					<div id="post-nav">
						<?php $prevPost = get_previous_post();
							if (!empty( $prevPost )) {
								$args = array(
									'posts_per_page' => 1,
									'include' => $prevPost->ID
								);
								$prevPost = get_posts($args);
								foreach ($prevPost as $post) {
									setup_postdata($post);
						?>
							<div class="post-previous">
							<a href="<?php the_permalink(); ?>"><?php if(has_post_format('video')): ?>	
							<?php if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'mayasilk_next_prev_image_link', array('class' => 'img-responsive'));
							}?>
							<a href='<?php the_permalink(); ?>'><img class='video-post-image-next-previous' src="<?php echo get_template_directory_uri(); ?>/img/video-sidebar.png" alt=''></a>
							<?php else : ?>
							<?php if ( has_post_thumbnail() ) {
							the_post_thumbnail( 'mayasilk_next_prev_image_link', array('class' => 'img-responsive'));
							}
							?><?php endif; ?></a>
								<a class="previous" href="<?php the_permalink(); ?>"><?php _e( '&laquo; Previous Story', 'mayasilk' ); ?></a>
								<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
							</div>
						<?php
									wp_reset_postdata();
								} //end foreach
							} // end if
							 
							$nextPost = get_next_post();
							if (!empty( $nextPost)) {
								$args = array(
									'posts_per_page' => 1,
									'include' => $nextPost->ID
								);
								$nextPost = get_posts($args);
								foreach ($nextPost as $post) {
									setup_postdata($post);
						?>
							<div class="post-next">
								<a href="<?php the_permalink(); ?>"><?php if(has_post_format('video')): ?>	
								<?php if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'mayasilk_next_prev_image_link', array('class' => 'next-image'));
								}?>
								<a href='<?php the_permalink(); ?>'><img class='video-post-image-next' src="<?php echo get_template_directory_uri(); ?>/img/video-sidebar.png" alt=''></a>
								<?php else : ?>
								<?php if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'mayasilk_next_prev_image_link', array('class' => 'next-image'));
								}
								?><?php endif; ?></a>
								<a class="next" href="<?php the_permalink(); ?>"><?php _e( 'Next Story &raquo;', 'mayasilk' ); ?></a>
								<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
							</div>
						<?php
									wp_reset_postdata();
								} //end foreach
							} // end if
						?>
					</div>

				<?php comments_template( '', true ); ?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>