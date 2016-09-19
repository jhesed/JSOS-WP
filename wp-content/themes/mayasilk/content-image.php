<?php
/**
 * The template for displaying posts in the Image post format
 *
 * @package WordPress
 * @subpackage Maya_Silk
 * @since Maya Silk 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( is_search() || !is_single() ) : // Only display Excerpts for Search ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div id="custom_slider" class="flexslider">
				<ul class="slides">
					<?php
					/* The loop */
					if ( get_post_gallery() ) :
						$gallery = get_post_gallery( get_the_ID(), false );
						
						/* Loop through all the image and output them one by one */
						foreach( $gallery['src'] as $src ) : ?>
							<li class="gallery-item">
								<dt class="gallery-slides">
									<img src="<?php echo $src; ?>" class="my-custom-class" alt="<?php esc_attr__( 'Gallery image', 'mayasilk' ) ?>" />
								</dt>
		
							</li>
							<?php
						endforeach;
					endif;
					?>
				</ul>
			</div>
		</div>
		<?php endif; ?>
		<header class="entry-header">
		<?php if ( is_single() ) : ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php else : ?>
			<h1 class="entry-title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h1>
		<?php endif; // is_single() ?>
				<div class="entry-meta">
					<span><i class="fa fa-user"></i> <span itemprop="author"><?php the_author_posts_link(); ?></span></span>
					<span class="date-and-time"><i class="fa fa-calendar"></i><span itemprop="datePublished"><?php the_time( get_option('date_format') ); ?></span></span>
					<span><i class="fa fa-tags"></i><?php the_category(','); ?></span>
					<span><i class="fa fa-comments"></i><span>
					<?php comments_popup_link( '' . __( 'Leave a comment', 'mayasilk' ) . '', __( '1 comment', 'mayasilk' ), __( '% comments', 'mayasilk' ) ); ?>
					</span></span>
				</div><!-- Post Meta -->
		</header>
		<?php if ( is_search() || !is_single() ) : // Only display Excerpts for Search ?>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
		<?php else : ?>
		<div class="entry-content">
			<?php the_content(); ?>
			<div class="post-tags">
				<?php the_tags( __( 'Tags:', 'mayasilk' ),',', '', '' ); ?>
			</div>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'mayasilk' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
		<?php endif; ?>

		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'mayasilk' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
