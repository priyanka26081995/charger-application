<?php


require_once('../dist/lib/config.php');  
require_once('../dist/lib/db_class.php');  

	
// FCM Server URL
$url = 'https://fcm.googleapis.com/fcm/send';

 

// Specify the recipient device token
$deviceToken = 'dobjsq7xRVe_Kz2zmF0ZNc:APA91bGgWDZfmLt8wjbIP4mbBlz-zw-EpTqrQHkHgGTfpwICN82XejH6RvtRjX7cpq5svonNfsNc6WvsBoYKP_y4kVJCIdvwUYg8vN7EfDH1GdUlwYIFLVTM9Q138inEleccAJFqN-gS';

// The notification payload
$notification = [
    'title' => 'Test Notification',
    'body' => 'This is a test notification from PHP script'
];

// The data payload (optional)
$data = [
    'key1' => 'value1',
    'key2' => 'value2'
];

// Construct the FCM request body
$fields = [
    'to' => $deviceToken,
    'notification' => $notification,
    'data' => $data
];

// Headers for the HTTP request
$headers = [
    'Authorization: key=' . SERVICEKEEDA_FCM_SERVER_KEY,
    'Content-Type: application/json'
];

// Initialize curl
$ch = curl_init();

// Set the URL, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

// Execute the curl request
$result = curl_exec($ch);

// Check for any errors
if ($result === FALSE) {
    die('Curl failed: ' . curl_error($ch));
}

// Close curl
curl_close($ch);

// Display the result
echo $result;
?>