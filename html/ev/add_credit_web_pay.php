<?php
require_once('../dist/lib/config.php');  

if(!isset($_REQUEST['amount'] )         )  
{ 
	ob_clean();
	header('location:add_credit.php');
	exit();
}

$amount = 0; 
try{
$amount = $_REQUEST['amount'];
$amount =  (int) trim($amount);
}catch(Exception $e){
	$amount = 0; 
}
// var_dump($amount);
 if(empty($amount))
 {
	 ob_clean();
	header('location:add_credit.php');
	exit();
 }
		 
require_once('../dist/lib/functions.php'); 
  
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');    
 $PAGE_NAME = "Make Payment"; 
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
            <h1>PLEASE WAIT</h1>
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


require('razorpay-php/Razorpay.php');
 use Razorpay\Api\Api;

$api = new Api(SERVICEKEEDA_EVIOT_RAZORPAY_KEY_ID, SERVICEKEEDA_EVIOT_RAZORPAY_KEY_SECRET);



$amount_in_paise = $amount *100;
$sk_razorpay_order_id = '';
if(isset($_SESSION['sk_razorpay_order_id']))
{
	if(!empty($_SESSION['sk_razorpay_order_id']))
	{ 
		$sk_razorpay_order_id = $_SESSION['sk_razorpay_order_id'];
		$sk_razorpay_id = $_SESSION['sk_razorpay_id']; 
		$amount = $_SESSION['sk_razorpay_amount'];
		$amount =  (int) trim($amount);
	}
}

// var_dump($_SESSION);
// var_dump('sk_razorpay_order_id-'.$sk_razorpay_order_id);
if(empty($sk_razorpay_order_id))
{
	$nru = array();   
	$nru['amount'] = $amount;  
	$nru['user_pk'] = $login_eviot_user_id;  
	$nru['is_active'] = INACTIVE_FLAG;	
	$nru['creation_date'] = CURRENT_DB_DATE;
	$nru['update_date'] = CURRENT_DB_DATE;  
	$sk_razorpay_id =  $db->insert('razorpay_transaction', $nru);



	$orderData = [
		'receipt'         => $sk_razorpay_id.'',
		'amount'          => $amount_in_paise , // 2000 rupees in paise
		'currency'        => 'INR',
		'payment_capture' => 1 // auto capture
	];

	$razorpayOrder = $api->order->create($orderData);

	$sk_razorpay_order_id = $razorpayOrder['id'];
	
	$nru = array();   
	$nru['razorpay_order_id'] = $sk_razorpay_order_id;	  
	 $db->update('razorpay_transaction', $nru, 'razorpay_transaction_pk='.$sk_razorpay_id);
}
else{
	 $sql = "Select  razorpay_transaction_pk    from razorpay_transaction  
			where razorpay_order_id = '".$sk_razorpay_order_id."' ";
				   // die($sql);  
		 $data =   $db->query_first($sql) ; 
		   // var_dump($data);
		if( is_array($data)   && !empty($data['razorpay_transaction_pk']))
		{
			$sk_razorpay_id = $data['razorpay_transaction_pk'];
		}
		else
		{
			$nru = array();   
			$nru['razorpay_order_id'] = $sk_razorpay_order_id;  
			$nru['amount'] = $amount;  
			$nru['user_pk'] = $login_eviot_user_id;  
			$nru['is_active'] = INACTIVE_FLAG;	
			$nru['creation_date'] = CURRENT_DB_DATE;
			$nru['update_date'] = CURRENT_DB_DATE;  
			$sk_razorpay_id =  $db->insert('razorpay_transaction', $nru);


  
		}
}
if(empty($amount))
{
	$amount = $_REQUEST['amount'];
	$amount =  (int) trim($amount);
}
$amount_in_paise = $amount *100;

$_SESSION['sk_razorpay_order_id']  = $sk_razorpay_order_id;
$_SESSION['sk_razorpay_id']  = $sk_razorpay_id;
$_SESSION['sk_razorpay_amount']  = $amount;

// var_dump('amount-'.$amount);
// var_dump('sk_razorpay_order_id-'.$sk_razorpay_order_id);
// var_dump($_SESSION);
// // die();
?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "<?php echo SERVICEKEEDA_EVIOT_RAZORPAY_KEY_ID; ?>", // Enter the Key ID generated from the Dashboard
    "amount": "<?php echo $amount_in_paise; ?>", // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
    "currency": "INR",
    "name": "ServiceKeeda EV IOT", //your business name
    "description": "Wallet Recharge",
    "image": "https://servicekeeda.com/assets/img/logo.png",
    "order_id": "<?php echo $sk_razorpay_order_id; ?>", //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
    "handler": function (response){
		console.log("HANDLER");
		console.log(response);
        window.location = 'add_credit_verify.php?razorpay_payment_id='+response.razorpay_payment_id + '&razorpay_signature='+ response.razorpay_signature;
    },
	"modal": { escape: false, ondismiss: function(){  window.location = 'add_credit_verify.php?payment_cancelled=YES'; } },
    "prefill": { //We recommend using the prefill parameter to auto-fill customer's contact information, especially their phone number
        "name": "<?php echo $login_eviot_user_name; ?>", //your customer's name
        "email": "<?php echo $login_eviot_user_email; ?>", 
        "contact": "<?php echo $login_eviot_user_mobile; ?>"  //Provide the customer's phone number for better conversion rates 
    },
    "notes": {
        "address": ""
    },
    "theme": {
        "color": "#3399cc"
    }
};
var rzp1 = new Razorpay(options);
rzp1.on('payment.failed', function (response){
		console.log("payment.failed");
		console.log(response);
		window.location = 'add_credit_verify.php?razorpay_payment_id='+response.error.metadata.payment_id + '&error_code='+ response.error.code + '&error_description='+ response.error.description + '&error_source='+ response.error.source + '&error_step='+ response.error.step + '&error_reason='+ response.error.reason;
         
           
});
 
    rzp1.open(); 
</script>