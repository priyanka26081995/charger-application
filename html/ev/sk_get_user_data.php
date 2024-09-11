<?php
if(isset($user_id))
{	
	if(!empty($user_id))
	{
		$sql = "Update user set update_date = '".CURRENT_DB_DATE."' 
		where user_pk =  ".$user_id;
		$db->query($sql);
		
		
		


		$data_array = array();  
		 $data_array["app_version"] = '1.0.0.1'; 
		 $data_array["app_version_msg"] = 'A new updated version is available, please update to latest version of '.APP_NAME.' !'; 
			 
			//$data_array["cb_offer"] = array('solo'=> '20', 'trip'=>'','group'=>'' );
		  
		$sql = "Select u.user_pk, u.first_name, u.last_name, u.phone, u.e_mail, u.is_active
		, IFNULL(t.id_tag , '') as id_tag, IFNULL(a.street, '') as street, IFNULL(a.house_number, '') as house_number, IFNULL(a.zip_code, '') as zip_code, 
		IFNULL(a.city, '') as city, IFNULL(a.country	, '') as country	 
		from user as u
		left outer join address as a on a.address_pk = u.address_pk						
		left outer join ocpp_tag as t on u.ocpp_tag_pk  = t.ocpp_tag_pk 						
		where u.user_pk = ".$user_id ;
		// die($sql);  
		$user_data =   $db->query_first($sql) ;
		 	
		 if( is_array($user_data)   && !empty($user_data['user_pk']))
		{
			
			$user_array['user_id'] = $user_data['user_pk'];
			$user_array['first_name'] = $user_data['first_name'];
			$user_array['last_name'] = $user_data['last_name'];
			$user_array['phone'] = $user_data['phone'];
			$user_array['email'] = $user_data['e_mail'];
			$user_array['street'] = $user_data['street'];
			$user_array['house_number'] = $user_data['house_number'];
			$user_array['zip_code'] = $user_data['zip_code'];
			$user_array['id_tag'] = $user_data['id_tag'];
			$user_array['city'] = $user_data['city'];
			$user_array['country'] = $user_data['country']; 
			$user_array['wallet'] = 0; 
			$user_array['device_id'] = 1; 
			
			
			// $sql = "Select sum(wallet_credit_amount)-sum(wallet_debit_amount) as wallet_amount from wallet
			// where user_pk = ".$user_data['user_pk'];     ;
				   // // var_dump($sql);  
			// $wallet =   $db->query_first($sql) ;
			 // // var_dump($user_data);
			 // // die();
			// if( is_array($wallet)   && !empty($wallet['wallet_amount']))
			// {
				// $user_array['wallet'] = (float)$wallet['wallet_amount'];  
			// }
		}
	}
}
?>