<?php

namespace App\Http\Controllers;
use App\Helpers\ApiUtils;
use App\Helpers\TextProcessingUtils;
use App\simplexlsx\src\SimpleXLSX;
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
    	$api = new ApiUtils;
    	$nlp = new TextProcessingUtils;
    	$fh = fopen(storage_path('addresses_t.txt'), "r");
        $xlsx = SimpleXLSX::parse('/home/mehriimm/lat_lng/storage/آدرس. راتین.xlsx');

        for($x = 1; $x < 160; $x++)
		{
		    $line = array_column($xlsx->rows(),0)[$x];
	        GetLatLng::dispatchNow($line, $api, $nlp);
		}


	}
}
