<?php
try{
require_once('../dist/lib/config.php');  
 require_once('../dist/lib/db_class.php');  
 require_once('check.php');  
 require_once('check_transaction.php');  
 $PAGE_NAME = "Dashboard";
 
 
  
							
require_once('header.php'); 
$data_array = array();
$sql = "Select  c.charge_box_pk ,cv.charge_point_vendor_pk,cv.charge_point_vendor, c.charge_box_id   ,  
c.location_latitude  , c.location_longitude 
, IFNULL(a.street, '') as street, IFNULL(a.house_number, '') as house_number, IFNULL(a.zip_code, '') as zip_code, 
IFNULL(a.city, '') as city, IFNULL(a.country	, '') as country			
from charge_box as c  
  join charge_point_vendor as cv on c.charge_point_vendor_pk = cv.charge_point_vendor_pk	
left outer join address as a on a.address_pk = c.address_pk	
where c.IS_ACTIVE = 1 and c.registration_status = 'Accepted' ";
 // die($sql);  
 
$data_list =   $db->fetch_array($sql) ; 
 // var_dump($data_list);die();
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
						  // var_dump($sql);  
			 // var_dump($connector_list);
			 // die();
			 if(count($connector_list) > 0)
			 {
				 $array = $data_sk;
				 $array['location_latitude'] = number_format( ( $array['location_latitude']), 4);
				 $array['location_longitude'] = number_format( ( $array['location_longitude']), 4);
				  // $array['connector'] = $connector_list;
				  // var_dump($array);
				 $data_array[] = $array;
			 }
	}
	
}
 // var_dump($data_array);die();
?>
 
    <style>
        /* Set the size of the map */
        #map { 
            width: 100%;
        }
		
		.container, .container-fluid, .container-lg, .container-md, .container-sm, .container-xl {
    width: 100%;
    padding-right: 0px !important;
    padding-left: 0px !important;
    margin-right: auto;
    margin-left: auto;
}

#description {
  font-family: Roboto;
  font-size: 15px;
  font-weight: 300;
}

#infowindow-content .title {
  font-weight: bold;
}

#infowindow-content {
  display: none;
}

#map #infowindow-content {
  display: inline;
}

.pac-card {
  background-color: #fff;
  border: 0;
  border-radius: 2px;
  box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
  margin: 10px;
  padding: 0 0.5em;
  font: 400 18px Roboto, Arial, sans-serif;
  overflow: hidden;
  font-family: Roboto;
  padding: 0;
}

#pac-container {
  padding-bottom: 12px;
  margin-right: 12px;
}

.pac-controls {
  display: inline-block;
  padding: 5px 11px;
}

.pac-controls label {
  font-family: Roboto;
  font-size: 13px;
  font-weight: 300;
}

#pac-input {
  background-color: #fff;
  font-family: Roboto;
  font-size: 15px;
  font-weight: 300;
  margin-left: 12px;
  padding: 0 11px 0 13px;
  text-overflow: ellipsis;
  width: 400px;
}

#pac-input:focus {
  border-color: #4d90fe;
}

#title {
  color: #fff;
  background-color: #4d90fe;
  font-size: 25px;
  font-weight: 500;
  padding: 6px 12px;
}

#target {
  width: 345px;
}
    </style> 
<div class="row">
  <div class="col-12">
   <input
      id="pac-input" style="display:none"
      class="controls"
      type="text"
      placeholder="Search Box"
    />
    <div id="map" style="height:100vh;"></div> 
		</div>
	</div> 
	
	<div class="modal fade" id="modal-connector">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="lblHeading"></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="divConnector">
               
            </div>
            <div class="m-1  ">
			
				<div class="row p-2" >
					  <div class="col-6"> 
						<button type="button" onclick="OpenNavigation()" class="btn btn-block btn-default">Navigate</button>
					</div>
					  <div class="col-6"> 
						<button type="button" class="btn btn-block btn-default">View Details</button>
					</div>
				</div>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
     
<?php
require_once('footer.php'); 

}catch(Exception $ex){
	var_dump($ex);
	
}
?>
	 <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo SERVICEKEEDA_EVIOT_MAP_API_KEY ; ?>&loading=async&callback=initMap">
    </script>
