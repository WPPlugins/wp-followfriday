<?php

if (function_exists('wp_enqueue_style')) 
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
}

/////////////////////////////
// Write Manage Menu
function wpfollowfriday_write_managemenu()
{
	echo '<div class="wrap"><h2>Manage your Follow Friday Recommendations</h2>';
	
	/////////////////////////////
	// Rec recommendations
	if ($_GET['followfridayaction'] == "recfollowfriday")
	{
		global $wpdb, $table_prefix;
		$adtable_name = $wpdb->prefix . "wpfollowfriday";
		
		if(count($_POST['selectedpeople'])>0 && $_POST['affListeFollowing']=='1')
		{
			$selected_list = $_POST['wpfollowfriday_list'] ;
			foreach($_POST['selectedpeople'] as $rien => $recline)
			{
				$tmp = explode('|||',$recline);
				$screenName = $tmp[0];
				$image_url  = $tmp[1];
				$wpdb->query("INSERT INTO $adtable_name (id,name,url_avatar,list_id)VALUES (NULL , '$screenName', '$image_url', $selected_list)");
			}
			
			echo '<div id="message" class="updated fade"><p>Selected recommendations added !</p></div>';
		}
		
	}
	if ($_GET['followfridayaction'] == "deletefollowfriday")
	{
		$theid = $_GET['theid'];
		global $wpdb, $table_prefix;
		$adtable_name = $wpdb->prefix . "wpfollowfriday";
		$wpdb->query("DELETE FROM $adtable_name WHERE id = '$theid'");
		echo '<div id="message" class="updated fade"><p>Deleted !</p></div>';
	}
	
	global $wpdb ;
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
	
	/////////////////////////////
	// print Recommendation List
	?>
	<table class="widefat">
	<thead><tr>
	<th scope="col">ID</th>
	<th scope="col">Name</th>
	<th scope="col">Avatar</th>
	<th scope="col"></th>
	<th scope="col">ID</th>
	<th scope="col">Name</th>
	<th scope="col">Avatar</th>
	<th scope="col"></th>
	</tr></thead>
	<tbody>
	<?php
	global $wpdb;
	$table_name = $wpdb->prefix . "wpfollowfriday";

	$wpfollowdb = $wpdb->get_results("SELECT * FROM $table_name ORDER BY list_id, name ASC", OBJECT);
	
	if ($wpfollowdb) 
	{
		foreach ($wpfollowdb as $wpfollowdb_detail)
		{
			if($wpfollowdb_detail->list_id != $flag_list)
			{
				$i = 0 ;
				$flag_list = $wpfollowdb_detail->list_id ;
				print("<tr>");
				print("<td colspan='8'><h3 style='color:#d66b6b;'>".$tab_list_name[$wpfollowdb_detail->list_id]."</h3></td>");
				print("</tr>");	
			}
			
			if($i == 0) print("<tr>");
			
			echo '<td>'.$wpfollowdb_detail->id.'</td>';
			echo '<td><strong>'.$wpfollowdb_detail->name.'</strong></td>';
			echo "<td><img src='".$wpfollowdb_detail->url_avatar."' width='30px' height='30px'/></td>";
			echo '<td><a href="admin.php?page=wp-followfriday/wpfollowfriday.php&theid='.$wpfollowdb_detail->id.'&followfridayaction=deletefollowfriday">Delete</a></td>';
			
			if($i != 0) print("</tr>");
			if($i == 0) $i = 1 ;
			else $i = 0 ;
		}
	} 
	else 
	{ 
		echo '<tr> <td colspan="8">No recommendation</td> </tr>';
	}
	
	echo '</tbody>
	</table>
	<br/>
	<p>Display your recommendations using either the widget (select the displayed list in Follow Friday Settings)
	<br/>or by using the <strong>||-WPFOLLOWFRIDAY-||</strong> tag in your posts or pages (for all lists)
	<br/>or by using the <strong>||-WPFF:{lists_id}:WPFF-||</strong> tag in your posts or pages (for selected lists) example : <strong>||-WPFF:1:WPFF-||</strong> or <strong>||-WPFF:2,5,4:WPFF-||</strong>
	<br/>or by using the <strong>&lt;?php wpfollowfriday_write();  ?&gt;</strong> tag in your template .
	</p>
	';
	
	
	//////////////////////////////////////////
	// Print following list
	if($_REQUEST['affListeFollowing'] == '1')
	{
		if($_REQUEST['twittercursor'] == '') $twittercursor = '-1' ;
		else $twittercursor = $_REQUEST['twittercursor'] ;
		
		$tableFollowing = wpfollowfriday_getFollowing($twittercursor);
		$tableFollowing_OK = array() ;
		
		$previous_curseur = $tableFollowing->previous_cursor ;
		$next_curseur = $tableFollowing->next_cursor ;
	}
	
	
	//////////////////////////////////////
	// write pagination
	if($previous_curseur != '')
	{
		print("<br/><a href='admin.php?page=wp-followfriday/wpfollowfriday.php&affListeFollowing=1&twittercursor=$previous_curseur'>Previous page</a>") ;
	}
	if($next_curseur != '')
	{
		print("<br/><a href='admin.php?page=wp-followfriday/wpfollowfriday.php&affListeFollowing=1&twittercursor=$next_curseur'>Next page</a>") ;
	}
	
	$selected = '' ;
	if($_REQUEST['affListeFollowing'] == '1') $selected = 'CHECKED' ;
	?>
    <form method="post" action="admin.php?page=wp-followfriday/wpfollowfriday.php&followfridayaction=recfollowfriday">
    <input type='hidden' name='twittercursor' value='<?=$twittercursor?>'>
    <p><input type="checkbox" name="affListeFollowing" value="1" onChange="submit()" <?=$selected?>><strong>Display your following list to add recommendations</strong><br>(it could take a while to display due to Twitter API)</p>
	<?php 
	if($_REQUEST['affListeFollowing'] == '1')
	{
		?>
		<p class="submit"><input type="submit" name="Submit" value="Add selected recommendations" /> in 
		<select name="wpfollowfriday_list" type="text" id="wpfollowfriday_list" >
		<?php 
		$table = $wpdb->prefix . "wpfollowfriday_list";
		$qry = "SELECT * FROM $table ORDER BY id ASC" ;
		$wpfollowdb = $wpdb->get_results($qry);
		if ($wpfollowdb) 
		{
			foreach ($wpfollowdb as $wpfollowdb_detail)
			{
				$list_id   = $wpfollowdb_detail->id  ;
				$list_name = $wpfollowdb_detail->name ;
				print("<option value='$list_id'>id $list_id - ".stripslashes($list_name)."</>");
			}
		}
		?>
	</select>
		</p>
		<table class="widefat">
		<thead><tr>
		<th scope="col"></th>
		<th scope="col">Name</th>
		<th scope="col">Avatar</th>
		<th scope="col"></th>
		<th scope="col">Name</th>
		<th scope="col">Avatar</th>
		</tr></thead>
		<tbody>
		<?php 
		$i=0;
		if(count($tableFollowing->users->user)>0)
		{
			foreach($tableFollowing->users->user as $following)
			{
				if($i == 0) print("<tr>");
				
				$screen_name = $following->screen_name ;
				$image_url   = $following->profile_image_url ;
					
				print("<td><input type='checkbox' name='selectedpeople[]' id='selectedpeople[]' value='$screen_name|||$image_url'></td>
				       <td><a href='http://www.twitter.com/$screen_name' target='_blank'>$screen_name</a></td>
				       <td><img src='$image_url' width='30px' height='30px'/></td>");
			
				if($i != 0) print("</tr>");
				if($i == 0) $i = 1 ;
				else $i = 0 ;
			}
		}
		else
		{
			print("<td colspan='6' style='text-align:center;'>Connection Fail due to Twitter server... please try again later</td>");
		}
		echo '</tbody>
		</table>';
	}
	
	//////////////////////////////////////////
	// Print Footer
	wpfollowfriday_admin_page_footer();
	echo '</div>';
}


/////////////////////////////
// Write Setting Menu
function wpfollowfriday_write_settingsmenu() 
{
	global $wpdb;
	///////////////////////
	// writing options
	if ($_POST['issubmitted']=='yes') 
	{
		$wpfollowfriday_login = $wpdb->escape($_POST['wpfollowfriday_login']);
		update_option("wpfollowfriday_login", $wpfollowfriday_login);
		
		$wpfollowfriday_pattern = $_POST['wpfollowfriday_pattern'];
		update_option("wpfollowfriday_pattern", $wpfollowfriday_pattern);
		
		$wpfollowfriday_before = $_POST['wpfollowfriday_before'];
		update_option("wpfollowfriday_before", $wpfollowfriday_before);
		
		$wpfollowfriday_after = $_POST['wpfollowfriday_after'];
		update_option("wpfollowfriday_after", $wpfollowfriday_after);
		
		$wpfollowfriday_before_list = $_POST['wpfollowfriday_before_list'];
		update_option("wpfollowfriday_before_list", $wpfollowfriday_before_list);
		
		echo '<div id="message" class="updated fade"><p>Settings updated.</p></div>';
	}
	
	///////////////////////
	// add list
	if ($_POST['issubmitted2']=='yes') 
	{
		$table = $wpdb->prefix . "wpfollowfriday_list";
		$wpfollowfriday_add_list = $wpdb->escape($_POST['wpfollowfriday_add_list']);
		$tmp = $wpdb->get_results("SELECT MAX(ordre) as actual FROM $table") ;
		$max = $tmp[0]->actual + 1 ; 	
		$wpdb->query("INSERT INTO $table (id,name,ordre)VALUES (NULL , '$wpfollowfriday_add_list', '$max')");		
	}
	
	///////////////////////
	// delete list
	if ($_POST['issubmitted3']=='yes') 
	{
		$table = $wpdb->prefix . "wpfollowfriday_list";
		$wpfollowfriday_add_list = $wpdb->escape($_POST['wpfollowfriday_del_list']); 	
		$wpdb->query("DELETE FROM $table WHERE id = $wpfollowfriday_add_list");		
	}
	
	
	///////////////////////
	// order list
	if ($_POST['issubmitted4']=='yes') 
	{
		$table = $wpdb->prefix . "wpfollowfriday_list";
		$wpfollowfriday_tab_order = $_POST['wpfollowfriday_list_order']; 	
		
		if(count($wpfollowfriday_tab_order)>0)
		{
			foreach($wpfollowfriday_tab_order as $id => $order)
			{
				$wpdb->query("UPDATE $table SET ordre = $order WHERE id = $id");
			}
		}
	}
	
	///////////////////////
	// change widget list
	if ($_POST['issubmitted5']=='yes') 
	{
		$wpfollowfriday_widgetlist = $wpdb->escape($_POST['wpfollowfriday_widgetlist']); 	
		update_option( "wpfollowfriday_widgetlist", $wpfollowfriday_widgetlist );	
	}
	
	/////////////////////////
	//Retrieve settings
	$setting_login   = get_option("wpfollowfriday_login");
	$setting_before  = get_option("wpfollowfriday_before");
	$setting_after   = get_option("wpfollowfriday_after");
	$setting_pattern = get_option("wpfollowfriday_pattern");
	$setting_before_list = get_option("wpfollowfriday_before_list");
	$setting_widgetlist = get_option("wpfollowfriday_widgetlist");
	
	?><div class="wrap">
	<h2>Follow Friday Settings</h2>
	<form method="post" action="admin.php?page=wp-followfriday">
	<table class="form-table">
	
	<tr valign="top">
	<th scope="row">Your ScreenName</th>
	<td><input name="wpfollowfriday_login" type="text" id="wpfollowfriday_login" value="<?php echo $setting_login; ?>" size="50" /></td>
	</tr>
	
	<tr valign="top">
	<th scope="row">Html code pattern
	<br> use the <strong>%screenName%</strong> and <strong>%urlAvatar%</strong> tags
	</th>
	<td><textarea name="wpfollowfriday_pattern" type="text" id="wpfollowfriday_pattern" rows=5 COLS=70><?php echo stripslashes($setting_pattern); ?></textarea></td>
	</tr>
	
	<tr valign="top">
	<th scope="row">Html code before</th>
	<td><textarea name="wpfollowfriday_before" type="text" id="wpfollowfriday_before" rows=5 COLS=70><?php echo stripslashes($setting_before); ?></textarea></td>
	</tr>
	
	<tr valign="top">
	<th scope="row">Html code after</th>
	<td><textarea name="wpfollowfriday_after" type="text" id="wpfollowfriday_after" rows=5 COLS=70><?php echo stripslashes($setting_after); ?></textarea></td>
	</tr>
	
	<tr valign="top">
	<th scope="row">Code before Each List
	<br> use the <strong>%listName%</strong> tag
	</th>
	<td><textarea name="wpfollowfriday_before_list" type="text" id="wpfollowfriday_before_list" rows=5 COLS=70><?php echo stripslashes($setting_before_list); ?></textarea></td>
	</tr>
	
	</table>
	<input name="issubmitted" type="hidden" value="yes" />
	<p class="submit"><input type="submit" name="Submit" value="Save Changes" /></p>
	</form>
	<br/><br/>
	
	<h2>Follow Friday Lists</h2>
	<form method="post" action="admin.php?page=wp-followfriday">
	<table class="form-table">
	<tr valign="top">
	<td><input name="issubmitted2" type="hidden" value="yes" /><p class="submit"><input type="submit" name="Submit" value="Add List" />
	<input type="text" name="wpfollowfriday_add_list" type="text" id="wpfollowfriday_add_list"  size="50" value="" ></p></td>
	</tr>
	</table>
	</form>
	
	<form method="post" action="admin.php?page=wp-followfriday">
	<table class="form-table">
	<tr valign="top">
	<td><input name="issubmitted3" type="hidden" value="yes" />
	<p class="submit">
	<input type="submit" name="Submit" value="Delete List" />
	<select name="wpfollowfriday_del_list" type="text" id="wpfollowfriday_del_list" >
		<option value="0">-- Select a List --</option>
		<?php 
		$table = $wpdb->prefix . "wpfollowfriday_list";
		$qry = "SELECT * FROM $table WHERE id != '1' ORDER BY id ASC" ;
		$wpfollowdb = $wpdb->get_results($qry);
		if ($wpfollowdb) 
		{
			foreach ($wpfollowdb as $wpfollowdb_detail)
			{
				$list_id   = $wpfollowdb_detail->id  ;
				$list_name = $wpfollowdb_detail->name ;
 				print("<option value='$list_id'>id $list_id - ".stripslashes($list_name)."</>");
			}
		}
		?>
	</select>
	</p></td>
	</tr>
	</table>
	</form>
	
	
	<form method="post" action="admin.php?page=wp-followfriday">
	<table class="form-table">
	<tr valign="top">
	<td><input name="issubmitted5" type="hidden" value="yes" />
	<p class="submit">
	<input type="submit" name="Submit" value="Change Widget List" />
	<select name="wpfollowfriday_widgetlist" type="text" id="wpfollowfriday_widgetlist" >
		<?php 
		$table = $wpdb->prefix . "wpfollowfriday_list";
		$qry = "SELECT * FROM $table ORDER BY id ASC" ;
		$wpfollowdb = $wpdb->get_results($qry);
		if ($wpfollowdb) 
		{
			foreach ($wpfollowdb as $wpfollowdb_detail)
			{
				$list_id   = $wpfollowdb_detail->id  ;
				$list_name = $wpfollowdb_detail->name ;
				
				if($list_id == $setting_widgetlist)$selected = "SELECTED" ;
				else $selected = ""; 
				
				print("<option value='$list_id' $selected>id $list_id - ".stripslashes($list_name)."</>");
			}
		}
		?>
	</select>
	</p></td>
	</tr>
	</table>
	</form>
	
	
	<form method="post" action="admin.php?page=wp-followfriday">
	<table class="form-table">
	<tr valign="top">
	<th scope="row"><strong>List Order</strong>
	<p class="submit">
	<input name="issubmitted4" type="hidden" value="yes" />
	<input type="submit" name="Submit" value="Change Lists order" />
	</p>
	</th>
	<td>
		<?php 
		$table = $wpdb->prefix . "wpfollowfriday_list";
		$qry = "SELECT * FROM $table ORDER BY ordre ASC" ;
		$wpfollowdb = $wpdb->get_results($qry);
		if ($wpfollowdb) 
		{
			foreach ($wpfollowdb as $wpfollowdb_detail)
			{
				$list_id    = $wpfollowdb_detail->id  ;
				$list_name  = $wpfollowdb_detail->name ;
				$list_order = $wpfollowdb_detail->ordre ;
				print("<input type='text' name='wpfollowfriday_list_order[$list_id]' id='name='wpfollowfriday_list_order[$list_id]' value='$list_order' size='5'> id <strong>$list_id</strong> - $list_name<br/>");
			}
		}
		?>
   </td>
	</tr>
	</table>
	</form>
	<br/><br/>
	<p>Display your recommendations using either the widget (select the displayed list in Follow Friday Settings)
	<br/>or by using the <strong>||-WPFOLLOWFRIDAY-||</strong> tag in your posts or pages (for all lists)
	<br/>or by using the <strong>||-WPFF:{lists_id}:WPFF-||</strong> tag in your posts or pages (for selected lists) example : <strong>||-WPFF:1:WPFF-||</strong> or <strong>||-WPFF:2,5,4:WPFF-||</strong>
	<br/>or by using the <strong>&lt;?php wpfollowfriday_write();  ?&gt;</strong> tag in your template .
	</p>
	<?php wpfollowfriday_admin_page_footer(); ?>
	</div><?php
}

///////////////////////////////////////////////
// FOOTER
function wpfollowfriday_admin_page_footer() 
{
	echo '<div style="margin-top:45px; font-size:0.87em;">';
	echo '<div style="float:left;">Written by <a href="http://www.fabien-bouchard.com" target="_blank">Fabien Bouchard</a> - <a href="http://www.fabien-bouchard.com/wp-followfriday/wp-followfriday-for-wordpresstwitter/" target="_blank">Documentation</a></div>';
	echo '</div>';
}

?>