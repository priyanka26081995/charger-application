<?php

$check_flag  = false;
	require_once('../dist/lib/config.php');  
	require_once('../dist/lib/db_class.php'); 
	// var_dump($_SESSION);
	// var_dump($_COOKIE);
	
	// die();
	// ob_clean();
	// header('Location: ./') ; 
	// exit(); 
if(isset($_SESSION['login_eviot_access_token'])  &&  isset($_SESSION['login_eviot_user_id']))      
{ 
	
	$sk_token = $_SESSION['login_eviot_access_token'];  	
	$user_id = $_SESSION['login_eviot_user_id'];  
	 if(   !empty($sk_token)  && !empty($user_id)   )
	{ 
		$sk_token = $_SESSION['login_eviot_access_token'];  	
		$user_id = $_SESSION['login_eviot_user_id']; 
		$sql = "Select a.device_pk, a.user_pk ,a.access_token ,
		u.first_name, u.last_name,   u.is_active
		from access_token  as a 
join user as u on u.user_pk = a.user_pk		
		where a.access_token = '".$sk_token ."' and a.user_pk =  ".$user_id    ;
			   
		$user_data =   $db->query_first($sql) ;
		   //var_dump($user_data);
		 //die($sql); 
		if( is_array($user_data)   && !empty($user_data['user_pk']))
		{
			$check_flag  = true;
			$login_eviot_user_id = $user_data['user_pk'];  
			 $login_eviot_device_id = $user_data['device_pk']; 
			 $login_eviot_access_token = $user_data['access_token'];  
			 $login_eviot_user_name = $user_data['first_name'].' '.$user_data['last_name'] ;   
			    ob_clean();
	header('Location: ./') ; 
	exit();	 
				
		}  
	}
}


if(!$check_flag)
{  
	ob_clean();
	header('Location: logout.php') ; 
	exit();
} 
?>