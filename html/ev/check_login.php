<?php 
  
$msg = "";
$username = "";
$password =""; 

if($_SERVER["REQUEST_METHOD"] === "POST")
{
	  try{
	$msg = "Please Provide Login Details !";
	if(isset($_POST['txt_username'])  
		&& isset($_POST['txt_password']))
	{ 
		require_once('../dist/lib/db_class.php');  
		$username= $db->escape($_POST['txt_username']); 
		$password= $db->escape($_POST['txt_password']); 
	// var_dump(3);
	// var_dump($_POST);
	// die();
		// var_dump($username);
		// var_dump($password);
		if(!empty($username)  
			&& !empty($password)
		)
		{   
			$msg = "Login details are not valid !";
			$sql="Select  * from user 
			where phone ='".$username."' AND  password='".$password."'   ";
			   
			$data =   $db->query_first($sql) ; 
		    
			if($data != null && count($data) > 0  && !empty($data['user_pk']))
			{ 
				
				 if($data['is_active'] != ACTIVE_FLAG)
				 {
					 $msg = "Your account is not active !";
				 }
				 else
				 { 
					 
					$user_id = 	 $data['user_pk'];
					$device_id = 	 1;
					require_once("set_access_token.php");
		
					$_SESSION['login_eviot_user_id']= $data['user_pk'] ;    
					$_SESSION['login_eviot_access_token']= $sk_token;   
						 
					require_once('set_cookies.php');					 					
					ob_clean();
				header('location:index.php');
				// header('location:generate_session.php');
					exit();
						  
					  
				 }  
			} 
		}
	}
}
catch(Exception $e){
            
    $msg = 'Error : ' . $e->getMessage();
	 
}
}
?> 