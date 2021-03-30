<?php


class IATACodes
{

    var $iatageo = "http://iatageo.com/getCode/";
    var $table   = 'vexsol_iatacodes';

    var $codes = array(
        'AQP' => array('code' => 'AQP', 'name' => 'Arequipa', 'country' => 'PE', 'country_name' => 'PERU'),
        'LIM' => array('code' => 'LIM', 'name' => 'Lima', 'country' => 'PE', 'country_name' => 'PERU'),
        'PIU' => array('code' => 'PIU', 'name' => 'Piura', 'country' => 'PE', 'country_name' => 'PERU', 'timezone' => '-5'),
        'TRU' => array('code' => 'TRU', 'name' => 'Trujillo', 'country' => 'PE', 'country_name' => 'PERU', 'timezone' => '-5'),


    );


    public function findCodeByCity($country, $city)
    {
        global $db;


        if($country && $city)
        {
            $sql= "SELECT code, cityCode, cityName FROM ".$this->table." WHERE countryName = :country AND cityName LIKE (:city) GROUP BY CODE,cityCode,cityName";
            $sql = $db->bindVars($sql, ':country', $country, 'string');
            $sql = $db->bindVars($sql, ':city'   , "%$city%", 'string');

        }elseif($city)
        {
            $sql= "SELECT code, cityCode, cityName FROM ".$this->table." WHERE cityName LIKE (:city) GROUP BY CODE,cityCode,cityName";
            $sql = $db->bindVars($sql, ':city'   , "%$city%", 'string');
        }

         $rscodes = $db->Execute($sql);

        if ($rscodes->RecordCount() == 0) return array();


        $codes = array();
        while (!$rscodes->EOF) {
            $code    = $rscodes->fields['code'];
            $codes[$code] = $code;
            $rscodes->MoveNext();
        }


        return $codes;

    }


    /**
     * @param $lat
     * @param $lng
     */
    function getByLatLng ( $lat="", $lng=""){
        global $messageStack;

        if (empty(trim($lat)) || empty(trim($lng)))  return false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->iatageo."$lat/$lng");
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 9);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, empty($_SERVER['HTTP_USER_AGENT']) ? HTTP_CATALOG_SERVER . DIR_WS_CATALOG : $_SERVER['HTTP_USER_AGENT']);
        $response   = curl_exec($ch);
        $error      = curl_error($ch);
        $errno      = curl_errno($ch);
        if ($errno > 0) {
            if (is_object($messageStack)) $messageStack->add_session('header', 'cURL communication ERROR: ' . $error, 'error');
            return $error;
        }


        return json_decode($response, true);

    }




}




?>