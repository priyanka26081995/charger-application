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
            'status' => 'error',
            'code' => 400,
            'message' => 'Missing required fields.',
            'details' => "The field '$field' is required."
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

// Basic input validation
if (empty($firstName) || empty($lastName) || empty($email) || empty($mobile) || empty($password) || empty($referralCode)) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'error',
        'code' => 400,
        'message' => 'Invalid input data.',
        'details' => 'All fields must be filled in.'
    ]);
    exit;
}

// Check if email or mobile already exists
$sql = "SELECT user_pk FROM user WHERE e_mail = '$email' OR phone = '$mobile'";
$result = $db->query_first($sql);

if ($result === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'status' => 'error',
        'code' => 500,
        'message' => 'Database query failed.',
        'details' => $conn->error
    ]);
    exit;
}

if (is_array($result) && !empty($result)) { 
    http_response_code(409); // Conflict
    echo json_encode([
        'status' => 'error',
        'code' => 409,
        'message' => 'User Already Exist',
        'details' => 'An account with this email or mobile number already exists.'
    ]);
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);
$expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes')); // OTP valid for 10 minutes

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
        'status' => 'error',
        'code' => 500,
        'message' => 'user registration failed.',
    ]);
    exit;
}

// Send OTP to user (implement actual sending logic)
//sendOtp($mobile, $otp); // You need to define this function to send the OTP via SMS or email

http_response_code(200); // OK
echo json_encode([
    'status' => 'success',
    'message' => 'OTP sent on your registered mobile number'
]);

// OTP Verification Endpoint (Create a separate PHP file for this)
?>

