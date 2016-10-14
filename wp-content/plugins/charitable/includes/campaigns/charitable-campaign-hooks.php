<?php
/**
 * Charitable Campaign Hooks.
 *
 * @package     Charitable/Functions/Campaigns
 * @version     1.3.0
 * @author      Eric Daams
 * @copyright   Copyright (c) 2016, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Sanitize campaign meta.
 *
 * @see     Charitable_Campaign::sanitize_meta()
 */
add_filter( 'charitable_sanitize_campaign_meta', array( 'Charitable_Campaign', 'sanitize_meta' ), 10, 3 );

/**
 * Sanitize individual campaign meta fields.
 *
 * @see     Charitable_Campaign::sanitize_campaign_goal()
 * @see     Charitable_Campaign::sanitize_campaign_end_date()
 * @see     Charitable_Campaign::sanitize_campaign_suggested_donations()
 * @see     Charitable_Campaign::sanitize_custom_donations()
 * @see     Charitable_Campaign::sanitize_campaign_description()
 */
add_filter( 'charitable_sanitize_campaign_meta_campaign_goal', array( 'Charitable_Campaign', 'sanitize_campaign_goal' ) );
add_filter( 'charitable_sanitize_campaign_meta_campaign_end_date', array( 'Charitable_Campaign', 'sanitize_campaign_end_date' ) );
add_filter( 'charitable_sanitize_campaign_meta_campaign_suggested_donations', array( 'Charitable_Campaign', 'sanitize_campaign_suggested_donations' ) );
add_filter( 'charitable_sanitize_campaign_meta_campaign_allow_custom_donations', array( 'Charitable_Campaign', 'sanitize_custom_donations' ), 10, 2 );
add_filter( 'charitable_sanitize_campaign_meta_campaign_description', array( 'Charitable_Campaign', 'sanitize_campaign_description' ) );
