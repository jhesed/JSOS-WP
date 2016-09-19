<?php
/*
Plugin Name: Embed image links - by Wonder
Plugin URI: http://WeAreWonder.dk/wp-plugins/embed-image-links/
Description: Automatically replace any image link with the source image in a post or a page.
Version: 1.3.2
Author: Wonder
Author URI: http://WeAreWonder.dk
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5B6TDUTW2JVX8
License: GPL2
	
	Copyright 2014 Wonder  (email : tobias@WeAreWonder.dk)
	
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
	
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
	
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there! I\'m just a plugin, not much I can do when called directly.';
	exit;
}

// Allow redirection, even if my theme starts to send output to the browser
add_action('init', 'embed_image_links_do_output_buffer');
function embed_image_links_do_output_buffer() {
	ob_start();
}

add_action('admin_menu', 'embed_image_links_admin_menu');

function embed_image_links_admin_menu() {
	add_submenu_page('options-general.php', _('Embed image links settings'), _('Embed Image Links'), 'manage_options', 'embed-image-links-settings', 'embed_image_links_admin_page');
}

// Replaces image text links with the actual images.
function replace_image_text_links_content_filter($content) {
	
	$content  = preg_replace('#((?!"|\')http(s?)://([^\s]*)\.(jpg|gif|png|bmp|jpeg)(?!"|\'))#', '<img class="embedded-image-link" src="$1">', $content);
	
	wp_register_script('embedimagelinksjs', plugin_dir_url( __FILE__ ) . 'js.php?max-width-percentage=' . getMaxWidthPercentageSetting(), false, '', true);
	wp_enqueue_script('embedimagelinksjs');
	
	return $content;
	
}

add_filter( 'the_content', 'replace_image_text_links_content_filter', 5 );

function embed_image_links_admin_page() {
	
	if ( isset($_POST['max-width-percentage']) ) {
		$iMaxWidthPercentage = $_POST['max-width-percentage'];
		$iMaxWidthPercentage = abs( intval($iMaxWidthPercentage) );
		if ( !$iMaxWidthPercentage ) {
			$iMaxWidthPercentage = 100;
		}
		update_option('embed_image_links_max_width_percentage', $iMaxWidthPercentage);
		
		wp_redirect('?page=' . $_GET['page'] . '&msg=saved');
		exit;
	}
	
	$iMaxWidthPercentage = getMaxWidthPercentageSetting();
	?>
	
	<style type="text/css">
	.donate-box {
		float: right;
		width: 200px;
		padding: 25px;
		margin:  25px;
		border: 2px solid #bbb;
		background-color: #e7e7e7;
	}
	
	.donate-box h2 {
		margin-top: 0;
	}
	
	.donate-box hr {
		height: 1px;
		border-width: 2px 0 0 0;
		border-style: solid;
		border-color: #bbb;
	}
	
	h2 img {
		float: left;
		margin-right: 5px;
	}
	h2 small {
		display: block;
		margin-left: 38px;
		font-size: 12px;
	}
	</style>
	
	<div class="donate-box">
		<h2>Donations</h2>
		
		<p>
			If you like our plugin, please consider donating a few dollars, so we can continue to provide support and make awesome stuff for you guys!
		</p>
		
		<p>
			<a title="Donate" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AHE8UEBKSYJCA">Donate &raquo;</a>
		</p>
		
		<hr>
		
		<h2>See also...</h2>
		
		<p>
			<a title="Queue Posts plugin" href="http://wordpress.org/plugins/queue-posts/">Queue Posts plugin</a><br>
			<a title="Simple MailChimp plugin" href="http://wordpress.org/plugins/simple-mailchimp/">Simple MailChimp plugin</a>
		</p>
	</div>
	
	<div class="wrap">
		
		<h2>
			<img alt="Settings" src="<?php echo plugin_dir_url( __FILE__ ); ?>img/settings-icon.png" width="32" height="32" style="margin-bottom: -7px;">
			Settings for embedded image links
			<small>by <a href="http://wearewonder.dk" target="_blank">Wonder</a></small>
		</h2>
		
		<form method="post" action="">
			
			<?php if ( $_GET['msg'] == 'saved' ) : ?>
				<div id="message" class="updated">
					<?php echo _('Your settings have been saved.'); ?>
				</div>
			<?php endif; ?>
			
			<p style="margin: 15px 0;">
				<label for="max-width-percentage">
					<?php echo _('If images are too large for the content area, resize to'); ?>:
				</label>
				
				<br><br>
				
				<input id="max-width-percentage" name="max-width-percentage" value="<?php echo $iMaxWidthPercentage; ?>" maxlength="3" style="width: 50px; text-align: center;" placeholder="100"> % <?php echo _('of content area'); ?>
			</p>
			
			<p>
				<input type="submit" value="<?php echo _('Save'); ?>">
			</p>
			
		</form>
		
	</div>
	
	<?php
	
}

function getMaxWidthPercentageSetting() {
	$iMaxWidthPercentage = intval( get_option('embed_image_links_max_width_percentage') );
	if ( !$iMaxWidthPercentage ) {
		$iMaxWidthPercentage = 100;
	}
	return $iMaxWidthPercentage;
}

function embed_image_links_plugin_action_links($links, $file) {
	static $this_plugin;
	
	if (!$this_plugin) {
		$this_plugin = plugin_basename(__FILE__);
	}
	
	// check to make sure we are on the correct plugin
	if ($file == $this_plugin) {
		$settings_link = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AHE8UEBKSYJCA">' . __('Donate') . '</a>';
		array_unshift($links, $settings_link);
		
		$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=embed-image-links-settings.php">' . __('Settings') . '</a>';
		array_unshift($links, $settings_link);
	}
	
	return $links;
}

add_filter('plugin_action_links', 'embed_image_links_plugin_action_links', 10, 2);

?>