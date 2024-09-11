<?php

  // $_SESSION['login_eviot_user_id'] = 11; //user 
  // $_SESSION['login_eviot_company'] = 1; //company
  // $_SESSION['login_eviot_access_token'] = 'a7bc1afd36348aa0d9b508eb860ad0d4'; //TOKEN
  
  // var_dump($_SESSION);
 $check_flag = false;
 $login_eviot_id_tag = '';  
 $login_eviot_company = 1;  
 $login_eviot_company_show_all = true;  
 $login_eviot_user_id = '';  
 $login_eviot_device_id = '';  
 $login_eviot_access_token = '';  
 $login_eviot_user_name = '';  
 $login_eviot_user_mobile = '';  
 $login_eviot_user_email = '';  
 $login_eviot_user_balance = 1000;   
 	 
if(isset($_SESSION['login_eviot_company'])        )  
{
	$login_eviot_company = $_SESSION['login_eviot_company']  ; 
}
if(isset($_SESSION['login_eviot_access_token'])  &&  isset($_SESSION['login_eviot_user_id']))      
{ 
	
	$sk_token = $_SESSION['login_eviot_access_token'];  	
	$user_id = $_SESSION['login_eviot_user_id'];  
	 if(   !empty($sk_token)  && !empty($user_id)   )
	{ 
		$sk_token = $_SESSION['login_eviot_access_token'];  	
		$user_id = $_SESSION['login_eviot_user_id']; 
		$sql = "Select a.device_pk, a.user_pk ,a.access_token , it.id_tag, 
		u.first_name, u.last_name,  u.e_mail,  u.phone,   u.is_active
		from access_token  as a 
		join user as u on u.user_pk = a.user_pk		
		join ocpp_tag as it on u.ocpp_tag_pk = it.ocpp_tag_pk		
		where a.access_token = '".$sk_token ."' and a.user_pk =  ".$user_id    ;
			   // var_dump($sql);  
		$user_data =   $db->query_first($sql) ;
		 // var_dump($user_data);
		 // die();
		if( is_array($user_data)   && !empty($user_data['user_pk']))
		{
			$check_flag  = true;
			$login_eviot_id_tag = $user_data['id_tag'];  
			$login_eviot_user_id = $user_data['user_pk'];  
			 $login_eviot_device_id = $user_data['device_pk']; 
			 $login_eviot_access_token = $user_data['access_token'];  
			 $login_eviot_user_name = $user_data['first_name'].' '.$user_data['last_name'] ;   
			 $login_eviot_user_mobile = $user_data['phone']  ;   
			 $login_eviot_user_email = $user_data['e_mail']  ;   
			  
			 $sql = "Select * from charge_point_vendor
			where charge_point_vendor = '".$login_eviot_company."'";     ;
				   // var_dump($sql);  
			$cmp_data =   $db->query_first($sql) ;
			 // var_dump($user_data);
			 // die();
			if( is_array($cmp_data)   && !empty($cmp_data['charge_point_vendor']))
			{
				$login_eviot_company_show_all = empty($cmp_data['show_all_charger']) ? false :true;  
			} 
			
			
			
			require_once('get_wallet_balance.php');
			require_once('set_cookies.php');
			 	
				
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