<?php

function GenerateAccessToken()
{
	$access_token = md5(uniqid().rand(1000000, 9999999));
	return $access_token;
}
function GetMinimumRate($rate, $commission, $gst, $minimum_amount)
{
	$rate_with_gst = 0;
	if($rate > 0)
	{
		$rate_with_gst = GetChargingRate($rate, $commission);
		// $rate_with_gst = GetAmountWithGST($rate_with_gst, $gst);
		$rate_with_gst = ceil($rate_with_gst);
		if($rate_with_gst < $minimum_amount)
		{
		 $rate_with_gst = $minimum_amount;
		} 
	}
	 return $rate_with_gst;
}
function GetChargingUnitInKwh($start_value, $end_value ) {
    $result = 0;
	if($start_value > 0 && $end_value > 0)
	{ 
		$result =  $end_value - $start_value; //IN WATT
		if(!empty($result))
		{
			$result = $result/1000;
		}
	}
	// return round($result,  2);
	return  $result ;
}
function GetChargingUnit($total_amount, $rate ) {
    $result = 0;
	if($rate > 0 && $total_amount > 0)
	{ 
		$result = $total_amount /$rate;
	}
	return round($result,  2);
}
function GetChargingRate($amount, $charges) {
    $result = $amount;
	if($amount > 0 && $charges > 0)
	{ 
		$result = $amount + ($amount*($charges/100));
	}
	return round($result,  2);
}
function GetAmountRemovingGST($amount, $gst) {
    $result = $amount;
	if($amount > 0 && $gst > 0)
	{
		$gst_amount = $amount - ($amount * (100/(100+$gst)));
		
		$result = $amount - $gst_amount;
	}
	return round($result,  2);
}
function GetAmountWithGST($amount, $gst) {
    $result = $amount;
	if($amount > 0 && $gst > 0)
	{
		$gst_amount = $amount - ($amount * (100/(100+$gst)));
		
		$result = $amount + $gst_amount;
	}
	return round($result,  2);
}
function GetDecimalAmount($amount, $precision  = 2) {
		
	$result = number_format((float)$amount, $precision, '.', '');
	 
	return $result ;
}
function GetRandomDigits($length) {
    $result = '';

    for ($i = 0; $i < $length; $i++) {
        $result .= random_int(0, 9);
    }

    return $result;
}
function GenerateAlphaNumeric($length, $isUpper = true)
{
	$bytesString  = '';
	if($length > 0)
	{
		$bytes = (random_bytes($length));
		
		$bytesString = bin2hex($bytes);
		if($isUpper)
		{
			return strtoupper($bytesString);
		}
		else 
		{
			return $bytesString;
		}
	}
	else
	{
		return $bytesString;
	}
}



?>