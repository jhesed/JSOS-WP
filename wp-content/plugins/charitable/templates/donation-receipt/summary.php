<?php 
/**
 * Displays the donation summary.
 *
 * Override this template by copying it to yourtheme/charitable/donation-receipt/summary.php
 *
 * @author  Studio 164a
 * @package Charitable/Templates/Donation Receipt
 * @since   1.0.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * @var     Charitable_Donation
 */
$donation = $view_args[ 'donation' ];

?>
<ul class="donation-summary">
    <li class="donation-id">
        <?php _e( 'Donation Number:', 'charitable' ) ?>
        <span class="donation-summary-value"><?php echo $donation->get_number() ?></span>
    </li>
    <li class="donation-date">
        <?php _e( 'Date:', 'charitable' ) ?>
        <span class="donation-summary-value"><?php echo $donation->get_date() ?></span>
    </li>
    <li class="donation-total"> 
        <?php _e( 'Total:', 'charitable' ) ?>
        <span class="donation-summary-value"><?php echo charitable_format_money( $donation->get_total_donation_amount() ) ?></span>
    </li>
    <li class="donation-method">
        <?php _e( 'Payment Method:', 'charitable' ) ?>
        <span class="donation-summary-value"><?php echo $donation->get_gateway_label() ?></span>
    </li>
</ul>