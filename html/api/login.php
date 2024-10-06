<?php
header('Content-Type: application/json');
require_once('../dist/lib/config.php'); 
require_once('../dist/lib/db_class.php');   

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if mobile and password are set
if (!isset($data['mobile']) || !isset($data['password'])) {
    echo json_encode(['status' => 'false', 'message' => 'Mobile number and password are required.']);
    exit();
}

// Sanitize inputs
$mobile = trim($data['mobile']);
$password = trim($data['password']);

// execute the query
$sql = "SELECT user_pk,ocpp_tag_pk,address_pk,first_name,last_name,phone,e_mail,referral_code,password FROM user WHERE phone = '$mobile'";
$result = $db->query_first($sql);

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if(is_array($result) && !empty($result))
{    // Verify password
    if ($password == $result['password']) {
        $user_id = $result['user_pk'];
        $sql1 = "SELECT access_token FROM access_token WHERE user_pk = '$user_id'";
        $getAccessToken = $db->query_first($sql1);
        $responseData = array(
            'user_id' => $result['user_id'],
            'first_name' => $result['first_name'],
            'last_name' => $result['last_name'],
            'phone' => $result['phone'],
            'e_mail' => $result['e_mail'],
            'referral_code' => $result['referral_code'],
            'access_token' => $getAccessToken['access_token']
        );
        $device_id =1;
        require_once("../ev/set_access_token.php");
        echo json_encode(['status' => 'true','code' => 200, 'message' => 'Login successful', 'data' => $responseData]);
    } else {
        echo json_encode(['status' => 'false', 'message' => 'Invalid mobile number or password.']);
    }
}
else{
    echo json_encode(['status' => 'false', 'message' => 'Invalid mobile number or password.']);
    exit();
}


?>
