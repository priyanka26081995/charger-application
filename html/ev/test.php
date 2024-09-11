<?php
	require_once('../dist/lib/config.php');  
	require_once('../dist/lib/db_class.php');  
	require_once('../dist/lib/functions.php');  
  $data1 = GetChargingRate(22, RATE_COMMISSION);
  var_dump( $data1);
  $data2 = GetAmountRemovingGST(100, BILL_GST);
  var_dump( $data2);
  $data = GetChargingUnit( $data2, $data1);
  var_dump( $data);
  
$data = $charging_booking_order_id = ORDERID_PREFIX.sprintf('%0'.ORDERID_LENGTH.'d', 111);;
  var_dump( $data);
  

  
?>