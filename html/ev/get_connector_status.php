<?php
require_once('../dist/lib/config.php'); 
require_once('../dist/lib/functions.php'); 
 $connector_status = "NO";
$connector_pk = '';
 if(isset($_REQUEST['id'])        )  
{
	$connector_pk = $_REQUEST['id']; 
 
	if(!empty($connector_pk))
	{
	
	 
		require_once('../dist/lib/db_class.php');  
		require_once('check_ajax.php'); 

		$sql = " Select status from connector_status where  connector_pk =  ".$connector_pk."
		 order by  status_timestamp  desc limit 1  ";
		$user_data =   $db->query_first($sql) ;
		 // var_dump($user_data);
		 // die();
		if( is_array($user_data)   && !empty($user_data['status']))
		{
			$connector_status = trim($user_data['status']);
		}			
		 // if($connector_status != 'Available' && $connector_status != 'Preparing' && $connector_status != 'Charging'    )
		// {
			// $connector_status = "NO";
		// }
	}
}

ob_clean(); 
echo $connector_status;
exit();