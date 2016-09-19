<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Maya_Silk
 * @since Maya Silk 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<?php if ( is_search() || !is_single() ) : // Only display Excerpts for Search ?>
			<?php endif; ?>
			<?php if ( is_single() ) : ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php else : ?>
			<h1 class="entry-title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h1>
			<?php endif; // is_single() ?>
			<?php if ( comments_open() ) : ?>
				<div class="entry-meta">
					<span><i class="fa fa-user"></i> <span itemprop="author"><?php the_author_posts_link(); ?></span></span>
					<span class="date-and-time"><i class="fa fa-calendar"></i><span itemprop="datePublished"><?php the_time( get_option('date_format') ); ?></span></span>
					<span><i class="fa fa-tags"></i><?php the_category(','); ?></span>
					<span><i class="fa fa-comments"></i><span>
					<?php comments_popup_link( '' . __( 'Leave a comment', 'mayasilk' ) . '', __( '1 comment', 'mayasilk' ), __( '% comments', 'mayasilk' ) ); ?>
					</span></span>
				</div><!-- Post Meta -->
			<?php endif; // comments_open() ?>
		</header><!-- .entry-header -->

		<?php if ( is_search() || !is_single() ) : // Only display Excerpts for Search ?>
		<div class="entry-summary">
			<?php the_content(); ?>
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
			<?php if ( is_singular() && get_the_author_meta( 'description' ) && is_multi_author() ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries. ?>
				<div class="author-info">
					<div class="author-avatar">
						<?php
						/** This filter is documented in author.php */
						$author_bio_avatar_size = apply_filters( 'mayasilk_author_bio_avatar_size', 68 );
						echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
						?>
					</div><!-- .author-avatar -->
					<div class="author-description">
						<h2><?php printf( __( 'About %s', 'mayasilk' ), get_the_author() ); ?></h2>
						<p><?php the_author_meta( 'description' ); ?></p>
						<div class="author-link">
							<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
								<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'mayasilk' ), get_the_author() ); ?>
							</a>
						</div><!-- .author-link	-->
					</div><!-- .author-description -->
				</div><!-- .author-info -->
			<?php endif; ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
