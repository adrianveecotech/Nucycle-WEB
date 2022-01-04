<?php
	if(isset($_POST['action']) && $_POST['action'] == 'get_coordinates')
	{
   		$address = $_POST['address'];
		$key = $_POST['gakey'];
		$address = str_replace(' ','+',$address);
		$geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&key='. $key );
		$output= json_decode($geocode);
		$latitude = $output->results[0]->geometry->location->lat;
		$longitude = $output->results[0]->geometry->location->lng;
		echo json_encode($latitude.','.$longitude);
	}
?>