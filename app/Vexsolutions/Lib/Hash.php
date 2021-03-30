<?php


namespace app\Library\lib;

use Illuminate\Support\Facades\Config;
use Hashids\Hashids;

class Hash {


    private $hash;

    public function  __construct(){
        $this->hash = new Hashids(Config::get('salt'), 20);

    }

    public function encode($string){

        $encoded    = false;
        try
        {
            $encoded = $this->hash->encode($string);

        }catch (Exception $e)
        {

        }
        return $encoded;

    }


    public  function decode($string){
        $result     = "";
        try
        {
            $decoded = $this->hash->decode($string);
            if( is_array($decoded) && isset($decoded[0])){
                $result  = $decoded[0];
            }


        }catch (Exception $e)
        {

        }

        return $result;


    }
} 