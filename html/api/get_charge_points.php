<?php

header('Content-Type: application/json');
require_once('../dist/lib/config.php'); 
require_once('../dist/lib/db_class.php');   

$data = json_decode(file_get_contents("php://input"), true);

// Check for required fields
$requiredFields = ['access_token', 'user_id', 'device_id', 'latitude', 'longitude'];
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
$accessToken = trim($data['access_token']);
$userId = trim($data['user_id']);
$deviceId = trim($data['device_id']);
$mobile = trim($data['mobile']);
$latitude = trim($data['latitude']);
$longitude = trim($data['longitude']);

$data_array = array();
$sql = "Select  c.charge_box_pk ,cv.charge_point_vendor_pk,cv.charge_point_vendor, c.charge_box_id   ,  
c.location_latitude  , c.location_longitude 
, IFNULL(a.street, '') as street, IFNULL(a.house_number, '') as house_number, IFNULL(a.zip_code, '') as zip_code, 
IFNULL(a.city, '') as city, IFNULL(a.country    , '') as country            
from charge_box as c  
  join charge_point_vendor as cv on c.charge_point_vendor_pk = cv.charge_point_vendor_pk    
left outer join address as a on a.address_pk = c.address_pk 
where c.IS_ACTIVE = 1 and c.registration_status = 'Accepted' and c.location_latitude = $latitude and c.location_longitude = $longitude";
 
$data_list =   $db->fetch_array($sql) ; 
if( count($data_list) > 0  )
{    
    foreach($data_list as  $data_sk)
    {
        $charge_box_pk = $data_sk['charge_box_pk'];
        $charge_box_id = $data_sk['charge_box_id'];
        $sql = "Select  c.connector_pk  , c.connector_name    ,
        c.connector_id    , cp.min_charging_rate  , 
            cp.charging_rate_unit  ,
            IFNULL((Select status from connector_status where  connector_pk = c.connector_pk order by  status_timestamp  desc limit 1 ), 'Not Available') as connector_status               
            from connector as c  
                join connector_charging_profile as ccp on c.connector_pk  = ccp.connector_pk    
                join charging_profile as cp on ccp.charging_profile_pk  = cp.charging_profile_pk    
                where  c.IS_ACTIVE = 1 and  c.charge_box_id = '".$charge_box_id."' ";
             $connector_list =   $db->fetch_array($sql) ; 
             
            if(count($connector_list) > 0)
            {
                 $array = $data_sk;
                 $array['location_latitude'] = $array['location_latitude'];
                 $array['location_longitude'] = $array['location_longitude'];
                 $array['connector'] = $connector_list;
                  // var_dump($array);
                 $data_array[] = $array;
            }
    }
    
}
echo json_encode($data_array);