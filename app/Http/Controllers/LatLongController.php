<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LatLongController extends Controller
{
	public function __construct(APIController $api_controller)
	{
		$this->api_controller = $api_controller;
	}

    public function get_lat_lng()
    {
    	$fh = fopen(storage_path('addresses.txt'), "r");

		$i = 0;

		$found_addresses = [];
		$notfound_addresses = [];

		if ( $fh ) 
		{
		  while ( !feof($fh) ) 
		  {
		    $line = fgets($fh);


		        $result = $this->api_controller->api_call($line);

		        $result = json_decode($result);
		        if($result->num > 0)
		        {
		          $found_addresses[] = [
		            "lat" => $result->result[0]->start_location->lat, 
		            "lng" => $result->result[0]->start_location->lng, 
		            "address" => trim($line), 
		          ];
		        }
		        else
		        {
		          $notfound_addresses[] = [
		            "lat" => "",
		            "lng" => "",
		            "address" => trim($line), 
		          ];
		        }
		      // }

		      $i++;
		  }

		  fclose($fh);
		}

		return ($found_addresses);
	}
}
