<?php
	$sql  = "Delete from access_token where user_pk = ".$user_id." and device_pk = ".$device_id    ;
	$db->query($sql);
	require_once('../dist/lib/functions.php');  	
	$sk_token = GenerateAccessToken();
	$insert_data = array();
	$insert_data['access_token'] = $sk_token;
	$insert_data['device_pk'] = $device_id;
	$insert_data['user_pk'] = $user_id;
	$insert_data['creation_date'] = CURRENT_DB_DATE;
	$insert_data['update_date'] = CURRENT_DB_DATE; 
	$id = $db->insert("access_token", $insert_data); 

?>