<?php
/**
 * Display the date filters above the Donations table.
 *
 * @author  Studio 164a
 * @package Charitable/Admin View/Donations Page
 * @since   1.4.0
 */

?>
<a href="#charitable-donations-filter-modal" class="charitable-donations-filter button  dashicons-before dashicons-filter trigger-modal hide-if-no-js" data-trigger-modal><?php _e( 'Filter', 'charitable' ) ?></a>

<?php if ( isset( $_GET['post_type'] ) && count( $_GET ) > 1 ) : ?>
	<a href="<?php echo esc_url( add_query_arg( array( 'post_type' => Charitable::DONATION_POST_TYPE ), admin_url( 'edit.php' ) ) ) ?>" class="charitable-donations-clear button dashicons-before dashicons-clear"><?php _e( 'Clear Filters', 'charitable' ) ?></a>
<?php endif; ?>
