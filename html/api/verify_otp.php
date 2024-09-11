<?php
// verify_otp.php

header('Content-Type: application/json');
require_once('../dist/lib/config.php'); 
require_once('../dist/lib/db_class.php');   

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check for required fields
if (!isset($data['mobile']) || !isset($data['otp'])) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'error',
        'code' => 400,
        'message' => 'Missing required fields.',
        'details' => "The fields 'phone' and 'otp' are required."
    ]);
    exit;
}

$phone = trim($data['mobile']);
$otp = trim($data['otp']);

//Check user exist

$sql1 = "SELECT * FROM user WHERE phone = '$phone' and is_active = 1";
$user = $db->query_first($sql1);

if ($user) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'error',
        'code' => 400,
        'message' => 'User Already Exist',
    ]);
    exit;
}

// Fetch the temporary user record
$sql2 = "SELECT * FROM temp_user WHERE phone = '$phone' AND otp = '$otp' AND otp_expiration > NOW()";
$tempUser = $db->query_first($sql2);

if (!$tempUser) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 'error',
        'code' => 400,
        'message' => 'Invalid OTP or OTP expired.',
    ]);
    exit;
}

// Move data from temp_user to user table
$user = [
    'first_name' => $tempUser['first_name'],
    'last_name' => $tempUser['last_name'],
    'e_mail' => $tempUser['e_mail'],
    'phone' => $tempUser['phone'],
    'password' => $tempUser['password'],
    'referral_code' => $tempUser['referral_code']
];

if ($db->insert('user', $user) === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'status' => 'error',
        'code' => 500,
        'message' => 'User registration failed.',
    ]);
    exit;
}

// Remove the temporary user record
$sql = "DELETE FROM temp_user WHERE phone = '$phone'";
if ($db->query($sql) === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        'status' => 'error',
        'code' => 500,
        'message' => 'Failed to delete temporary record.',
    ]);
    exit;
}

http_response_code(200); // OK
echo json_encode([
    'status' => 'success',
    'message' => 'User registered successfully.'
]);
?>
