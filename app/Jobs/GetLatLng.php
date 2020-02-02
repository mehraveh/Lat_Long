<?php

namespace App\Jobs;
use App\Http\Controllers\APIController;
use App\Http\Controllers\NLPController;
use App\Models\Address;
use App\simplexlsx\src\SimpleXLSX;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetLatLng implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct($line, APIController $api_controller, NLPController $nlp_controller)
    {
        $this->line = $line;
        $this->api_controller = $api_controller;
        $this->nlp_controller = $nlp_controller;
    }


    public function failed($exception)
    {
        echo $exception->getMessage();
    }



    public function pre_process()
      {

        $this->line = str_replace(':','', $this->line);
        $this->line = str_replace('آدرس','', $this->line);
        $this->line = str_replace('خ ','خیابان ', $this->line);
        $this->line = preg_replace('/خ[0-9]/','خیابان ', $this->line);
        //$this->line = preg_replace('/پ[0-9]/','پلاک ', $this->line);
        //$this->line = preg_replace('/ک[0-9]/','کوچه ', $this->line);
        $this->line = preg_replace('/ط[0-9]/','طبقه ', $this->line);
        $this->line = preg_replace('/ز[0-9]/','زنگ ', $this->line);
        $this->line = preg_replace('!طبقه.*$!', '',$this->line);
        $this->line = preg_replace('!واحد.*$!', '',$this->line);
        $this->line = preg_replace('!زنگ.*$!', '',$this->line);
        $this->line = preg_replace('!واحد.*$!', '',$this->line);

        $this->line = str_replace('/','', $this->line);
        $this->line = str_replace("\\",'', $this->line);
        $this->line = str_replace('خونه','خانه', $this->line);

        $this->line = str_replace('۱','1', $this->line);
        $this->line = str_replace('۲','2', $this->line);
        $this->line = str_replace('۳','3', $this->line);
        $this->line = str_replace('۴','4', $this->line);
        $this->line = str_replace('۵','5', $this->line);
        $this->line = str_replace('۶','6', $this->line);
        $this->line = str_replace('۷','7', $this->line);
        $this->line = str_replace('۸','8', $this->line);
        $this->line = str_replace('۹','9', $this->line);
        $this->line = str_replace('۰','0', $this->line);

        $this->line = str_replace(',',' , ', $this->line);
        $this->line = str_replace('،',' ، ', $this->line);
        $this->line = str_replace('-',' ، ', $this->line);

        $this->line = preg_replace('/[a-z]/',' ', $this->line);
        $this->line = preg_replace('/[A-Z]/',' ', $this->line);
        $this->line = preg_replace("/\([^)]+\)/","",$this->line);
      }

    public function handle()
    {   
        $this->pre_process();
        
        $result = $this->api_controller->api_call($this->line);
                $result = json_decode($result);
                if(!$result){
                    echo $this->line;
/*                    $line_t = $this->line;
                    echo  $line_t . '<hr><br>';
                    if( $this->nlp_controller->is_address($line_t) == FALSE)
                    {
                        echo "its not address!!!";
                    }*/
                    return [
                    "lat" => "",
                    "lng" => "",
                    "address" => trim($this->line), 
                  ];
                }
                if($result->num > 0)
                {
                    $address = Address::where('lat_long', $result->result[0]->start_location->lat . $result->result[0]->start_location->lng)->first();
                    if (!$address)
                    {
                        $address = new Address;
                        $address->text_address = $this->line;
                        $address->lat = $result->result[0]->start_location->lat;
                        $address->long = $result->result[0]->start_location->lng;
                        $address->lat_long = $result->result[0]->start_location->lat . $result->result[0]->start_location->lng;
                        $address->save();
                    }
                    return [
                    "lat" => $result->result[0]->start_location->lat, 
                    "lng" => $result->result[0]->start_location->lng, 
                    "address" => trim($this->line), 
                  ];

                }

                else
                {
                  echo $this->line . '<hr><br>';
                  return [
                    "lat" => "",
                    "lng" => "",
                    "address" => trim($this->line), 
                  ];
                }

    }
}
