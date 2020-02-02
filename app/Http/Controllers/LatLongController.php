<?php

namespace App\Http\Controllers;
use App\Http\Controllers\NLPController;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Jobs\GetLatLng;
use Carbon\Carbon;

class LatLongController extends Controller
{
	public function __construct()
	{
	}

    public function get_lat_lng()
    {
    	$api_controller = new APIController;
    	$nlp_controller = new NLPController;
    	$fh = fopen(storage_path('addresses_t.txt'), "r");

		$i = 0;


		if ( $fh ) 
		{
		  while ( !feof($fh) ) 
		  {
		    $line = fgets($fh);
              GetLatLng::dispatchNow($line, $api_controller, $nlp_controller);
		      $i++;
		  }

		  fclose($fh);
		}

	}
}
