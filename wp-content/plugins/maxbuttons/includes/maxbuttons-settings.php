<?php
defined('ABSPATH') or die('No direct access permitted');


if(isset($_POST['alter_charset'])) {
    
    global $maxbuttons_installed_version;
    global $wpdb;
    $table_name = maxUtils::get_buttons_table_name();

    $sql = "ALTER TABLE " . $table_name . " CONVERT TO CHARACTER SET utf8";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $wpdb->query($sql);
    $response = 'CHARSET now utf_8 COLLATE utf8_general_ci';

} else {
    $response = '';
}

if (isset($_POST["reset_cache"])) 
{
	$button = new maxButton();
	$button->reset_cache();

}

if (isset($_POST["remigrate"]))
{
 	$install = MB()->getClass("install"); 
	$install::create_database_table();
	$install::migrate();
}
 
if (isset($_POST["replace"]) && check_admin_referer('mb_bulk_edit', 'bulk_edit')) 
{
	$search = $_POST["search"]; 
	$replace = $_POST["replace"]; 
	$field = $_POST["replace_field"]; 

	$button = new maxButton();
	
	if ($field == '') 
		exit("FATAL"); 
	
	$admin = MB()->getClass('admin'); 
	$buttonsIDS = $admin->getButtons(array('limit' => -1)); 
	 
	$data_found = false; 
	 
	foreach($buttonsIDS as $row)
	{
		$button_id = $row["id"]; 
		$button->set($button_id); 
		$data = $button->get(); 
		foreach($data as $block => $fields) 
		{
			if (isset($fields[$field])) 
			{
				$value = $fields[$field]; 
				$data[$block][$field] = str_replace($search, $replace, $value);
				$button->update($data); 
				//echo "UPDATE $field of $block with ($search) - $replace - ($value) <br>"; 
				
				$data_found = true; 
				continue;
			}
			
			if ($data_found)
			{
				$data_found = false; 
				continue;
			}
		}
 
 
	}
	
} 
 
?>
<?php
$admin = MB()->getClass('admin'); 
$page_title = __("Settings","maxbuttons"); 
$admin->get_header(array("tabs_active" => true, "title" => $page_title) );
?>
 
