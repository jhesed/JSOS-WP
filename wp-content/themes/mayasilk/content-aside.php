<?php
/**
 * The template for displaying posts in the Aside post format
 *
 * @package WordPress
 * @subpackage Maya_Silk
 * @since Maya Silk 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="aside">
			<?php if ( is_single() ) : ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php else : ?>
			<h1 class="entry-title">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h1>
			<?php endif; // is_single() ?>
			<?php if ( comments_open() ) : ?>
				<div class="entry-meta aside-meta">
					<span class="date-and-time"><i class="fa fa-calendar"></i><span itemprop="datePublished"><?php the_time( get_option('date_format') ); ?></span></span>
					<span><i class="fa fa-comments"></i><span>
					<?php comments_popup_link( '' . __( 'Leave a comment', 'mayasilk' ) . '', __( '1 comment', 'mayasilk' ), __( '% comments', 'mayasilk' ) ); ?>
					</span></span>
				</div><!-- Post Meta -->
			<?php endif; // comments_open() ?>
			<div class="entry-content">
				<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'mayasilk' ) ); ?>
			</div><!-- .entry-content -->
		</div><!-- .aside -->

		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'mayasilk' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
