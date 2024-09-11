<?php
$login_eviot_user_balance = 0;
	$sql = "Select sum(wallet_credit_amount)-sum(wallet_debit_amount) as wallet_amount from wallet
			where user_pk = ".$login_eviot_user_id;     ;
				   // var_dump($sql);  
			$wallet =   $db->query_first($sql) ;
			 // var_dump($user_data);
			 // die();
			if( is_array($wallet)   && !empty($wallet['wallet_amount']))
			{
				$login_eviot_user_balance = (float)$wallet['wallet_amount'];  
			}
?>