<div class="mb_tab"> <!-- first tab ---> 
            <div class="title">
		        <span class="dashicons dashicons-list-view"></span> 
				<span class='title'><?php _e('Settings', 'maxbuttons') ?></span>
            </div>
             
       <form method="post" action="options.php">
       
            <div class="option-container settings">
            

                
                <div class="inside">
                    <div class="option-design">

                            <?php settings_fields( 'maxbuttons_settings' ); ?>
                            <label><?php _e('MaxButtons User Level', 'maxbuttons') ?></label>
                            <div class="input">
                                <select name="maxbuttons_user_level">
                                    <?php $maxbuttons_user_level = get_option('maxbuttons_user_level'); ?>
                                    <option value="edit_posts" <?php if($maxbuttons_user_level === 'edit_posts') { echo 'selected="selected"'; } ?>>Contributor</option>
                                    <option value="edit_published_posts" <?php if($maxbuttons_user_level === 'edit_published_posts') { echo 'selected="selected"'; } ?>>Author</option>
                                    <option value="manage_categories" <?php if($maxbuttons_user_level === 'manage_categories') { echo 'selected="selected"'; } ?>>Editor</option>
                                    <option value="manage_options" <?php if($maxbuttons_user_level === 'manage_options') { echo 'selected="selected"'; } ?>>Administrator</option>
                                </select>
                                <br />
                                <?php printf( __('For more details on user roles and permissions, click %s here%s.','maxbuttons'),
                                '<a target="_blank" href="https://codex.wordpress.org/Roles_and_Capabilities">', 
                                "</a>"); 
                                ?>
 
                            </div>
 
                        <div class="clear"></div>
                    </div><!-- option-design --> 
                     <?php 
                     	$noshow = get_option('maxbuttons_noshowtinymce'); 
                     	//$noshow = $max["noshow_tinymce"]; 
                     ?>               
                     <div class="option-design">
                        <label for='maxbuttons_noshowtinymce'><?php _e("Don't show add button in post editor", 'maxbuttons'); ?></label>         
                       	<div class="input checkbox"><input type="checkbox" id='maxbuttons_noshowtinymce' name="maxbuttons_noshowtinymce" value="1" <?php checked($noshow,1); ?> /></div>
                     </div>
                     
                     <?php 
                     	$minify = get_option("maxbuttons_minify", 1); 
                     	$description_hide = get_option('maxbuttons_hidedescription',0); 
                     	
                     ?>
                     <div class="option-design"> 
                     	<label for="maxbuttons_minify"><?php _e("Minify CSS output of buttons","maxbuttons"); ?></label>
                     	<div class="input checkbox">
                     		<input type="checkbox" id='maxbuttons_minify' name="maxbuttons_minify" value="1" <?php checked($minify,1); ?>>
                           <span class='note'><?php _e("You will have to clear your cache after changing this setting","maxbuttons"); ?></span>
                     	</div>                            	
                     </div>
                     
                    <div class="option-design">
                     	<label for='maxbuttons_hidedescription'><?php _e("Hide description field","maxbuttons"); ?></label>
                     	<div class='input checkbox'><input type='checkbox' id='maxbuttons_hidedescription' name='maxbuttons_hidedescription' value='1' <?php checked($description_hide, 1); ?> > 
                     	</div>
 
                     </div>
            
                     
             		<?php do_action("maxbuttons_settings_end"); ?>
                      <?php submit_button(); ?>
                </div>
            </div>
        </form>
        
        <form method="POST"> 
        	<input type="hidden" name="reset_cache" value="true" />
        	<div class="option-container"> 
        		<div class="title"><?php _e("Clear button cache","maxbuttons"); ?></div>
        		<div class="inside">
        			<p><?php _e("Maxbuttons caches the style output allowing for lightning fast display of your buttons. In the event 
        			this cache needs to be flushed and rebuilt you can reset the cache here.","maxbuttons"); ?></p>
        			 <?php submit_button(__("Reset Cache", "maxbuttons") ); ?>
        		</div>
        	</div>
      </form>
      
</div> <!-- /first tab --->      
<div class="mb_tab"><!-- advanced tab --> 
              <div class="title">
		        <span class="dashicons dashicons-list-view"></span> 
				<span class='title'><?php _e('Advanced', 'maxbuttons') ?></span>
            </div>   
                 
        <form method="POST">       
      <div class="option-container">
 
              	<input type="hidden" name="remigrate" value="true" />
      	<div class="title"><?php _e("Retry Database migration","maxbuttons"); ?></div>
      	<div class="inside"><p><?php _e("In case the upgrade functionality failed to move your old buttons from MaxButtons before version 3, you can do so here manually. <strong>Attention</strong>  The new database table (maxbuttonsv3) *must* be empty, and the old database table *must* contain buttons otherwise this will not run. Run this <strong>at your own risk</strong> - it is strongly advised to make a backup before doing so.", "maxbuttons"); ?></p>	
      	 <?php submit_button(__("Remigrate", "maxbuttons") ); ?>
      	</div>
      	        			
      
        </div>
  		</form>
 		
            <div class="option-container">
                <div class="title"><?php _e('UTF8 Table Fix', 'maxbuttons') ?></div>
                <div class="inside">
                    <div class="option-design" >
                        <h3 class="alert"><?php _e('WARNING: We strongly recommend backing up your database before altering the charset of the MaxButtons table in your WordPress database.', 'maxbuttons') ?></h3>

                        <h3><?php _e('The button below should help fix the "foreign character issue" some people experience when using MaxButtons. If you use foreign characters in your buttons and after saving see ????, use this button.', 'maxbuttons') ?></h3>
                        
                        <form action="" method="POST">
                            <input type="submit" name="alter_charset" class="button-primary" value="<?php _e('Change MaxButtons Table To UTF8', 'maxbuttons') ?>" /> <?php echo $response; ?>
                        </form>
            
  		                
                <div class="clear"></div>
            </div>
        </div>
          </div>   
      
      <?php if (isset($_GET["show_replace"])): ?>           
      <form method="POST"> 
      <div class="option-container">
      <?php 
      	$button = MB()->getClass('button'); 
      	$button->set(0); 
      	$data = $button->get(); 
 
      	$allfields = array(); 
      	foreach($data as $block => $fields) 
      	{
      		 $allfields =  array_merge($allfields, array_keys($fields)); 
      		
      	}
			$allfields = array_combine($allfields, $allfields);
      	
      	wp_nonce_field( 'mb_bulk_edit','bulk_edit' );
      	?>
            
        <input type="hidden" name="replace" value="true" />
      	<div class="title"> <?php _e("Bulk edit","maxbuttons"); ?></div>
     
      	<div class="inside"  >
      	<p><strong><?php _e("Using Bulk editor MAY and probably WILL destroy your buttons. In case you wish to prevent this - please BACKUP all your buttons before proceeding!","maxbuttons"); ?></strong></p>	
      	
      	<div class="option"><label><?php _e("Field", "maxbuttons"); ?> </label> <?php echo maxUtils::selectify("replace_field", $allfields, 'url'); ?></div>	
      
      	<div class="option"><label><?php _e("Search","maxbuttons"); ?> </label> <input type="text" name="search" value=""></div>
      	<div class="option"><label><?php _e("Replace","maxbuttons"); ?> </label> <input type="text" name="replace" value=""></div>
      	
    
    	<p style="color: #ff0000"> <?php _e("I understand that this may destroy all my buttons", "maxbuttons"); ?></p>
      	
      	<p><?php _e("", "maxbuttons"); ?></p>	
      	 <?php submit_button(__("Replace", "maxbuttons") ); ?>
      	</div>
      	</div>
  		</form>
  		
		<?php else: ?>  		
  		<a href="<?php echo add_query_arg('show_replace',true); ?>"><?php _e("I need to bulk edit something","maxbuttons"); ?></a>
  		<?php endif; ?> 
 
        </div>
 </div> <!-- advanced tab -->        
        <div class="ad-wrap">
		<?php do_action("mb-display-ads"); ?> 
    </div>

<?php $admin->get_footer(); ?> 
