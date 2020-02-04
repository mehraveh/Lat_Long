<?php

namespace App\Jobs;
use App\Http\Controllers\APIController;
use App\Http\Controllers\NLPController;
use App\Helpers\ApiUtils;
use App\Helpers\TextProcessingUtils;
use App\Models\Address;
use App\Models\Keyword;
use App\simplexlsx\src\SimpleXLSX;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetLatLng implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct($line, ApiUtils $api, TextProcessingUtils $text_process)
    {
        $this->line = $line;
        $this->api = $api;
        $this->text_process = $text_process;
    }


    public function failed($exception)
    {
        echo $exception->getMessage();
    }


    public function pre_process()
    {
        $text_processor = new TextProcessingUtils;
        $this->line = $text_processor->change_numbers($this->line);
        $this->line = $text_processor->convert_words($this->line);
        $this->line = $text_processor->delete_unnecessary_words($this->line);
        $this->line = $text_processor->delete_engish_words($this->line);

      //if($text_processor->is_address($this->line))
        {
            $this->line = $text_processor->delete_before_colon($this->line);
            $this->line = $text_processor->delete_delimiters($this->line); 
            $this->line = $text_processor->delete_slash($this->line);
            $this->line = $text_processor->delete_inside_prantheses($this->line);  
            $this->line = $text_processor->delete_prantheses($this->line);  

        }
/*       else
        {
            echo  " <h1> not address  </h1> <br>";
        }*/
    }


    public function handle()
    {   
        $this->pre_process();
        $result = $this->api->api_call($this->line);
                $result = json_decode($result);
                if(!$result){
                   // echo "** ".$this->line.' **<hr><br>';
                    return [
                    "lat" => "",
                    "lng" => "",
                    "address" => trim($this->line), 
                  ];
                }
                if($result->num > 0)
                {   
                    $words = explode(" ", $this->line);
                    foreach($words as $word)
                    {
                       $keyword = Keyword::where('word', $word)->first();

                       if(!$keyword)
                       {
                            $keyword = new Keyword;
                            $keyword->word = $word;
                            $keyword->repeted = 1;
                            $keyword->save();
                       }
                       else
                       {
                            $keyword->repeted = $keyword->repeted + 1;
                            $keyword->save();
                       }
                    }

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
                 // echo "** ".$this->line.' **<hr><br>';
                  return [
                    "lat" => "",
                    "lng" => "",
                    "address" => trim($this->line), 
                        ];
                }

    }
}
