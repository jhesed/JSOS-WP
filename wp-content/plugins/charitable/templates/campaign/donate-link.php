<?php 
/**
 * Displays the donate button to be displayed on campaign pages. 
 *
 * Override this template by copying it to yourtheme/charitable/campaign/donate-link.php
 *
 * @author  Studio 164a
 * @since   1.0.0
 */

$campaign = charitable_get_current_campaign();

if ( $campaign->has_ended() ) :
    return;
endif;

?>
<div class="campaign-donation">
    <a class="donate-button button" href="#charitable-donation-form" title="<?php echo esc_attr( sprintf( _x( 'Make a donation to %s', 'make a donation to campaign', 'charitable' ), get_the_title( $campaign->ID ) ) ) ?>"><?php _e( 'Donate', 'charitable' ) ?></a>
</div>