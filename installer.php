<?php
//***** Installer *****
require_once(ABSPATH . 'wp-admin/upgrade.php');

//***Installer variables***
global $wpdb;

$wpfollowfriday_db_version = "0.3";
$installed_ver = get_option( "wpfollowfriday_db_version" );

$sql = "" ;

$table_name = $wpdb->prefix . "wpfollowfriday";
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
{
	$sql = "CREATE TABLE " . $table_name . " (
		  id int(12) NOT NULL auto_increment,
		  name text NOT NULL,
	      url_avatar text NOT NULL,
	      list_id int(12) NOT NULL DEFAULT '1',
		  PRIMARY KEY  (id)
		);";
	
	add_option("wpfollowfriday_login", "");
	add_option("wpfollowfriday_before", "");
	add_option("wpfollowfriday_after", "");
	add_option("wpfollowfriday_pattern", "<a href='http://twitter.com/%screenName%' target='_blank'><img src='%urlAvatar%' width='40px' height='40px' style='margin:3px'/></a>");
	add_option("wpfollowfriday_db_version", $wpfollowfriday_db_version);
}

$table_name = $wpdb->prefix . "wpfollowfriday_list";
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) 
{
		$sql .= "CREATE TABLE " . $table_name . " (
			  id int(12) NOT NULL auto_increment,
			  name text NOT NULL,
		      ordre int(12) NOT NULL DEFAULT '1',
			  PRIMARY KEY  (id) );";
		
		$sql .= "INSERT INTO ". $table_name ." (id, name, ordre)
                VALUES (NULL , 'My Twitter #followFriday', '1');";
		
	 add_option("wpfollowfriday_widgetlist", "1");
	 add_option("wpfollowfriday_before_list", "<div style='text-align:left;'><h3>%listName%</h3></div>");

}

dbDelta($sql);

//***Upgrader***
if( $installed_ver != $wpfollowfriday_db_version ) {
$table_name = $wpdb->prefix . "wpfollowfriday";
$wpdb->query("ALTER TABLE " . $table_name . " ADD list_id INT( 12 ) NOT NULL DEFAULT '1' ;");

update_option( "wpfollowfriday_db_version", $wpfollowfriday_db_version );
}


//***** End Installer *****
?>