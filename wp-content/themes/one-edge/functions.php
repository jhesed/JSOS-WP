<?php

add_action( 'wp_enqueue_scripts', 'one_edge_enqueue_styles' );
function one_edge_enqueue_styles() {
	wp_enqueue_style( 'one_edge-font', '//fonts.googleapis.com/css?family=Cabin:400,600|Open+Sans:400,300,600|Ubuntu:400,300,500,700');
	wp_enqueue_style( 'one_edge-style',  get_template_directory_uri() . '/style.css', array('llorix-one-lite-bootstrap-style'),'1.0.0');
	wp_enqueue_script( 'one_edge-custom-js', get_stylesheet_directory_uri() . '/js/custom-js.js', array('jquery'), '1.0.0', true );
}

function one_edge_setup() {

	/* Set the image size by cropping the image */
	add_image_size( 'one-edge-post-thumbnail-big', 340, 340, true );
	add_image_size( 'one_edge-post-thumbnail-mobile', 233, 233, true );

}
add_action( 'after_setup_theme', 'one_edge_setup', 11 );

