<?php

namespace App\Helpers;


class ApiUtils
{
	function api_call($input)
	{

	  $curl = curl_init();

	  $text = urlencode( trim( $input ) );

	  curl_setopt_array($curl, array(
	    CURLOPT_URL => "https://alopeyk.parsimap.com/comapi.svc/FindAddressLocation/10511133/".$text."/ALo475W-43FG6cv7-OPw230-kmA88q/11",
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_FOLLOWLOCATION => true,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "GET",
	    CURLOPT_HTTPHEADER => array(
	      "Content-Type: application/json",
	      "X-Requested-With: XMLHttpRequest"
	    ),
	  ));

	  $response = curl_exec($curl);
	  curl_close($curl);
	  return $response;
	}
}