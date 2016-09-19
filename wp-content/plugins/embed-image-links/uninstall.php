<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit ();
}

delete_option('embed_image_links_max_width_percentage');
delete_option('embed_image_links_alignment');

?>