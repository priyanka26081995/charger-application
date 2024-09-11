<?php
require_once('../dist/lib/config.php');  
require_once('../dist/lib/db_class.php');  
require_once('../dist/lib/functions.php');  
try 
{
	$json_array = array();
	$json_array["status"] =  "fail";
	$json_array["title"] =  "No Data";
	$json_array["data"] =  "Your Session has expired , Please Sign In Again !!!"; 
	   

	// var_dump($_REQUEST);
	if(isset($_REQUEST['user_id']) && isset($_REQUEST['sk_token'])   && isset($_REQUEST['GCM_ID'])  
		&& isset($_REQUEST['PLATFORM']) && isset($_REQUEST['SERIAL'])   && isset($_REQUEST['MANUFACTURER'])  
		&& isset($_REQUEST['VERSION']) && isset($_REQUEST['UNIQUE_UUID'])   && isset($_REQUEST['MODEL'])  
		&& isset($_REQUEST['IMEI']) && isset($_REQUEST['SIM_NO'])  
		)    
	{ 
		if(isset($_REQUEST['IS_APP']))    
		{ 
			$_SESSION['IS_APP'] = $db->escape($_REQUEST['IS_APP']); 
		}
		  $device_id = 0;
		$user_id  = $db->escape($_REQUEST['user_id']);  
		$sk_token  = $db->escape($_REQUEST['sk_token']);  
		$GCM_ID  = $db->escape($_REQUEST['GCM_ID']);    
		$PLATFORM  = $db->escape($_REQUEST['PLATFORM']);    
		$SERIAL  = $db->escape($_REQUEST['SERIAL']);    
		$MANUFACTURER  = $db->escape($_REQUEST['MANUFACTURER']);    
		$VERSION  = $db->escape($_REQUEST['VERSION']);    
		$UNIQUE_UUID  = $db->escape($_REQUEST['UNIQUE_UUID']); 
		$MODEL  = $db->escape($_REQUEST['MODEL']); 
		$IMEI  = $db->escape($_REQUEST['IMEI']); 
		$SIM_NO  = $db->escape($_REQUEST['SIM_NO']); 


		if(   !empty($user_id)  && !empty($sk_token) )
		{ 
			 $sql = "Select   a.user_pk   
			from access_token  as a  	
			where a.access_token = '".$sk_token ."' and a.user_pk =  ".$user_id    ;
				// die($sql);  
			$user_data =   $db->query_first($sql) ;
			  // var_dump($user_data);
			if( is_array($user_data)   && !empty($user_data['user_pk']))
			{ 
				$data_sk = array();
				$data_sk['platform'] 		= $PLATFORM;  
				$data_sk['imei'] 		= $IMEI;  
				$data_sk['sim_no'] 		= $SIM_NO;  
				$data_sk['version'] 		= $VERSION;  
				$data_sk['model'] 		= $MODEL; 
				$data_sk['uuid'] 		= $UNIQUE_UUID;  
				$data_sk['update_date'] 	= CURRENT_DB_DATE; 
				$data_sk['user_pk'] 		= $user_id;  
				$data_sk['gcm_id'] 		= $GCM_ID;  
			 
				$sql1 = "Select device_pk   from user_device
				where  manufacturer = '".$MANUFACTURER."'  and  model = '".$MODEL."'  
				and  serial = '".$SERIAL."'  
				and unique_uuid = '".$UNIQUE_UUID."' and user_pk = '".$user_id."' ";
				// $sql1 = "Select device_pk   from ".TABLE_DEVICE." 
				// where  user_pk = '".$user_id."' ";
					   // // die($sql);  
				$data1 =   $db->query_first($sql1) ; 
				  // var_dump($data1);
				if( $data1 != null &&  is_array($data1) > 0  && !empty($data1['device_pk']))
				{  
					$device_id  = $data1['device_pk']; 
					$db->update('user_device', $data_sk, 'device_pk='.$device_id);
				}
				else
				{  
					$data_sk['serial'] 		= $SERIAL;  
					$data_sk['unique_uuid'] 		= $UNIQUE_UUID;  
					$data_sk['manufacturer'] 		= $MANUFACTURER;  
					$data_sk['install_date'] 	= CURRENT_DB_DATE; 
					$device_id = $db->insert("user_device", $data_sk); 
				}
				$sql = " Update access_token  set device_pk = ".$device_id."  	
				where  access_token = '".$sk_token ."' and  user_pk =  ".$user_id    ;
				$db->query($sql);
				
				$json_array["status"] =  "ok";
				$json_array["title"] =  "Success"; 
				$json_array["data"] =  $device_id ; 
			  
			}
		 } 
	}
}
//catch exception
catch(Exception $e) {
   $json_array["status"] =  "fail";
						$json_array["title"] =  "Error";
						$json_array["data"] =  "Something went wrong , Try after sometime....";
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
  