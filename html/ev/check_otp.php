<?php 
$mobile  = "";    
$email  = ""; 
$msg = "";
$otp = ""; 
 $is_valid = false;
 if(isset($_SESSION['register_phone'])  &&  isset($_SESSION['register_email']))      
{ 
	$mobile  = $_SESSION['register_phone'];    
	$email  = $_SESSION['register_email']; 
	if(!empty($mobile) && !empty($email))
	{
		$is_valid = true;
	}
}
if(!$is_valid)
{
	ob_clean(); 
	header('location:register.php');
	exit();
}
if($_SERVER["REQUEST_METHOD"] === "POST")
{
	 // var_dump($_POST);
	$msg = "Please Provide valid OTP !";
	if(isset($_POST['txt_otp']))
	{ 
		require_once('../dist/lib/db_class.php');  
		$otp= $db->escape($_POST['txt_otp']);  
		// var_dump($username);
		// var_dump($password);
		if(   !empty($mobile)  && !empty($otp)   && !empty($email) )
		{ 
			$sql = "Select  *   from user_unregistered  
					where phone = '".$mobile."' and e_mail = '".$email."'  
					and otp = '".$otp."' ";
						   // die($sql);  
			 $data =   $db->query_first($sql) ; 
			   // var_dump($data);
			if( is_array($data)   && !empty($data['user_pk']))
			{ 
				$idTag = '';
				$notFoundIDTag = true;
				while($notFoundIDTag)
				{
					$notFoundIDTag = false;
					$idTag = GenerateAlphaNumeric(ID_TAG_LENGTH);
					$sql = "Select  id_tag  from ocpp_tag  
					where id_tag  = '".$idTag."' ";
						 // die($sql);  
					 $data1 =   $db->query_first($sql) ; 
					   // var_dump($data);
					if( is_array($data1)   && !empty($data1['id_tag']))
					{ 
						$notFoundIDTag = true;
					}
				}
				if(empty($idTag))
				{
					$idTag = GenerateAlphaNumeric(ID_TAG_LENGTH).GetRandomDigits(6);
				}
				$cid = 1; //COMPANY
				if(isset($_SESSION['login_eviot_company'])      )    
				{  
					$cid = $_SESSION['login_eviot_company'];  
				}
				 $tag = array();   
				$tag['id_tag'] = $idTag ;	 
				$tag['max_active_transaction_count'] = 1; 
				$tag['expiry_date'] = '2033-12-31 00:00:00.000000'; 
				$tag['note'] = 'CREATED BY APP - '.CURRENT_DB_DATE; 
				$ocpp_tag_pk  =  $db->insert('ocpp_tag', $tag);
				
				$nru = array();  
				$nru['first_name'] = $data['first_name'];	
				$nru['last_name'] = $data['last_name'];	
				$nru['ocpp_tag_pk'] = $ocpp_tag_pk ;	
				$nru['charge_point_vendor_pk'] = $cid ;	
				$nru['phone'] = $mobile;	
				$nru['e_mail'] = $email;	
				$nru['referral_code'] = $data['referral_code'];	
				$nru['password'] = $data['password'];	
				$nru['creation_date'] = CURRENT_DB_DATE;
				$nru['update_date'] = CURRENT_DB_DATE;  
				$user_id =  $db->insert('user', $nru);
				 
				 
				 $sql  = "Delete from user_unregistered where user_pk = ".$data['user_pk']   ;
				$db->query($sql);
				 
				$device_id =1;
				require_once("set_access_token.php");
	
				$_SESSION['login_eviot_user_id']= $user_id;    
				$_SESSION['login_eviot_access_token']= $sk_token;    
				
				require_once('set_cookies.php');
				ob_clean();
				
				// var_dump($_SESSION);
	// var_dump($_COOKIE);
	
	// die();
	
	
				header('location:index.php');
				// header('location:generate_session.php');
				exit();
				  
				
			} 
		}  
	}

}
?> 