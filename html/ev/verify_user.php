<?php
require_once('../dist/lib/config.php');  
require_once('../dist/lib/db_class.php');  
require_once('../dist/lib/functions.php');  
try 
{
	$json_array = array(); 
	 $json_array["status"] =  "fail";
	$json_array["title"] =  "Error";
	$json_array["data"] =  "Your Session has expired , Please Sign In Again !!!"; 


	if(isset($_REQUEST['user_id'])  &&  isset($_REQUEST['sk_token']))      
	{ 
		$_SESSION['login_eviot_company'] = 1; //COMPANY
		if(isset($_REQUEST['cid'])      )    
		{ 
			$_SESSION['login_eviot_company'] = $db->escape($_REQUEST['cid']); 
		}
		if(isset($_REQUEST['IS_APP']))    
		{ 
			$_SESSION['IS_APP'] = $db->escape($_REQUEST['IS_APP']); 
		}
		$user_id  = $db->escape($_REQUEST['user_id']);  
		$sk_token  = $db->escape($_REQUEST['sk_token']);  

		if(   !empty($sk_token)  && !empty($user_id)   )
		{ 
			$sql = "Select   a.user_pk   
			from access_token  as a  	
			where a.access_token = '".$sk_token ."' and a.user_pk =  ".$user_id    ;
			// die($sql);  
			$user_data =   $db->query_first($sql) ;
			// var_dump($user_data);
			if( is_array($user_data)   && !empty($user_data['user_pk']))
			{

				$user_array = array();

				require_once("sk_get_user_data.php");
				if(count($user_array) > 0)
				{ 
					if(!isset($device_id))
					{
						$device_id = 	 0;
					}
					require_once("set_access_token.php");

					$user_array['access_token'] = $sk_token; 
					$json_array["status"] =  "ok";
					$json_array["title"] =  "Success"; 
					$json_array["data"] =  $user_array ; 
				}	 
			}

		}
	}
}

//catch exception
catch(Exception $e) {
   $json_array["status"] =  "fail";
	$json_array["title"] =  "Error";
	// $json_array["data"] =  "Something went wrong , Try after sometime....";
	$json_array["data"] =  "ERROR : ".$e->getMessage();
}

 // var_dump($json_array);
 $html = json_encode($json_array, JSON_HEX_APOS|JSON_HEX_QUOT);
 // $html = json_encode($json_array, true);
 //Set these headers to avoid any issues with cross origin resource sharing issues
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with');
  
 echo $html; 


// var_dump(json_decode($html));
?>
  