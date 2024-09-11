<?php
	 
$url = "login.php";	

if(isset($_REQUEST['sk_token'])  && isset($_REQUEST['id'])        )    
{ 
	require_once('../dist/lib/config.php');  
	require_once('../dist/lib/db_class.php');  
	
	$_SESSION['login_eviot_company'] = 1; //COMPANY
	if(isset($_REQUEST['cid'])      )    
	{ 
		$_SESSION['login_eviot_company'] = $db->escape($_REQUEST['cid']); 
	}
	if(isset($_REQUEST['IS_APP']))    
	{ 
		$_SESSION['IS_APP'] = $db->escape($_REQUEST['IS_APP']); 
	}
	
	
	 
	$sk_token = $db->escape($_REQUEST['sk_token']);  	
	$user_id = $db->escape($_REQUEST['id']);  	 	
	 if(   !empty($sk_token)  && !empty($user_id)  )
	{ 
		$sql = "Select user_pk 
		from access_token  					
		where access_token = '".$sk_token ."' and user_pk =  ".$user_id    ;
			// die($sql);  
		$user_data =   $db->query_first($sql) ;
		// var_dump($user_data);
		if( is_array($user_data)   && !empty($user_data['user_pk']))
		{
			$user_id  = $user_data['user_pk']; 
			
			$_SESSION['login_eviot_user_id']= $user_id  ;    
			$_SESSION['login_eviot_access_token']= $sk_token;   
			$url = "index.php";
		}  
	}
}
ob_clean(); 
header('location:'.$url);
	exit();
?>