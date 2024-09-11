<?php
require_once('../dist/lib/config.php');  

require_once('../dist/lib/functions.php'); 
  
 require_once('../dist/lib/db_class.php');  
 if(isset($_REQUEST['sk_token'])  && isset($_REQUEST['id'])        )    
{ 
	$_SESSION['login_eviot_company'] = 1; //COMPANY
	if(isset($_REQUEST['cid'])      )    
	{ 
		$_SESSION['login_eviot_company'] = $db->escape($_REQUEST['cid']); 
	}
	
	$sk_token = $db->escape($_REQUEST['sk_token']);  	
	$user_id = $db->escape($_REQUEST['id']);  	 	
	  
	$_SESSION['login_eviot_user_id']= $user_id  ;    
	$_SESSION['login_eviot_access_token']= $sk_token;   
			 
		 
}
if(isset($_REQUEST['IS_APP']))    
{ 
	$_SESSION['IS_APP'] = $db->escape($_REQUEST['IS_APP']); 
}
 require_once('check.php'); 
    
 $PAGE_NAME = "Payment Status"; 
require_once('header_login.php');  
 
?>
 
<div class="login-box"  id="login-box">
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
	
  <div class="login-logo">
    <img src="../dist/img/logo-big.png" alt="ServiceKeeda Logo"  style="opacity: .8;width:150px">
      
  </div>
      
       

        
      <div class="social-auth-links text-center mb-3">
        <hr/>
      </div>
      <!-- /.social-auth-links -->

          <div class="row"> 
          <!-- /.col -->
          <div class="col-12  text-center"> 
            <h1>VERIFYING, PLEASE WAIT</h1>
          </div>
          <!-- /.col -->
        </div> 
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->
<?php  
require_once('footer_login.php'); 
 
if(!isset($_REQUEST['razorpay_order_id'])  &&  !isset($_REQUEST['razorpay_id']) &&  !isset($_REQUEST['razorpay_amount']))      
{
	echo "<script>alert('Invalid Session...')</script>";
	 ob_clean();
	header('location:add_credit.php');
	exit();
}
$razorpay_signature = "";
$razorpay_payment_id = "";
$razorpay_order_id = $_REQUEST['razorpay_order_id'];
$razorpay_id = $_REQUEST['razorpay_id'];
$razorpay_amount = $_REQUEST['razorpay_amount'];
// var_dump($_SESSION);
// var_dump($_REQUEST);
// // die();

if(isset($_REQUEST['razorpay_signature']) )  
{
	 $razorpay_signature = trim($_REQUEST['razorpay_signature']);
	 $razorpay_payment_id = trim($_REQUEST['razorpay_payment_id']);
	 
}
else if(isset($_REQUEST['error_code']) )  
{
	 $razorpay_payment_id = trim($_REQUEST['razorpay_payment_id']);
	 $error_code = trim($_REQUEST['error_code']);
	 $error_description = trim($_REQUEST['error_description']);
	 $error_source = trim($_REQUEST['error_source']);
	 $error_step = trim($_REQUEST['error_step']);
	 $error_reason = trim($_REQUEST['error_reason']);
	 
	 
	$nru = array();   
	$nru['razorpay_payment_id'] = $razorpay_payment_id;	 
	$nru['error_code'] = $error_code;	 
	$nru['error_description'] = $error_description;	 
	$nru['error_source'] = $error_source;	 
	$nru['error_step'] = $error_step;	 
	$nru['error_reason'] = $error_reason;	 
	$nru['is_active'] = REJECT_FLAG;	 
	$nru['update_date'] = CURRENT_DB_DATE;  
	$db->update('razorpay_transaction', $nru, 'razorpay_transaction_pk='.$razorpay_id);


	echo "<script>alert('".$error_description."')</script>";
	 ob_clean();
	header('location:profile.php');
	exit();
}
else  
{
	$nru = array();   
	$nru['is_active'] = REJECT_FLAG;	 
	$nru['update_date'] = CURRENT_DB_DATE;  
	 $db->update('razorpay_transaction', $nru, 'razorpay_transaction_pk='.$razorpay_id);
	ob_clean();
	header('location:profile.php');
	exit();
}
 
require('razorpay-php/Razorpay.php');



use Razorpay\Api\Api;

$api = new Api(SERVICEKEEDA_EVIOT_RAZORPAY_KEY_ID, SERVICEKEEDA_EVIOT_RAZORPAY_KEY_SECRET);
 
 try{
    $attributes = array(
        'razorpay_order_id' => $razorpay_order_id ,
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_signature' => $razorpay_signature 
    );
    $api->utility->verifyPaymentSignature($attributes);
}
catch(SignatureVerificationError $e){
            
    $error = 'Razorpay Error : ' . $e->getMessage();
	$nru = array();    
	$nru['error_code'] = "INVALID_SIGNATURE";	 
	$nru['error_description'] = $error;	 
	$nru['is_active'] = REJECT_FLAG;	 
	$nru['update_date'] = CURRENT_DB_DATE;  
	$db->update('razorpay_transaction', $nru, 'razorpay_transaction_pk='.$razorpay_id);

	echo "<script>alert('".$error."')</script>";
	 ob_clean();
	header('location:add_credit.php');
	exit();
}

$payment =    $api->payment->fetch($razorpay_payment_id);
// echo $payment->amount;
// var_dump($payment);
 
$razorpay_amount = (int)$payment->amount;
$razorpay_amount = $razorpay_amount/100;

$nru = array();    
$nru['amount'] = $razorpay_amount;	 
$nru['razorpay_signature'] = $razorpay_signature;	 
$nru['razorpay_payment_id'] = $razorpay_payment_id;	 
$nru['is_active'] = ACTIVE_FLAG;	 
$nru['update_date'] = CURRENT_DB_DATE;  
$db->update('razorpay_transaction', $nru, 'razorpay_transaction_pk='.$razorpay_id);



$nru = array();  
$nru['wallet_debit_amount'] = 0;	 	
$nru['wallet_credit_amount'] = $razorpay_amount;	 	
$nru['user_pk'] = $login_eviot_user_id;	 	
$nru['charging_booking_pk'] = 0;	 	
$nru['description'] = CREDIT_RECHARGE;	 	
$nru['creation_date'] = CURRENT_DB_DATE; 
$id =  $db->insert('wallet', $nru);


ob_clean();
header('location:profile.php');
exit();
?>