<script>
var firstChild_marginBottom= 0;
var zoomLevel = 18;
var markersData = JSON.parse('<?php echo json_encode($data_array);?>') ;
var charger_latitude = "";
var charger_longitude = "";
function GotoBooking(lat, lng, charge_box_pk, charge_box_id, charge_point_vendor)
{
	charger_latitude = lat;
	charger_longitude = lng;
	$.ajax(
		'get_connector.php',
		{
			data: { 'id': charge_box_pk}
		}
	).done(function (data) {
		if(data.indexOf('SESSIONEXPIRED') > -1)
		{
			window.location = 'login.php';
		}
		else
		{
			$('#lblHeading').html(charge_point_vendor);
			$('#divConnector').html(data);
		
			$('#modal-connector').modal('show');
		}
	});
	
} 

 

       // Initialize and add the map
        function SetMapHeight() {
			 var height = $(window).height();
			 var footerHeight = $('#divFooter').height();
			 
			 var mapHeight = height-footerHeight;
			 $('#map').height(mapHeight);
			 // $('#map').css("height", mapHeight+"px !important");
			 // $('#map').style("height:" + mapHeight+"px !important");
		}
        function initMap() {
            SetMapHeight();
            // var center = { lat: 19.05114284243138, lng: 73.07664166922643 };
            var center = { lat: 18.46846300, lng: 73.79213700 };
            
            // Create the map
            var map = new google.maps.Map(
                document.getElementById('map'), { zoom: zoomLevel, center: center ,mapTypeControl: false,fullscreenControl: false , mapTypeControl: false, 
   scaleControl: false,
   scrollwheel: false,
   navigationControl: false,
   streetViewControl: false,disableDefaultUI: true 
});

  // const input = document.getElementById("pac-input");
  // const searchBox = new google.maps.places.SearchBox(input);

  // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
			  // Create bounds object to contain all markers
            var bounds = new google.maps.LatLngBounds();
			
			 
			
			  // Add markers to the map
            markersData.forEach(function(markerInfo) {
				
				var image = {
                 url: '<?php echo SERVICEKEEDA_EVIOT_MAP_MARKER_IMG ; ?>marker_' + markerInfo.charge_point_vendor_pk + '.png',
                 // This marker is 32 pixels wide by 32 pixels high.
                size: new google.maps.Size(32, 32),
                // The origin for this image is (0, 0).
                origin: new google.maps.Point(0, 0),
                // The anchor for this image is the base of the flagpole at (0, 32).
                anchor: new google.maps.Point(0, 16)
            };
				var myLatlng =  { lat: parseFloat(markerInfo.location_latitude) , lng: parseFloat(markerInfo.location_longitude) };
				  // myLatlng =  { lat: 40.7128, lng: -74.0060 };
                var marker = new google.maps.Marker({
                    position: myLatlng,
                    map: map,
					 icon: image,
					title: markerInfo.charge_point_vendor,
					location_latitude: markerInfo.location_latitude,
					location_longitude: markerInfo.location_longitude,
					charge_box_pk: markerInfo.charge_box_pk,
					charge_box_id: markerInfo.charge_box_id,
					charge_point_vendor: markerInfo.charge_point_vendor
                });
                
				marker.addListener('click', function () {
					// Alert when marker is clicked
					GotoBooking(marker.location_latitude, marker.location_longitude, marker.charge_box_pk, marker.charge_box_id, marker.charge_point_vendor)
				});
                // // Extend bounds with each marker's position
                 // bounds.extend(markerInfo.position);
            });

            // Fit the map to the bounds containing all markers
            // map.fitBounds(bounds);
             
			addYourLocationButton(map);
        }
		
		
	function OpenNavigation()
	{
		try{
			if(window.current_latitude != 0 && window.current_longitude != 0) {
				var fromAddress = window.current_latitude+","+window.current_longitude;
				var toAddress = charger_latitude+","+charger_longitude;
				// alert(fromAddress+" -- "+toAddress);
				app.openNavigation(fromAddress, toAddress);
			}
		}catch(err) {}
	}	
	
	function addYourLocationButton(map) 
	{
	var controlDiv = document.createElement('div');
	var controlDiv2 = document.createElement('div');
	var controlDiv3 = document.createElement('div');
	
	var firstChild = document.createElement('button');
	firstChild.style.backgroundColor = '#fff';
	firstChild.style.border = 'none';
	firstChild.style.outline = 'none';
	firstChild.style.width = '32px';
	firstChild.style.height = '32px';
	firstChild.style.borderRadius = '2px';
	firstChild.style.boxShadow = '0 1px 4px rgba(0,0,0,0.3)';
	firstChild.style.cursor = 'pointer';
	firstChild.style.marginRight = '10px';
	<?php
	if(isset($_SESSION['IS_APP']))    
	{ 
		?>
		firstChild.style.marginBottom = '110px';
		<?php
	} 
	?>
	
	firstChild.style.padding = '0px';
	firstChild.title = 'Your Location';
	controlDiv.appendChild(firstChild);
	
	var secondChild = document.createElement('div');
	secondChild.style.margin = '5px';
	secondChild.style.width = '18px';
	secondChild.style.height = '18px';
	secondChild.style.backgroundImage = 'url(https://maps.gstatic.com/tactile/mylocation/mylocation-sprite-1x.png)';
	secondChild.style.backgroundSize = '180px 18px';
	secondChild.style.backgroundPosition = '1px 1px';
	secondChild.style.backgroundRepeat = 'no-repeat';
	secondChild.id = 'you_location_img';
	firstChild.appendChild(secondChild);
	
	google.maps.event.addListener(map, 'dragend', function() {
		$('#you_location_img').css('background-position', '0px 0px');
	});

	firstChild.addEventListener('click', function() {
		var imgX = '0';
		var animationInterval = setInterval(function(){
			if(imgX == '-18') imgX = '0';
			else imgX = '-18';
			$('#you_location_img').css('background-position', imgX+'px 0px');
		}, 500);
		 // alert(window.current_latitude + ' - ' +  window.current_longitude);
		if(window.current_latitude != 0 && window.current_longitude != 0) {
			 var latlng = new google.maps.LatLng(window.current_latitude, window.current_longitude);
				 
			map.setCenter(latlng);
			clearInterval(animationInterval);
			$('#you_location_img').css('background-position', '-144px 0px'); 
		}
		else{
			clearInterval(animationInterval);
			$('#you_location_img').css('background-position', '0px 0px');
		}
	});
	
	
	var thirdChild = document.createElement('button');
	thirdChild.style.backgroundColor = '#fff';
	thirdChild.style.border = 'none';
	thirdChild.style.outline = 'none';
	thirdChild.style.width = '32px';
	thirdChild.style.height = '32px';
	thirdChild.style.borderRadius = '2px';
	thirdChild.style.boxShadow = '0 1px 4px rgba(0,0,0,0.3)';
	thirdChild.style.cursor = 'pointer';
	thirdChild.style.marginRight = '10px'; 
	thirdChild.style.marginBottom = '2px'; 
	thirdChild.style.padding = '0px';
	thirdChild.title = 'Zoom In'; 
	controlDiv3.appendChild(thirdChild);
	var fourthChild = document.createElement('i');
	fourthChild.className  = 'fa fa-plus'; 
	thirdChild.appendChild(fourthChild);
	
	thirdChild.addEventListener('click', function() {
		zoomLevel = map.getZoom();
		 zoomLevel++;
		 if(zoomLevel <= 24)
		 {
			 map.setZoom(zoomLevel);
		 }
	});
	
	
	
	
	var fifthChild = document.createElement('button');
	fifthChild.style.backgroundColor = '#fff';
	fifthChild.style.border = 'none';
	fifthChild.style.outline = 'none';
	fifthChild.style.width = '32px';
	fifthChild.style.height = '32px';
	fifthChild.style.borderRadius = '2px';
	fifthChild.style.boxShadow = '0 1px 4px rgba(0,0,0,0.3)';
	fifthChild.style.cursor = 'pointer';
	fifthChild.style.marginRight = '10px'; 
	fifthChild.style.marginBottom = '10px'; 
	fifthChild.style.padding = '0px';
	fifthChild.title = 'Zoom Out'; 
	controlDiv2.appendChild(fifthChild);
	var sixthChild = document.createElement('i');
	sixthChild.className  = 'fa fa-minus'; 
	fifthChild.appendChild(sixthChild);
	
	
	fifthChild.addEventListener('click', function() {
		zoomLevel = map.getZoom();
		 zoomLevel--;
		 if(zoomLevel >= 0)
		 {
			 map.setZoom(zoomLevel);
		 }
	});
	
	
	controlDiv.index = 1;
	map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv);
	map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv2);
	map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(controlDiv3);
}
		
    </script>
	
