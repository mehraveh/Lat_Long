<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\simplexlsx\src\SimpleXLSX;
use App\Models\Address;

class NLPController extends Controller
{

     public function edit_distance($word)
    {
        $xlsx = SimpleXLSX::parse( 'names.xlsx' );
        $min = 10000;
        $index = 0;
        for($x = 0; $x < count(array_column($xlsx->rows(),0)); $x++)
        {                                                
            $lev = levenshtein($word, array_column($xlsx->rows(),0)[$x]);
            if ($lev < $min)
            {
                $min= $lev;
                $index = $x;
            }
        }
        return $index;

    }

    public function tokenize($text)
    {
        $tokens = [];
        $token = strtok($text, " "); 
        echo $text;   
        while ($token !== false)
           {
               $tokens[] = [$token];
               echo $token;
               $token = strtok(" ");
           }
        return $tokens;
    }


    public function is_address($line)
    {
        echo "Sssssssssssssss" .$line;
        $num = 0;
        if ( $xlsx = SimpleXLSX::parse('/home/mehriimm/lat_lng/storage/names.xlsx') )
        {  
          echo "yess"; 
        } 
        else
         {                  
            echo SimpleXLSX::parseError(); 
         }
        $tokens = $this->tokenize($line);

        for($x = 0; $x < count(array_column($xlsx->rows(),0)); $x++)
        {
            if (in_array($tokens[$x], $tokens)) 
            {
                $num += 1;
            }
        }
        if($num >= 2)
        {
            return TRUE;
        }
        return FALSE;
    }

}
