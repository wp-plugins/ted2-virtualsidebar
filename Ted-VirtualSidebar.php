<?php
/*
Plugin Name: Ted-VirtualSidebar
Plugin URI: http://wp.tedac.fr/plugins/ted2_virtualsidebar1-1-0/
Description: Ted-VirtualSidebar, Most of us, we are use to take widgets in sidebars, so the famous file 'sidebar.php' are never use with default lines. This plugin will allow you to avoid this and choising as many sidebar you need. 
See also: <a href="http://wp.tedac.fr/plugins/">My owns plugins</a>.
Version: 1.1.0
License: A "Slug" license name e.g. GPL2
Author: Didier CADET
Author URI: http://wp.tedac.fr/
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

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

// create custom plugin settings menu
add_action('admin_menu', 'Ted_VSB_create_menu' );

function Ted_VSB_create_menu() {

	if( is_admin() ){
		//create new top-level menu
		add_submenu_page('options-general.php', 'Ted_VSB Plugin Settings', 'Ted Virtual Sidebar', 'administrator',  __FILE__, 'Ted_VSB_settings_page');
		
		//call register settings function
		add_action( 'admin_init', 'Ted_VSB_register' );
	}
}


function Ted_VSB_register() {

	if( is_admin() ){
		//register our settings
		register_setting( 'Ted_VSB-settings-group', 'ted_vsb_number_of_sidebar' );
		
		$nbr = (int)get_option('ted_vsb_number_of_sidebar');
		if( $nbr < 1) $nbr = 1;
		for($i=1;$i<= $nbr;$i++){ 
			register_setting( 'Ted_VSB-settings-group', 'ted_vsb_location_'.$i );
			register_setting( 'Ted_VSB-settings-group', 'ted_vsb_page_'.$i.'_Index'    );
			register_setting( 'Ted_VSB-settings-group', 'ted_vsb_page_'.$i.'_Page'     );
			register_setting( 'Ted_VSB-settings-group', 'ted_vsb_page_'.$i.'_Single'   );
			register_setting( 'Ted_VSB-settings-group', 'ted_vsb_page_'.$i.'_Archive'  );
			register_setting( 'Ted_VSB-settings-group', 'ted_vsb_page_'.$i.'_Category' );
			register_setting( 'Ted_VSB-settings-group', 'ted_vsb_sidebar_name'.$i );
		}
	}
}

function Ted_VSB_Sidebar( $location = "0" ) {
	
	$nbr = (int)get_option('ted_vsb_number_of_sidebar');
	if( $nbr < 1) {
		$nbr = 1;
	}
	
	for($i=1;$i<= $nbr;$i++){ 
		if ( $location == get_option( 'ted_vsb_location_'.$i )){
			if ( ( get_option( 'ted_vsb_page_'.$i.'_Index'   ) == "1" && is_home()    ) ||
			     ( get_option( 'ted_vsb_page_'.$i.'_Page'    ) == "1" && is_page()     ) ||
			     ( get_option( 'ted_vsb_page_'.$i.'_Single'  ) == "1" && is_single()   ) ||
			     ( get_option( 'ted_vsb_page_'.$i.'_Archive' ) == "1" && is_archive()  ) ||
			     ( get_option( 'ted_vsb_page_'.$i.'_Category') == "1" && is_category() ) )
			{
				echo "\n<div id=ted-vsidebar-".$i.">\n<ul>\n";
				dynamic_sidebar(get_option('ted_vsb_sidebar_name'.$i));
				echo "\n</ul>\n</div> <!-- ted-vsidebar-".$i." -->\n";
			}
		}
	}
}


function Ted_VSB_init() {
	if (function_exists("register_sidebar")) {
		$nbr = (int)get_option('ted_vsb_number_of_sidebar');
		if( $nbr < 1) {
			$nbr = 1;
		}
		
		for($i=1;$i<= $nbr;$i++){ 
			$taba['name']= get_option('ted_vsb_sidebar_name'.$i);
			register_sidebar($taba);
		}
	}
}

add_action( 'init', 'Ted_VSB_init' );


function Ted_VSB_settings_page() {

	if( is_admin() ){ ?>
		<div class="wrap">
		<h2>Ted Virtual Sidebar</h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'Ted_VSB-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
				<th scope="row"><?php _e('Number of sidebars :') ?></th>
				<td><input type="text" name="ted_vsb_number_of_sidebar" value="<?php echo get_option('ted_vsb_number_of_sidebar'); ?>" /></td>
				</tr>
				<?php 
				$nbr = (int)get_option('ted_vsb_number_of_sidebar');
				for($i=1;$i<= $nbr;$i++){ ?>
				<tr valign="top">
				<th scope="row"><?php _e('Name of the sidebar No :');echo $i; ?></th>
				<td><input type="text" name="ted_vsb_sidebar_name<?php echo $i; ?>" value="<?php echo get_option('ted_vsb_sidebar_name'.$i); ?>" /></td>
				</tr>
				<tr valign="top">
				<th scope="row"><?php _e('Location of the sidebar No :');echo $i; ?></th>
				<td>		
				<select name="ted_vsb_location_<?php echo $i; ?>">
					<option value="0" <?php if(get_option('ted_vsb_location_'.$i)=="0") echo " selected"; ?> ><?php _e('Before the LOOP'); ?></option>
					<option value="1" <?php if(get_option('ted_vsb_location_'.$i)=="1") echo " selected"; ?> ><?php _e('After the LOOP'); ?></option>
				</select> 
				</td>
				</tr>
				<tr valign="top">
				<th scope="row"><?php _e('On wich templates pages ?'); ?> :</th>
				<td>		
				<input name="ted_vsb_page_<?php echo $i; ?>_Index"    type=checkbox value="1" <?php if(get_option('ted_vsb_page_'.$i."_Index")    =="1") echo " checked"; ?> >Index
				<input name="ted_vsb_page_<?php echo $i; ?>_Page"     type=checkbox value="1" <?php if(get_option('ted_vsb_page_'.$i."_Page")     =="1") echo " checked"; ?> >Page
				<input name="ted_vsb_page_<?php echo $i; ?>_Single"   type=checkbox value="1" <?php if(get_option('ted_vsb_page_'.$i."_Single")   =="1") echo " checked"; ?> >Single
				<input name="ted_vsb_page_<?php echo $i; ?>_Archive"  type=checkbox value="1" <?php if(get_option('ted_vsb_page_'.$i."_Archive")  =="1") echo " checked"; ?> >Archive
				<input name="ted_vsb_page_<?php echo $i; ?>_Category" type=checkbox value="1" <?php if(get_option('ted_vsb_page_'.$i."_Category") =="1") echo " checked"; ?> >Category
				</td>
				</tr>
				<?php } ?>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save All') ?>" />
			</p>
		</form>
		</div>
<?php 
	} 
}
?>