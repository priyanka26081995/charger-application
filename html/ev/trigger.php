<?php 
 if(isset($getData) || isset($_REQUEST['cmd']))
{
	// // Script to test if the CURL extension is installed on this server

	// // Define function to test
	// function _is_curl_installed() {
		// if  (in_array  ('curl', get_loaded_extensions())) {
			// return true;
		// }
		// else {
			// return false;
		// }
	// }

	// // Ouput text to user based on test
	// if (_is_curl_installed()) {
	  // echo "cURL is <span style=\"color:blue\">installed</span> on this server";
	// } else {
	  // echo "cURL is NOT <span style=\"color:red\">installed</span> on this server";
	// }
	// // die();
	try{
	  // Steve OCPP HTTP client emulator (v1.6)
	  // NOTE: Do not use the & symbol in login, steve password, and AuthKey. It won't work.

	  // Configuration
	  $steveServerAddresLogin = 'http://iot.servicekeeda.com:6002/servicekeeda/manager/'; 
	  $steveServerAddres = 'http://iot.servicekeeda.com:6002';
	  $steveLogin = 'admin';
	  $stevePass = 'Pravi@9797';
	  
	  $ocppProtocol = 'JSON'; // or SOAP
	  $ocppVersion = 'v1.6';  // Your OCPP version
	  $supervision = 'servicekeeda';
	  // Only for SOAP use - charge point endpoint url
	  // Write here your charge point endpoint url
	  $endpointURL = 'http://localhost:9090/ocpp'; // (ex: http://localhost:9090/ocpp)

	  // Steve commands path array
	  $stevePathArray = array(
		// Local cmd (not use)
		'signin' => '/' . $supervision . '/manager/signin',
		'getTransaction' => '/' . $supervision . '/manager/transactions',
		// OCPP cmd
		'getConnectorState' => '/' . $supervision . '/manager/home/connectorStatus',
		'ReserveNow' => '/' . $supervision . '/manager/operations/' . $ocppVersion . '/ReserveNow',
		'RemoteStartTransaction' => '/' . $supervision . '/manager/operations/' . $ocppVersion . '/RemoteStartTransaction',
		'RemoteStopTransaction' => '/' . $supervision . '/manager/operations/' . $ocppVersion . '/RemoteStopTransaction',
		'UnlockConnector' => '/' . $supervision . '/manager/operations/' . $ocppVersion . '/UnlockConnector',
		'DataTransfer' => '/' . $supervision . '/manager/operations/' . $ocppVersion . '/DataTransfer',
		'Reset' => '/' . $supervision . '/manager/operations/' . $ocppVersion . '/Reset',
		'SetChargingProfile' => '/' . $supervision . '/manager/operations/' . $ocppVersion . '/SetChargingProfile'
	  );

	  // Variables
	  if(!isset($getData))
	  {
		$getData = $_REQUEST;
	  }
	    $SK_REQUEST = $getData;
	  $curl = "";

	  // Using endpoint? (for SOAP only)
	  if($ocppProtocol == 'JSON') { $endpointURL = '-'; }

	  // Functions
	  // # cURL init
	  function curlConnectionInit($curl) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_COOKIESESSION, true);
		curl_setopt($curl, CURLOPT_COOKIEFILE, "cookiefile");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36');
		return $curl;
	  }

	  // # Connect to URL
	  function curlConnectTo($steveServerAddres, $stevePathArray) {
		global $curl;
		$steveServerURL = $steveServerAddres . $stevePathArray;
		// var_dump($steveServerURL);die();
		curl_setopt($curl, CURLOPT_URL, $steveServerURL);
		$content = curl_exec($curl);
		//echo $content;
		return $content;
	  }

	  // # Get CSRF token
	  function getCSRFToken($content) {
		preg_match('/<input type="hidden" name="_csrf" value="(.*)"/Uis', $content, $csrf);
		$csrf = $csrf[1];
		if($csrf != NULL) {
		  return $csrf;
		} else {
		  return NULL;
		}
	  }

	  // # SignIn to Steve panel
	  function steveSignIn($login, $pass) {
		global $curl, $steveServerAddres, $stevePathArray;
		$content = curlConnectTo($steveServerAddres, $stevePathArray['signin']);
		$token = getCSRFToken($content);
		$form = "username=".$login."&password=".$pass."&_csrf=".$token ;
		// var_dump($form);die();
		curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
		$rr = curl_exec($curl);
		 // var_dump($rr);die();
	  }

	  // # Get element by class (for HTML DOM parse)
	  function getElementsByClass(&$parentNode, $tagName, $className) {
		$nodes = array();
		$childNodeList = $parentNode->getElementsByTagName($tagName);
		for($i = 0; $i < $childNodeList->length; $i++) {
		  $temp = $childNodeList->item($i);
		  if(stripos($temp->getAttribute('class'), $className) !== false) {
			return $temp;
		  }
		}
	  }

	  // # Parse html (Get data)
	  function htmlParser($getData, $mode) {
		  echo"<br/><br/>htmlParser<br/>";
		  echo($getData);
		  echo"<br/><br/>";
		global $curl, $content, $steveServerAddres, $stevePathArray;

		// NULL ChargeBoxID protection
		if($getData['ChargeBoxID'] == NULL) {
		  return 'NullBoxID';
		}

		// Null mode protectio
		if($mode == NULL) {
		  return 'NullMode';
		}

		// Get content
		// Not used for DataTransfer (DataTransfer connected before calling this function)
		if($mode != 'getDataTransferResponse') {
		  $content = curlConnectTo($steveServerAddres, $stevePathArray[$mode]);
		}

		// Create DOM element
		$domdoc = new DOMDocument();
		libxml_use_internal_errors(true);
		$domdoc->loadHTML($content);
		$domdoc->saveHTML();

		// Get table (for DataTransfer respond)
		if($mode == 'getDataTransferResponse') {
		  $domdoc = getElementsByClass($domdoc, 'table', 'res');
		}

		// Get 'th' array
		$thArray = $tdArray = $array = array();
		$th = $domdoc->getElementsByTagName('th');
		foreach ($th as $th) {
		  $thArray[] = $th->nodeValue;
		}
		$count = count($thArray);

		// Get 'td' and create result array
		$dataArray = [];
		foreach($domdoc->getElementsByTagName('tr') as $tr) {
		  $data = $tr->getElementsByTagName('td');
		  if($data != null && count($data) > 0) {
			  $tdArray = [];
			  foreach($data as $td) {
				$tdArray[] = $td->textContent;
			  }
			$dataArray[] = @array_combine($thArray, $tdArray);
		  }
		}

		// Select mode and get values
		// GetTransaction
		if($mode == 'getTransaction') {
		  // Search transaction ID
		  foreach ($dataArray as $key) {
			if(($key['ChargeBox ID'] == $getData['ChargeBoxID']) && ($key['Connector ID'] == $getData['ConnectorID'])) {
			  // Return transaction ID
			  return $key['Transaction ID'];
			}
		  }
		  return 'TransactionNotExist';
		}

		// Get connector state (Fixing the space issue)
		  else if($mode == 'getConnectorState') {
		  // Input data
		  $requiredChargeBoxID = $getData['ChargeBoxID'];
		  $requiredConnectorID = $getData['ConnectorID'];

			  foreach($dataArray as $key) {
				// Сlear the spaces
			$existChargeBoxID = trim($key['ChargeBox ID']);
			$existConnectorID = trim($key['Connector ID']);
			// Compare
				  if(($requiredChargeBoxID == $existChargeBoxID) && ($requiredConnectorID == $existConnectorID)) {
					return $key['Status'];
				  }
			  }
			  return 'StateNotExist';
		  }

		// Get DataTransfer response
		else if($mode == 'getDataTransferResponse') {
		  $response = $dataArray[1]['Response'];
		  if($response == NULL) {
			return 'ResponseNotFound';
		  }
		  return $response;
		}
	  }

	  // # Command selector
	  function cmdInputSelector($getData) {
		global $curl, $content, $steveServerAddres, $stevePathArray, $ocppProtocol, $endpointURL;

		// Set path
		$stevePath = $stevePathArray[$getData['cmd']];
	// var_dump($stevePath);die();
		// Select command
		switch ($getData['cmd']) {
		  case 'getConnectorState':
			$allow = true; // Allow command?
			if($allow) {
			  // Get connector state
			  $connectorState = htmlParser($getData, 'getConnectorState');
			  return $connectorState;
			}
			break;
		  case 'ReserveNow':
			$allow = true; // Allow command?
			if($allow) {
			  // Redirect to ReserveNow page
			  $content = curlConnectTo($steveServerAddres, $stevePath);
			  // Get token
			  $token = getCSRFToken($content);
			  // Prepare form
			  $form = "chargePointSelectList=".$ocppProtocol.";".$getData['ChargeBoxID'].";".$endpointURL."&connectorId=".$getData['ConnectorID']."&expiry=".$getData['Expiry']."&idTag=".$getData['idTag']."&_csrf=".$token."";
			  // Send form
			  curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
			  curl_exec($curl);
			  return 'Ok';
			}
			break;
		  case 'RemoteStartTransaction':
			$allow = true; // Allow command?
			if($allow) {
			  // Redirect to RemoteStartTransaction page
			  $content = curlConnectTo($steveServerAddres, $stevePath);
			  // Get token
			  $token = getCSRFToken($content);
			  // Prepare form
			  $form = "chargePointSelectList=".$ocppProtocol.";".$getData['ChargeBoxID'].";".$endpointURL."&connectorId=".$getData['ConnectorID']."&idTag=".$getData['idTag']."&_csrf=".$token."";
			  // Send form
			  curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
			  $contentResponse  = curl_exec($curl);
			  echo"<br/><br/>contentResponse<br/>";
			  echo($contentResponse);
			  echo"<br/><br/>";
			  return 'Ok';
			}
			break;
			case 'GetTransaction':
			$allow = true; // Allow command?
			if($allow) {
			  // Redirect to GetTransaction page
			  $content = curlConnectTo($steveServerAddres, $stevePath);
			  // Get token
			  $token = getCSRFToken($content);
			  // Prepare form
			  $form = "chargePointSelectList=".$ocppProtocol.";".$getData['ChargeBoxID'].";".$endpointURL."&connectorId=".$getData['ConnectorID']."&idTag=".$getData['idTag']."&_csrf=".$token."";
			  // Send form
			  curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
			  $sql = "SELECT * FROM stevedb.transaction_start where id_tag = '2286a72122' order by transaction_pk desc 
	limit 1";
			  $contentResponse  = curl_exec($sql);
			  echo"<br/><br/>contentResponse<br/>";
			  echo($contentResponse);
			  echo"<br/><br/>";
			  return 'Ok';
			}
			break;
		  case 'RemoteStopTransaction':
			$allow = true; // Allow command?
			if($allow) {
			  // Get transaction ID
			  // $steveTransactionID = htmlParser($getData, 'getTransaction');
			  $steveTransactionID = $getData['TransactionID'];
			  // Redirect to RemoteStopTransaction page
			  $content = curlConnectTo($steveServerAddres, $stevePath);
			  // Get token
			  $token = getCSRFToken($content);
			  // Prepare form
			  $form = "chargePointSelectList=".$ocppProtocol.";".$getData['ChargeBoxID'].";".$endpointURL."&transactionId=".$steveTransactionID."&_csrf=".$token."";
			  // Send form
			  curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
			  $contentResponse  = curl_exec($curl);
			   echo"<br/><br/>contentResponse<br/>";
			  echo($contentResponse);
			  echo"<br/><br/>";
			  return 'Ok';
			}
			break;
		  case 'UnlockConnector':
			$allow = true; // Allow command?
			if($allow) {
			  // Redirect to RemoteStartTransaction page
			  $content = curlConnectTo($steveServerAddres, $stevePath);
			  // Get token
			  $token = getCSRFToken($content);
			  // Prepare form
			  $form = "chargePointSelectList=".$ocppProtocol.";".$getData['ChargeBoxID'].";".$endpointURL."&connectorId=".$getData['ConnectorID']."&_csrf=".$token."";
			  // Send form
			  curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
			  curl_exec($curl);
			  return 'Ok';
			}
			break;
		  case 'DataTransfer':
			$allow = true; // Allow command?
			if($allow) {
			  // Redirect to RemoteStartTransaction page
			  $content = curlConnectTo($steveServerAddres, $stevePath);
			  // Get token
			  $token = getCSRFToken($content);
			  // Prepare form
			  $form = "chargePointSelectList=".$ocppProtocol.";".$getData['ChargeBoxID'].";".$endpointURL."&vendorId=".$getData['VendorID']."&messageId=".$getData['MessageID']."&data=".$getData['Data']."&_csrf=".$token."";
			  // Send form
			  curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
			  curl_exec($curl);
			  // Sleep.. (Wait response)
			  sleep(5);
			  // Get response
			  $content = curl_exec($curl);
			  // Parse response
			  $response = htmlParser($getData, 'getDataTransferResponse');
			  // Return response
			  return $response;
			}
			break;
		  case 'Reset':
			$allow = true; // Allow command?
			if($allow) {
			  // Redirect to Reset page
			  $content = curlConnectTo($steveServerAddres, $stevePath);
			  // Get token
			  $token = getCSRFToken($content);
			  // Prepare form
			  $cbid = explode(";",$getData['ChargeBoxID']);
				  $toReset = "";
				  for($i = 0; $i < count($cbid); $i++) {
					  $toReset = $toReset . "chargePointSelectList=".$ocppProtocol.";" .$cbid[$i] . ";".$endpointURL."&";
				  }
			  $form = $toReset . "_chargePointSelectList=1&resetType=HARD&_csrf=".$token."";
			  // Send form
			  curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
			  curl_exec($curl);
			  return 'Ok';
			}
			break;
		  case 'SetChargingProfile':
			 $allow = true; // Allow command?
			 if($allow) {
			   // Redirect to Reset page
			   $content = curlConnectTo($steveServerAddres, $stevePath);
			   // Get token
			   $token = getCSRFToken($content);
			   // Prepare form
			   $form = "chargePointSelectList=".$ocppProtocol.";".$getData['ChargeBoxID'].";".$endpointURL."&connectorId=".$getData['ConnectorID']."&chargingProfilePk=".$getData['ChargingProfileID']."&_csrf=".$token."";
			   // Send form
			   curl_setopt($curl, CURLOPT_POSTFIELDS, $form);
			   curl_exec($curl);
			   return 'Ok';
			 }
			break;
		  default:
			// Unknown command
			return 'Unknown command';
			break;
		}

		// Command not allow?
		if(!$allow) {
		  return 'Command NotAllow';
		}

	  }

	  /*** Сode execution ***/
	  // Auth
	  // var_dump($SK_REQUEST);
	  // var_dump('<br/><br/>');
	  // var_dump($getData);
	  if(!isset($getData['id'])  || !isset($getData['cmd'])    )    
	{ 
		ob_clean();
		  echo 'Something went Wrong. Try After sometime';
		 exit();
	  }
	  
	require_once('../dist/lib/config.php'); 
	require_once('../dist/lib/functions.php');  
	require_once('../dist/lib/db_class.php');  
	 
	  
	  $charging_booking_pk = $db->escape($SK_REQUEST['id']);  	
			
		 if(   empty($charging_booking_pk)   )
		{ 
			ob_clean();
			echo 'Something went Wrong. Try After sometime';
			exit();
		}
		$cmd = $db->escape($SK_REQUEST['cmd']);  
		$getData['cmd'] = "RemoteStartTransaction";
		
		$msg = "Something went Wrong. Try After sometime";	
		$sql = "Select bboking.*,IFNULL(bboking.transaction_pk, 0) as transaction_pk ,cb.charge_box_pk ,cb.charge_point_vendor, cb.charge_box_id   , cb.charge_point_vendor,
	c.connector_pk  , c.connector_name    ,
	c.connector_id    , cp.min_charging_rate  , ct.connector_type, ct.current_type,	
	cp.charging_rate_unit  ,c.connector_capacity,
	IFNULL((Select status from connector_status where  connector_pk = c.connector_pk order by  status_timestamp  desc limit 1 ), 'Not Available') as connector_status	

		from charging_booking   as bboking  
		join connector as c  on c.connector_pk = bboking.connector_pk 
	join connector_type as ct on c.connector_type_pk  = ct.connector_type_pk 	
	join charge_box as cb on c.charge_box_id  = cb.charge_box_id 	
	join connector_charging_profile as ccp on c.connector_pk  = ccp.connector_pk 	
	join charging_profile as cp on ccp.charging_profile_pk  = cp.charging_profile_pk 	
		
		where   bboking.charging_booking_pk =  ".$charging_booking_pk ;
				 // die($sql);  
		$charging_booking =   $db->query_first($sql) ; 
		 // var_dump($charging_booking);
		 $isCharging = true;
		if( is_array($charging_booking)   && !empty($charging_booking['charging_booking_pk']))
		{
			$login_eviot_user_id = $charging_booking['user_pk'];
			$msg = "Charger is ".$charging_booking['connector_status'] ;
			if($charging_booking['connector_status'] == 'Preparing' || $charging_booking['connector_status']  == 'Charging')
			{
				$msg = '';
			}
		} 
		if(   !empty($msg) )
		{ 
			ob_clean();
			echo $msg;
			exit();
		}
		$sql = "Select  it.id_tag, u.user_pk,
		u.first_name, u.last_name,   u.is_active
		from  user as u  		
		join ocpp_tag as it on u.ocpp_tag_pk = it.ocpp_tag_pk		
		where u.user_pk =  ".$login_eviot_user_id    ;
			   // var_dump($sql);  
		$user_data =   $db->query_first($sql) ;
		 // var_dump($user_data);
		 // die();
		if( is_array($user_data)   && !empty($user_data['user_pk']))
		{ 
			$login_eviot_id_tag = $user_data['id_tag'];   
			 $login_eviot_user_name = $user_data['first_name'].' '.$user_data['last_name'] ;   
		}
		else
		{ 
			ob_clean();
			echo "Invalid User";
			exit();
		}
		$charging_final_amount = floatval($charging_booking['charging_final_amount']) ;
		$charging_booked_amount = floatval($charging_booking['charging_booked_amount']) ;
		$getData['ChargeBoxID'] = $charging_booking['charge_box_id'] ;
		$getData['ConnectorID'] = $charging_booking['connector_id'] ;
		$getData['idTag'] =  $login_eviot_id_tag ;
		$getData['TransactionID'] = $charging_booking['transaction_pk'] ;
		
		if(empty($cmd))
		{
			// die('asdadsfsfdssd');
			$getData['cmd'] = "RemoteStopTransaction";
			if(empty($getData['TransactionID']))
			{
				$sql1 = "Select   transaction_pk  from transaction_start  
				where id_tag = '".$login_eviot_id_tag."' and connector_pk = ".$charging_booking['connector_pk']." and transaction_pk not in (Select   transaction_pk  from transaction_stop)
				 order by event_timestamp desc limit 1 ";

			 
					  
				 $data_sk1 =   $db->query_first($sql1) ; 
				 //var_dump($data_list);
					 
				if( is_array($data_sk1)   && !empty($data_sk1['transaction_pk']))
				{ 
					$transaction_pk = $data_sk1['transaction_pk'];
					$sql = 'update charging_booking set transaction_pk = '.$transaction_pk.' 
					where charging_booking_pk =  '.$charging_booking_pk ;
					$db->query($sql);
					$getData['TransactionID'] = $transaction_pk ;
				}
			}
			
			$sql = 'update charging_booking set is_active = '.ACTIVE_FLAG.' 
			where charging_booking_pk =  '.$charging_booking_pk ;
			$db->query($sql);
			
			if($charging_booked_amount > $charging_final_amount)
			{ 
				$wallet_credit_amount = $charging_booked_amount-$charging_final_amount;
				$sql = "Select charging_booking_pk from wallet   	where  description = '".ORDER_REFUND."' and   charging_booking_pk =  ".$charging_booking_pk ;
			 // die($sql);  
				$wallet_booking =   $db->query_first($sql) ; 
				 // var_dump($charging_booking);
				if( is_array($wallet_booking)   && !empty($wallet_booking['charging_booking_pk']))
				{
					$sql = "update wallet set wallet_credit_amount = '".$wallet_credit_amount."' 
					where  description = '".ORDER_REFUND."' and   charging_booking_pk =  ".$charging_booking_pk ;
					$db->query($sql);
				}
				else
				{
					$nru = array();  
					$nru['wallet_debit_amount'] = 0;	 	
					$nru['wallet_credit_amount'] = $wallet_credit_amount;	 	
					$nru['user_pk'] = $login_eviot_user_id;	 	
					$nru['charging_booking_pk'] = $charging_booking_pk;	 	
					$nru['description'] = ORDER_REFUND;	 	
					$nru['creation_date'] = CURRENT_DB_DATE; 
					$id =  $db->insert('wallet', $nru); 
				}
			}
			else if($charging_final_amount > $charging_booked_amount)
			{ 
				$wallet_debit_amount = $charging_final_amount-$charging_booked_amount;
				$sql = "Select charging_booking_pk from wallet   	where  description = '".BOOKING_CHARGES."' and   charging_booking_pk =  ".$charging_booking_pk ;
			 // die($sql);  
				$wallet_booking =   $db->query_first($sql) ; 
				 // var_dump($charging_booking);
				if( is_array($wallet_booking)   && !empty($wallet_booking['charging_booking_pk']))
				{
					$sql = "update wallet set wallet_debit_amount = '".$wallet_debit_amount."' 
					where  description = '".BOOKING_CHARGES."' and  charging_booking_pk =  ".$charging_booking_pk ;
					$db->query($sql);
				}
				else
				{
					$nru = array();  
					$nru['wallet_credit_amount'] = 0;	 	
					$nru['wallet_debit_amount'] = $wallet_debit_amount;	 
					$nru['user_pk'] = $login_eviot_user_id;	 	
					$nru['charging_booking_pk'] = $charging_booking_pk;	 	
					$nru['description'] = BOOKING_CHARGES;	 	
					$nru['creation_date'] = CURRENT_DB_DATE; 
					$id =  $db->insert('wallet', $nru); 
				}
			} 
				
		}
		else
		{
			$sql = 'update charging_booking set is_active = '.PROCESS_FLAG.' 
			where charging_booking_pk =  '.$charging_booking_pk ;
			$db->query($sql);
		}
		  // var_dump($getData);
		// die('kjkjkjkjk');
		// die('asdavxxcvdasd');
	  // Init connection
	  $curl = curlConnectionInit($curl);

	  // SignIn Steve panel
	  steveSignIn($steveLogin, $stevePass);
	// die('NOT DONE');
	  // Select page and send cmd
	  $result = cmdInputSelector($getData);

	 
	  ob_clean();
	  echo "OK"; 
	  // echo $result;
			exit();
	}

	//catch exception
	catch(Exception $e) {
		  ob_clean();
	  echo 'Message: ' .$e->getMessage();
			exit();
	} 
}
else
{
	echo "Invalid request";
	
}
 ?>
