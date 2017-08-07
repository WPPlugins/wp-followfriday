<?php
/*
Plugin Name: WP-Follow-Friday
Plugin URI: http://www.fabien-bouchard.com/wp-followfriday/wp-followfriday-for-wordpresstwitter/
Description: Manage your Twitter recommendations - aka Follow Friday - with this easy to use plugin. Select recommendations into your followings and display them in a widget, in a post, in a page or in your template.
Author: Fabien Bouchard @WeLoveUfunk
Author URI: http://www.fabien-bouchard.com
Version: 0.3
*/

define("MANAGEMENT_PERMISSION", "edit_themes"); 

/////////////////////////////////
//Installer
function wpfollowfriday_install () {
	require_once(dirname(__FILE__).'/installer.php');
}
register_activation_hook(__FILE__,'wpfollowfriday_install');

/////////////////////////////////
//Create Widget
function wpfollowfriday_create_widget() {
	register_sidebar_widget("FollowFriday", 'wpfollowfriday_write_widget');
}

function wpfollowfriday_write_widget($args) {
	extract($args);
	echo $before_widget;
	echo "\n".$before_title."Follow Friday !".$after_title;
	wpfollowfriday_write();
	echo $after_widget;
}

/////////////////////////////////
//Add the Admin Menus
if (is_admin()) {
	function wpfollowfriday_add_admin_menu() {
		add_menu_page("Follow Friday", "Follow Friday", MANAGEMENT_PERMISSION, __FILE__, "wpfollowfriday_write_managemenu");
		
		add_submenu_page(__FILE__, "Manage recommendations", "Manage recommendations", MANAGEMENT_PERMISSION, __FILE__, "wpfollowfriday_write_managemenu");
		add_submenu_page(__FILE__, "Follow Friday Settings", "Follow Friday Settings", MANAGEMENT_PERMISSION, 'wp-followfriday', "wpfollowfriday_write_settingsmenu");
	}
	//Include menus
	require_once(dirname(__FILE__).'/adminmenus.php');
}

///////////////////////////////////////////////
// Write recommendation in post
function wpfollowfriday_writeInPost($content)
{
	global $wpdb;
	
	$table = $wpdb->prefix . "wpfollowfriday_list";
	$qry = "SELECT * FROM $table ORDER BY id ASC" ;
	$wpfollowdb = $wpdb->get_results($qry);
	$tab_list_name = array();
	if ($wpfollowdb) 
	{
		foreach ($wpfollowdb as $wpfollowdb_detail)
		{
			$list_id   = $wpfollowdb_detail->id  ;
			$list_name = $wpfollowdb_detail->name ;
			$tab_list_name[$list_id] = $list_name ;
		}
	}
	
	
	$chaine = '' ;
	$setting_login   = get_option("wpfollowfriday_login");	
	$setting_before  = get_option("wpfollowfriday_before");
	$setting_after   = get_option("wpfollowfriday_after");
	$setting_pattern = get_option("wpfollowfriday_pattern");
	$setting_list    = get_option("wpfollowfriday_before_list");
	
	$adtable_name = $wpdb->prefix . "wpfollowfriday";

	///////////////////////////////////////////////////////////////
	// DISPLAY ALL 
	if ( ($a=strpos($content,'||-WPFOLLOWFRIDAY-||'))!==false ) 
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wpfollowfriday";
		$wpfollowdb = $wpdb->get_results("SELECT * FROM $table_name ORDER BY list_id, name ASC", OBJECT);	
	
		if ($wpfollowdb) 
		{
			$chaine .= '<div style="text-align:center;">' ;
			$chaine .= stripslashes($setting_before) ;
			foreach ($wpfollowdb as $wpfollowdb_detail)
			{
				if($wpfollowdb_detail->list_id != $flag_list)
				{
					$i = 0 ;
					$flag_list = $wpfollowdb_detail->list_id ;
					$name_list = $tab_list_name[$wpfollowdb_detail->list_id] ;
					$chaine .= str_replace("%listName%", $name_list, $setting_list) ;
				}
				
				$line = '' ;
				$line = str_replace("%screenName%", $wpfollowdb_detail->name, stripslashes($setting_pattern)) ;
				$line = str_replace("%urlAvatar%", $wpfollowdb_detail->url_avatar, $line) ;
				$chaine .= $line ;
			}
			$chaine .= stripslashes($setting_after) ;
			$chaine .= '</div>' ;
		}
	
		$content = str_replace("||-WPFOLLOWFRIDAY-||", $chaine, $content) ;
	}
	
	///////////////////////////////////////////////////////////////
	// DISPLAY ALL 
	$TMP = explode("||-WPFF:",$content);
	if(count($TMP)>1) 
	{
		foreach($TMP as $rien => $TMP2)
		{
			if($rien == 0) continue ;
			$chaine = "" ;
			$TMP3 = explode(":WPFF-|",$TMP2) ;
			$listFF = $TMP3[0];
			
			global $wpdb;
			$table_name = $wpdb->prefix . "wpfollowfriday";
			$wpfollowdb = $wpdb->get_results("SELECT * FROM $table_name WHERE list_id IN ($listFF) ORDER BY list_id, name ASC", OBJECT);	
		
			if ($wpfollowdb) 
			{
				$chaine .= '<div style="text-align:center;">' ;
				$chaine .= stripslashes($setting_before) ;
				foreach ($wpfollowdb as $wpfollowdb_detail)
				{
					if($wpfollowdb_detail->list_id != $flag_list)
					{
						$i = 0 ;
						$flag_list = $wpfollowdb_detail->list_id ;
						$name_list = $tab_list_name[$wpfollowdb_detail->list_id] ;
						$chaine .= str_replace("%listName%", $name_list, $setting_list) ;
					}
					
					$line = '' ;
					$line = str_replace("%screenName%", $wpfollowdb_detail->name, stripslashes($setting_pattern)) ;
					$line = str_replace("%urlAvatar%", $wpfollowdb_detail->url_avatar, $line) ;
					$chaine .= $line ;
				}
				$chaine .= stripslashes($setting_after) ;
				$chaine .= '</div>' ;
			}
		
			$content = str_replace("||-WPFF:$listFF:WPFF-||", $chaine, $content) ;
		}
	}
	
	return $content;
}
add_filter( "the_content", "wpfollowfriday_writeInPost" );


