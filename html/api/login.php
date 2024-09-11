<?php
header('Content-Type: application/json');
require_once('../dist/lib/config.php'); 
require_once('../dist/lib/db_class.php');   

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if mobile and password are set
if (!isset($data['mobile']) || !isset($data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
    exit();
}

// Sanitize inputs
$mobile = trim($data['mobile']);
$password = trim($data['password']);

// execute the query
$sql = "SELECT user_pk, password FROM user WHERE phone = '$mobile'";
$result = $db->query_first($sql);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(is_array($result) && !empty($result))
{    // Verify password
    if ($password == $result['password']) {
        $device_id =1;
        $user_id = $result['user_pk'];
        require_once("../ev/set_access_token.php");
        echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid mobile or password']);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Invalid mobile or password']);
    exit();
}


?>
