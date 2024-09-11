<?php 

$msg = "";
$user_id = "";
$first_name = "";
$last_name =""; 
$email =""; 
$phone =""; 
$password ="";
$referral_code = "";
if($_SERVER["REQUEST_METHOD"] === "POST")
{
	 // var_dump($_POST);
	$msg = "Please Provide Valid Details ";
	if(isset($_POST['txt_firstname'])   
		&& isset($_POST['txt_lastname'])
		&& isset($_POST['txt_email'])
		&& isset($_POST['txt_phone']) 
		&& isset($_POST['txt_password']) )
	{ 
		require_once('../dist/lib/db_class.php');  
		  
		$first_name= $db->escape($_POST['txt_firstname']);   
		$last_name= $db->escape($_POST['txt_lastname']);   
		$email= $db->escape($_POST['txt_email']); 
		$phone= $db->escape($_POST['txt_phone']);  
		 $password= $db->escape($_POST['txt_password']); 
		 
		// var_dump($username);
		// var_dump($password);
		if(!empty($first_name)  
			&& !empty($last_name)
			&& !empty($email)
			&& !empty($phone)
			&& !empty($password)
		)
		{   
			 $sql = "Select  user_pk   from user  
				where phone = '".$phone."' ";
					   // die($sql);  
			 $data =   $db->query_first($sql) ; 
			   // var_dump($data);
			if( is_array($data)   && !empty($data['user_pk']))
			{ 
				$msg = "Mobile is already registered ";
			 
			}  
			else
			{
				 $sql = "Select  user_pk   from user  
					where e_mail = '".$email."' ";
						   // die($sql);  
				 $data =   $db->query_first($sql) ; 
				   // var_dump($data);
				if( is_array($data)   && !empty($data['user_pk']))
				{ 
					$msg =  "Email Registered"; 
				 
				}  
				else
				{
					
					$pass= rand(1000, 9999);
					$pass = 1234 ;
					$nru = array();  
					$nru['first_name'] = $first_name;	
					$nru['last_name'] = $last_name;	
					$nru['phone'] = $phone;	
					$nru['e_mail'] = $email;	
					$nru['referral_code'] = $referral_code;	
					$nru['password'] = $password;	
					$nru['otp'] = $pass;	
					
					
					$sql = "Select  user_pk   from user_unregistered  
					where e_mail = '".$email."' or phone = '".$phone."' ";
						   // die($sql);  
					 $data =   $db->query_first($sql) ; 
					   // var_dump($data);
					if( is_array($data)   && !empty($data['user_pk']))
					{   
						$db->update('user_unregistered', $nru, 'user_pk='.$data['user_pk']);
					}
					else
					{
						 $db->insert('user_unregistered', $nru);
					}
					 $_SESSION['register_phone']= $phone ;    
					$_SESSION['register_email']= $email ;    
					     
					
					ob_clean(); 
					header('location:verfiy_otp.php' );
					exit();
				}
			}
		}
	}


}
?> 