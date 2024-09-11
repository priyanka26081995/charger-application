<?php	
	if(isset($_REQUEST['user_obj']))
	{
		$userObj  = json_decode($_REQUEST['user_obj']);  
		$gcm_id  = $db->escape($userObj->gcm_id); 
		// if(!empty($gcm_id))
		// {
			// if(isset($userObj->unique_uuid))
			// {
				$unique_uuid  = $db->escape($userObj->unique_uuid); 
				// if(!empty($unique_uuid))
				// {
					$platform  = $db->escape($userObj->platform); 
					$serial  = $db->escape($userObj->serial); 
					$imei  = $db->escape($userObj->imei); 
					$sim  = $db->escape($userObj->sim); 
					$manufacturer  = $db->escape($userObj->manufacturer); 
					$version  = $db->escape($userObj->version); 
			
				
					$uuid  = $db->escape($userObj->uuid); 
					$model  = $db->escape($userObj->model); 
					
					
					$data_sk = array();
					$data_sk['platform'] 		= $platform;  
					$data_sk['imei'] 		= $imei;  
					$data_sk['sim_no'] 		= $sim;  
					$data_sk['version'] 		= $version;  
					$data_sk['model'] 		= $model; 
					$data_sk['uuid'] 		= $uuid;  
					$data_sk['update_date'] 	= CURRENT_DB_DATE; 
					$data_sk['user_id'] 		= $user_id;  			
					// var_dump($data_sk);die();
					$sql1 = "Select device_id   from ".TABLE_DEVICE." 
					where  manufacturer = '".$manufacturer."'  and  serial = '".$serial."'  and unique_uuid = '".$unique_uuid."' ";
					$sql1 = "Select device_id   from ".TABLE_DEVICE." 
					where  user_id = '".$user_id."' ";
						   // die($sql);  
					$data1 =   $db->query_first($sql1) ; 
					// var_dump($data);
					if($data1 != null &&   is_array($data1)   && !empty($data1['device_id']))
					{  
						$device_id  = $data1['device_id']; 
						$db->update(TABLE_DEVICE, $data_sk, 'user_id='.$user_id);
					}
					else
					{  
						$data_sk['gcm_id'] 		= $gcm_id;  
						$data_sk['serial'] 		= $serial;  
						$data_sk['unique_uuid'] 		= $unique_uuid;  
						$data_sk['manufacturer'] 		= $manufacturer;  
						$data_sk['install_date'] 	= CURRENT_DB_DATE; 
						$device_id = $db->insert(TABLE_DEVICE, $data_sk); 
					}
				// }
			// }
		// }
	}
?>
				