<?php

// Making sure your child theme has an independent version and can bust caches: http://wordpress.stackexchange.com/a/182023/30783
// Filter get_stylesheet_uri() to return the parent theme's stylesheet
add_filter('stylesheet_uri', 'use_parent_theme_stylesheet');
// Enqueue this theme's scripts and styles (after parent theme)
add_action('wp_enqueue_scripts', 'my_theme_styles', 20);
// add_action('wp_enqueue_scripts', 'jquery', 20);
remove_filter('the_content', 'wpautop');
function use_parent_theme_stylesheet()
{
    // Use the parent theme's stylesheet
    return get_template_directory_uri() . '/style.css';
}

function my_theme_styles()
{
    $themeVersion = wp_get_theme()->get('Version');
    // Enqueue our style.css with our own version
    wp_enqueue_style('child-theme-style', get_stylesheet_directory_uri() . '/style.css',
        array(), $themeVersion);
}

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}
/* Disable WordPress Admin Bar for all users but admins. */
  //show_admin_bar(false);
// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

// END ENQUEUE PARENT ACTION

//  for changed port
// remove_filter('template_redirect','redirect_canonical');