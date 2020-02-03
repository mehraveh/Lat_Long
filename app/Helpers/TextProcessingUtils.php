<?php

namespace App\Helpers;
use App\simplexlsx\src\SimpleXLSX;


class TextProcessingUtils
{

	public function change_numbers($text)
    {
        $text = strtr($text, array('۱' => '1','۲' => '2','۳' => "3",'۴'=>"4",'۵'=>"5",'۶'=>"6",'۷'=>"7",'۸'=>"8",'۹'=>"9",'۰'=>"0"));
        return $text;
    }


    public function delete_unnecessary_words($text)
    {
        $text = preg_replace('!طبقه.*$!', ' ',$text);
        $text = preg_replace('!واحد.*$!', ' ',$text);
        $text = preg_replace('!زنگ.*$!', ' ',$text);
        $text = preg_replace('!کدپستی.*$!', ' ',$text);
        //$text = preg_replace('!آدرس.*$!', ' ',$text);
        return $text;
    }


    public function convert_words($text)
    {
        $text = str_replace('خ ','خیابان ', $text);
        $text = str_replace('خ.','خیابان ', $text);
        $text = preg_replace('/ط(\d)+/','طبقه ', $text);
        $text = str_replace(' ط ','طبقه ', $text);
        //$text = preg_replace('/پ(\d)+/','پلاک ', $text);
        $text = preg_replace('/ز[0-9]/','زنگ ', $text);
        return $text;
    }


    public function delete_before_colon($text)
    {
        $t = strstr($text, ':');
        if(! $t)
        {
        	return $text;
        }
        $text = str_replace(':','', $t);
        return $text;

    }
    

    public function delete_engish_words($text)
    {
        $text = preg_replace('/[a-z]/',' ', $text);
        $text = preg_replace('/[A-Z]/',' ', $text);
        return $text;	
    }


    public function delete_delimiters($text)
    {
    	$text = str_replace(',', ' ', $text);
    	$text = str_replace('،', ' ', $text);
    	$text = str_replace('-', ' ', $text);
    	//strtr($text, array(',' => ' ', '،' => ' ', '-' => ' '));
        return $text;
    }


    public function delete_slash($text)
    {
    	$text = str_replace('/','', $text);
        $text = str_replace("\\",'', $text);
        return $text;
    }


    public function delete_inside_prantheses($text)
    {
    	
    	$text = preg_replace("/\([^)]+\)/","",$text);
    	return $text;
    }


    public function delete_prantheses($text)
    {
    	
    	$text = str_replace('(',' ', $text);
    	return $text;
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
        $num = 0;
        if ( $xlsx = SimpleXLSX::parse('/home/mehriimm/lat_lng/storage/names.xlsx') )
        {  
        } 
        else
         {                  
            return SimpleXLSX::parseError(); 
         }

        for($x = 0; $x < count(array_column($xlsx->rows(),0)); $x++)
        {
            if (strpos($line, array_column($xlsx->rows(),0)[$x])) 
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