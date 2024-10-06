<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

header('Content-Type: application/json');
require_once('../dist/lib/config.php'); 
require_once('../dist/lib/db_class.php');   

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check for required fields
$requiredFields = ['first_name', 'email', 'mobile', 'password'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field])) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'status' => 'false',
            'code' => 400,
            'message' => "The field '$field' is required."
        ]);
        exit;
    }
}

$firstName = trim($data['first_name']);
$lastName = trim($data['last_name']);
$email = trim($data['email']);
$mobile = trim($data['mobile']);
$password = trim($data['password']);
$referralCode = trim($data['referral_code']);


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'false',
        'code' => 400,
        'message' => 'Please enter a valid email address.'
    ]);
    exit;
}

// Mobile number validation (assuming a standard 10-digit Indian number format)
if (!preg_match('/^[789]\d{9}$/', $mobile)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'false',
        'code' => 400,
        'message' => 'Invalid mobile number format. It should be a 10-digit number.'
    ]);
    exit;
}

// Basic input validation
if (empty($firstName) || empty($lastName) || empty($email) || empty($mobile) || empty($password) || empty($referralCode)) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'false',
        'code' => 400,
        'message' => 'All fields must be filled in.',
    ]);
    exit;
}

// Check if email or mobile already exists
$sql = "SELECT user_pk FROM user WHERE e_mail = '$email' OR phone = '$mobile'";
$result = $db->query_first($sql);

if ($result === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'status' => 'false',
        'code' => 500,
        'message' => 'An unexpected error occurred while processing your request. Please try again later.',
    ]);
    exit;
}

if (is_array($result) && !empty($result)) { 
    http_response_code(409); // Conflict
    echo json_encode([
        'status' => 'false',
        'code' => 409,
        'message' => 'User already exists with the provided email or mobile number.',
    ]);
    exit;
}

// Generate OTP
//$otp = rand(100000, 999999);
$otp = '123456';
$expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes')); // OTP valid for 10 minutes

// Your Textlocal API Key
// $apiKey = urlencode('NmM3NTMwNTE3YTRhNDQ2YzU0NTY0OTZjNjc3OTQ5NTQ=');

// // Sender ID
// $sender = urlencode('SERVEV'); // You can change this to your preferred sender ID

// // Recipient's phone number
// $numbers = '91'.$mobile; // Replace with the recipient's phone number

// // Message content
// $message = rawurlencode('Your OTP for verification is: '.$otp.'. Please enter this code to complete your process. This code is valid for 10 minutes.');

// // Prepare the data for the API request
// $data = array(
//     'apiKey' => $apiKey,
//     'numbers' => $numbers,
//     'sender' => $sender,
//     'message' => $message
// );

// print_r($data);
// // API URL
// $url = 'https://api.textlocal.in/send/';

// // Initialize cURL
// $ch = curl_init($url);

// // Set cURL options
// curl_setopt($ch, CURLOPT_POST, true);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// // Execute the request and fetch the response
// $response = curl_exec($ch);

// // Close cURL session
// curl_close($ch);

// // Print the response
// echo $response;


// Insert into temporary table
$tempUser = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'e_mail' => $email,
    'phone' => $mobile,
    'password' => $password,
    'referral_code' => $referralCode,
    'otp' => $otp,
    'otp_expiration' => $expiresAt
];

if ($db->insert('temp_user', $tempUser) === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'status' => 'false',
        'code' => 500,
        'message' => 'An error occurred while attempting to register your account. Please try again later.',
    ]);
    exit;
}

// Send OTP to user (implement actual sending logic)
//sendOtp($mobile, $otp); // You need to define this function to send the OTP via SMS or email

http_response_code(200); // OK
echo json_encode([
    'status' => 'success',
    'code' => 200,
    'message' => 'An OTP has been sent to your registered mobile number. Please check your messages.',
    'otp_expiration' => $expiresAt
]);

// OTP Verification Endpoint (Create a separate PHP file for this)
?>

