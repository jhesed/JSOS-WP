<?php
/**
 * The template for displaying posts in the Quote post format
 *
 * @package WordPress
 * @subpackage Maya_Silk
 * @since Maya Silk 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header>
			<div class="entry-meta quote-meta">
				<span class="date-and-time"><i class="fa fa-calendar"></i><span itemprop="datePublished"><?php the_time( get_option('date_format') ); ?></span></span>
				<span><i class="fa fa-comments"></i><span>
				<?php comments_popup_link( '' . __( 'Leave a comment', 'mayasilk' ) . '', __( '1 comment', 'mayasilk' ), __( '% comments', 'mayasilk' ) ); ?>
				</span></span>
			</div><!-- Post Meta -->
		</header>
		<div class="entry-content">
			<?php the_content(); ?>
		</div><!-- .entry-content -->

		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'mayasilk' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
