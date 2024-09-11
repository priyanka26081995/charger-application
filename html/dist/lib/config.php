<?php
ob_start();
// var_dump(realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/nexoneweb.com/revolution2020s/tmp/sessions'));die();
session_save_path(realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/nexoneweb.com/revolution2020s/tmp/sessions'));
session_start() ;  
error_reporting(E_ALL);
// error_reporting(E_ERROR | E_PARSE);
 	error_reporting(0);
// ini_set("memory_limit","200M"); 
date_default_timezone_set('Asia/Calcutta'); 
$time = date("H:i a");
$current_db_date = date("Y-m-d");
$current_db_date_time = date("Y-m-d H:i:s");
$current_date = date("d-m-Y"); 

@set_time_limit(10000000);
ini_set('max_execution_time', '100000'); 
 ini_set('memory_limit', '1024M');

define('DB_HOST', 'localhost:3306');    
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'servicekeedadb'); 

// define('DB_USERNAME', 'root');
// define('DB_PASSWORD', '');
// define('DB_NAME', 'servicekeedadb'); 
 
 
define('SITE_PATH', substr(dirname(__FILE__),0,-4));
define('SITE_LIB_PATH', SITE_PATH.'\lib'); 

define('SITE_URL', 'http://iot.servicekeeda.com/'); 
define('SITE_API_URL', 'http://iot.servicekeeda.com/restapi/'); 
 
 
define('APP_NAME', "ServiceKeeda ECharger App"); 
define('SITE_NAME', "ServiceKeeda"); 
$PAGE_NAME = "ADMIN PANEL";


//ALL KEYS
// define('SERVICEKEEDA_EVIOT_MAP_MARKER_IMG', 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'); 
define('SERVICEKEEDA_EVIOT_MAP_MARKER_IMG', 'http://iot.servicekeeda.com/dist/marker/'); 
define('SERVICEKEEDA_EVIOT_MAP_API_KEY', "AIzaSyCqGsOPiT-Uc9L0DvlAYyf6RigeE9ZAiZ8"); 
define('SERVICEKEEDA_EVIOT_RAZORPAY_KEY_ID', "rzp_live_Ns9x6Mjez0xEFV"); 
define('SERVICEKEEDA_EVIOT_RAZORPAY_KEY_SECRET', "jidUEDQ3oJoegQLU3ELCasxw"); 
define('SERVICEKEEDA_EVIOT_RAZORPAY_CURRENCY', "INR"); 
define('SERVICEKEEDA_FCM_SERVER_KEY', "AIzaSyBcT-xMuhfCi1Rnc-ejUIdP7FNsoE8Y5PM");  
 
 
 
//WALLET STATUS
define("CANCELLATION_REFUND", 'Cancellation Refund');
define("BOOKING_CHARGES", 'Booking Charge');
define("ORDER_REFUND", 'Order Refund');
define("NEW_BOOKING", 'New Booking');
define("CREDIT_RECHARGE", 'Credit Recharge');
 
//TRANSACTON METER READING PARAMS Flag 
define("TRANSACTION_BEGIN", "Transaction.Begin");
define("TRANSACTION_READING", "Sample.Periodic");
define("TRANSACTION_MEASURAND_READING", "Energy.Active.Import.Register");
define("TRANSACTION_END", "Transaction.End");



//IS_ACTIVE Flag 
define("INACTIVE_FLAG", "0");//PENDING CHARGING/PENDING RAZORPAY
define("ACTIVE_FLAG", "1"); //COMPLETED CHARING/SUCCESS RAZORPAY
define("PROCESS_FLAG", "2"); //CHARGING 
define("REJECT_FLAG", "3"); //CANCELLED CAHRGING /FAILED-CANCELLED RAZORPAY


//COOKIES
define("COOKIE_USER_ID", "COOKIE_USER_ID");  
define("COOKIE_TOKEN", "COOKIE_TOKEN"); 
define("COOKIE_RAZORPAY_ORDER_ID", "COOKIE_RAZORPAY_ORDER_ID"); 
define("COOKIE_RAZORPAY_ID", "COOKIE_RAZORPAY_ID"); 
define("COOKIE_RAZORPAY_AMOUNT", "COOKIE_RAZORPAY_AMOUNT"); 
define("COOKIE_RAZORPAY_PREFILL_NAME", "COOKIE_RAZORPAY_PREFILL_NAME"); 
define("COOKIE_RAZORPAY_PREFILL_EMAIL", "COOKIE_RAZORPAY_PREFILL_EMAIL"); 
define("COOKIE_RAZORPAY_PREFILL_MOBILE", "COOKIE_RAZORPAY_PREFILL_MOBILE"); 


define("START_TRANSACTION", "Pending");  
define("BILL_GST", 18); 
define("RATE_COMMISSION", 4.125); 
define("ID_TAG_LENGTH", 14); 
define("ORDERID_LENGTH", 8); 
define("ORDERID_PREFIX", "SK"); 
define("MINIMUM_BOOKING_AMOUNT", 20); 
 

define("MAIL_SMTP_HOST", "stmp.gmail.com"); 
define("MAIL_SMTP_PORT", 465); 
define("MAIL_SMTP_USERNAME", "smedies.app@gmail.com"); 
define("MAIL_SMTP_PASSWORD", "sscp@family"); 
define("MAIL_SMTP_REPLY_EMAIL_ID", "sunil.kumbhar43@gmail.com"); 
define("MAIL_SMTP_SENDER_NAME", "Smedies");  


define("CURRENT_DB_DATE", $current_db_date_time); 
define("CURRENT_DATE", $current_date); 


$USER_ROLES = array("ADMIN", "RESELLER", "USER");
 
define("ROLE_ADMIN", 0); 
define("ROLE_RESELLER", 1); 
define("ROLE_USER", 2); 




 

setcookie('COOKIE_RAZORPAY_ORDER_ID', '', time()-2000);
$_COOKIE['COOKIE_RAZORPAY_ORDER_ID'] = '';
setcookie('COOKIE_RAZORPAY_ID', '', time()-2000);
$_COOKIE['COOKIE_RAZORPAY_ID'] = '';
setcookie('COOKIE_RAZORPAY_AMOUNT', '', time()-2000);
$_COOKIE['COOKIE_RAZORPAY_AMOUNT'] = '';
setcookie('COOKIE_RAZORPAY_PREFILL_NAME', '', time()-2000);
$_COOKIE['COOKIE_RAZORPAY_PREFILL_NAME'] = '';
setcookie('COOKIE_RAZORPAY_PREFILL_EMAIL', '', time()-2000);
$_COOKIE['COOKIE_RAZORPAY_PREFILL_EMAIL'] = '';
setcookie('COOKIE_RAZORPAY_PREFILL_MOBILE', '', time()-2000);
$_COOKIE['COOKIE_RAZORPAY_PREFILL_MOBILE'] = '';
   


?>