///////////////////////////////////////////////
//Write the recommendations
function wpfollowfriday_write() 
{

	global $wpdb;
	
	$table = $wpdb->prefix . "wpfollowfriday_list";
	$qry = "SELECT * FROM $table ORDER BY id ASC" ;
	$wpfollowdb = $wpdb->get_results($qry);
	$tab_list_name = array();
	if ($wpfollowdb) 
	{
		foreach ($wpfollowdb as $wpfollowdb_detail)
		{
			$list_id   = $wpfollowdb_detail->id  ;
			$list_name = $wpfollowdb_detail->name ;
			$tab_list_name[$list_id] = $list_name ;
		}
	}
	
	$setting_login      = get_option("wpfollowfriday_login");
	$setting_before     = get_option("wpfollowfriday_before");
	$setting_after      = get_option("wpfollowfriday_after");
	$setting_pattern    = get_option("wpfollowfriday_pattern");
	$setting_list       = get_option("wpfollowfriday_before_list");
	$setting_widgetlist = get_option("wpfollowfriday_widgetlist");

	$table_name = $wpdb->prefix . "wpfollowfriday";
	$wpfollowdb = $wpdb->get_results("SELECT * FROM $table_name WHERE list_id = $setting_widgetlist ORDER BY list_id, name ASC", OBJECT);
	
	if ($wpfollowdb) 
	{
		print('<div style="text-align:center;">');
		print(stripslashes($setting_before)) ;
		foreach ($wpfollowdb as $wpfollowdb_detail)
		{
			if($wpfollowdb_detail->list_id != $flag_list)
			{
				$i = 0 ;
				$flag_list = $wpfollowdb_detail->list_id ;
				$name_list = $tab_list_name[$wpfollowdb_detail->list_id] ;
				print(str_replace("%listName%", $name_list, $setting_list)) ;
			}
			
			$line = '' ;
			$line = str_replace("%screenName%", $wpfollowdb_detail->name, stripslashes($setting_pattern)) ;
			$line = str_replace("%urlAvatar%", $wpfollowdb_detail->url_avatar, $line) ;
			print($line) ;
			//print("<a href='http://twitter.com/".$wpfollowdb_detail->name."' target='_blank'><img src='".$wpfollowdb_detail->url_avatar."' width='40px' height='40px' style='margin:3px'/>");
		}
		print(stripslashes($setting_after)) ; 

		print('</div>');
	} 
    
}

////////////////////////////////////////////////////
//Return path to plugin directory (url/path)
function wpfollowfriday_get_plugin_dir($type)
{
	if ( !defined('WP_CONTENT_URL') )
		define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ($type=='path') { return WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)); }
	else { return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)); }
}



/////////////////////////////////////////////////////
// TWITTER FONCTION
function wpfollowfriday_getFollowing($cursor)
{
	if($cursor == '') $cursor = '-1' ;
	
	$setting_login = get_option("wpfollowfriday_login");
	if($setting_login != '')
	{
		$request = "http://twitter.com/statuses/friends/$setting_login.xml?cursor=$cursor";
		return wpfollowfriday_process($request); 
	} 
	return 0; 
}

function wpfollowfriday_process($url)
{  
        $ch = curl_init($url);
        $responseInfo=array();
        $headers = array('X-Twitter-Client: ',
                         'X-Twitter-Client-Version: ',
                         'X-Twitter-Client-URL: ');

        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        
        $responseInfo=curl_getinfo($ch);
        curl_close($ch);
        
        if(intval($responseInfo['http_code'])==200){
        	//print("-200-");
            if(class_exists('SimpleXMLElement')){
                $xml = new SimpleXMLElement($response);
                return $xml;
            }else{
                return $response;    
            }
        }else{
        	//print("-".$responseInfo['http_code']."-");
            return false;
        }
} 












/////////////////////////////////
//Hooks
add_action("plugins_loaded", "wpfollowfriday_create_widget"); //Create the Widget
if (is_admin()) { add_action('admin_menu', 'wpfollowfriday_add_admin_menu'); } //Admin pages




/*
Copyright 2009 Fabien Bouchard

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

?>