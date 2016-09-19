<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Maya_Silk
 * @since Maya Silk 1.0
 */
?>
	</div><!-- #main .wrapper -->
	<footer id="colophon" role="contentinfo">
	<?php if ( get_theme_mod( 'footer_logo' ) || get_theme_mod( 'footer_content' ) ) : ?>
		<div class="footer-top">
			<div class="site">
				<div class="footer-widget">
				
					<div class="footer-logo">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" id="site-logo" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<img src="<?php echo get_theme_mod('footer_logo', esc_url('', 'mayasilk')); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
						</a>		   
					</div>
					<div class="site-description">	
						<p><?php echo get_theme_mod('footer_content', esc_html__('', 'mayasilk')); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="footer-bottom">
			<div class="site">
				<div class="footer-menu">
					<?php wp_nav_menu( array( 'theme_location' => 'footermenu', 'container' => '', 'menu_class' => 'footer-menu-item', 'menu_id' => 'footer-menu-item', ) ); ?>
				</div>
				<div class="footer-social-icon">
					<?php maysilk_social(); ?>
				</div>
				<div class="copyright">
				<?php if ( get_theme_mod( 'footer_copyright_text' ) ) : ?>
					<?php echo get_theme_mod('footer_copyright_text', esc_html__('', 'mayasilk')); ?>
				<?php else : ?>
					<?php esc_attr_e( '&copy;', 'mayasilk' ); ?> <?php echo date( 'Y' ); ?><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
					<?php bloginfo( 'name' ); ?></a>
				<?php endif; ?>
				</div><!-- end of .copyright -->
				<div class="powered-by">
					<a href="<?php echo esc_url( 'http://abcthemes.net/mayasilk/' ); ?>" title="<?php esc_attr_e( 'Mayasilk', 'mayasilk' ); ?>"><?php _e('Mayasilk', 'mayasilk'); ?></a>
					<?php esc_attr_e( 'powered by', 'mayasilk' ); ?> <a href="<?php echo esc_url( 'http://wordpress.org/' ); ?>" title="<?php esc_attr_e( 'WordPress', 'mayasilk' ); ?>"><?php _e('WordPress', 'mayasilk'); ?></a>
				</div><!-- end .powered -->
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>