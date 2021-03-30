<?php

namespace App\Http\Controllers\Glovo;

use App\Models\EMVexglovoorders;
use App\Models\EMVexsetting;
use Carbon\Carbon;
use Escom\Base\CBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Exception;

use Vexsolutions\Glovo\Api;
use Vexsolutions\Glovo\Model\GlovoOrder;
use Vexsolutions\Glovo\Model\GlovoAddress;


class GlovoController extends CBase
{
    //
    protected $emsettings = null;


    public function __construct(EMVexsetting $emsettings)
    {
        $this->emsettings = $emsettings;
    }


    /**
     * Vex soluciones API Glovo for estimate cost order
     * @param $order
     * @param $origin
     * @param $destination
     * @return array
     * @throws Exception
     */
    public function estimateOrderPrice ($order, $origin, $destination){


        $result   = ['success'=>false];

        try
        {

            $this->debug('GlovoController -> estimateOrderPrice', 2, true);


            //validamos si trae alguna ubicacion de la tienda
            if ( !is_array($origin) )
            {
                $result = array('success'=>false, 'errors'=>array('message'=> "origin is required" ) );
                return $result;
            }


            if (empty($origin['lat']) || empty($origin['lng']))
            {
                $result = array('success'=>false, 'errors'=>array('message'=> "lat and lng is required" ) );
                return $result;
            }



            $glovoOrder = array(
                'description'   => "ORden de prueba",
                'scheduletime'  => null,
                'address'       => [
                    'origin'   => [
                        'lat'               => $origin['lat'],
                        'lng'               => $origin['lng'],
                        'label'             => $origin['label'],
                        'details'           => $origin['details'],
                        'contactphone'      => $origin['contactphone'],
                        'contactperson'     => $origin['contactperson']
                    ],
                    'destination'    => [
                        'lat'               => $destination['lat'],
                        'lng'               => $destination['lng'],
                        'label'             => $destination['label'],
                        'details'           => $destination['details'],
                        'contactphone'      => $destination['contactphone'],
                        'contactperson'     => $destination['contactperson']
                    ]
                ]
            );

            $this->debug("Initializing glovo api", 3, true);
            $this->debug("Key    : ".$this->emsettings->getGlovoApi(), 3, true);
            $this->debug("Secret : ".$this->emsettings->getGlovoSecret(), 3, true);
            $this->debug("Server : ".$this->emsettings->getServer(), 3, true);



            $api = new Api( $this->emsettings->getGlovoApi(), $this->emsettings->getGlovoSecret() );
            $api->sandbox_mode( ($this->emsettings->getServer() == 'Production' ? false : true) );


            $AddressOrigin  = new GlovoAddress( GlovoAddress::TYPE_PICKUP, $origin['lat'], $origin['lng'], $origin['label'], $origin['details'] );
            $AddressOrigin->setContactPhone($origin['contactphone']);
            $AddressOrigin->setContactPerson($origin['contactperson']);

            $AddressDestin  = new GlovoAddress( GlovoAddress::TYPE_DELIVERY, $destination['lat'], $destination['lng'], $destination['label'], $destination['details']);
            $AddressDestin->setContactPhone($destination['contactphone']);
            $AddressDestin->setContactPerson($destination['contactperson']);


            $GlovoOrder = new GlovoOrder();
            $GlovoOrder->setDescription( $order['description'] );
            $GlovoOrder->setAddresses( [$AddressOrigin, $AddressDestin] );

            $this->debug(" Invoking API", 3, true);
            $this->debug(" Request JSON ". json_encode($GlovoOrder), 3, false);


            $estimateorder  = $api->estimateOrderPrice( $GlovoOrder );
            $result         = ['success'=>true, 'order'=> $estimateorder];

        }
        catch (Exception $e)
        {
            $result   = ['success'=>false, "errors"=>['message'=>$e->getMessage()]];
            $this->debug(" An error ocurred: ".$e->getMessage(), 3, true);


        }


        return $result;

    }



    /**
     * Send a order to glovo api
     * @param $order
     * @return array
     */
    function createOrder($order, $origin, $destination){

        $result         = ['success'=>false];
        $scheduleTime   = null;
        $now            = Carbon::now('UTC');


        try
        {

            $this->debug('GlovoController -> createOrder', 2, true);

            #-----------------------------------------------------------------------------------------------------------
            # Verificamos si existe
            #-----------------------------------------------------------------------------------------------------------
            $glovoorder = EMVexglovoorders::findByOrderId($order['order_id']);

            //create local order
            if (is_null($glovoorder))
            {
                $this->debug('order not exist, need create and continue', 3, true);
                $this->debug("Order -> description    : ".$order['description'], 3, true);
                $this->debug("Order -> scheduletime   : ".$order['scheduletime'], 3, true);
                $this->debug("Order -> timezone       : ".$order['timezone'], 3, true);
                $this->debug("Order -> Origin - Label : ".$origin['label'], 3, true);
                $this->debug("Order -> Origin - Deta  : ".$origin['details'], 3, true);
                $this->debug("Order -> Origin - Lat   : ".$origin['lat'], 3, true);
                $this->debug("Order -> Origin - Lng   : ".$origin['lng'], 3, true);

                $this->debug("Order -> Destin - Label : ".$destination['label'], 3, true);
                $this->debug("Order -> Destin - Deta  : ".$destination['details'], 3, true);
                $this->debug("Order -> Destin - Lat   : ".$destination['lat'], 3, true);
                $this->debug("Order -> Destin - Lng   : ".$destination['lng'], 3, true);


                $this->debug("Order -> Saving   ", 3, true);


                $glovoorder = new EMVexglovoorders();
                $glovoorder->ORGL_ORDER_ID                      = $order['order_id'];
                $glovoorder->ORGL_DATE                          = $now->format('Y-m-d H:i:s'); //utc format
                $glovoorder->ORGL_DESCRIPTION                   = $order['description'];
                $glovoorder->ORGL_SCHEDULE_TIME                 = ($order['scheduletime'] ? $order['scheduletime'] : \DB::raw("NULL"));
                $glovoorder->ORGL_DATETIMEZONE                  = $order['timezone']; // store local timezone
                $glovoorder->ORGL_METAS                         = $order['metas'];
                $glovoorder->ORGL_DOMAIN                        = $order['domain'];
                $glovoorder->ORGL_PREPARATION_TIME              = (isset($order['preparationtime']) ? $order['preparationtime'] : \DB::raw("NULL"));
                $glovoorder->ORGL_ADDRESS_ORIGIN_LABEL          = $origin['label'];
                $glovoorder->ORGL_ADDRESS_ORIGIN_DETAILS        = $origin['details'];
                $glovoorder->ORGL_ADDRESS_ORIGIN_LAT            = $origin['lat'];
                $glovoorder->ORGL_ADDRESS_ORIGIN_LNG            = $origin['lng'];
                $glovoorder->ORGL_ADDRESS_ORIGIN_PHONE          = $origin['contactphone'];
                $glovoorder->ORGL_ADDRESS_DESTINATION_LABEL     = $destination['label'];
                $glovoorder->ORGL_ADDRESS_DESTINATION_DETAILS   = $destination['details'];
                $glovoorder->ORGL_ADDRESS_DESTINATION_LAT       = $destination['lat'];
                $glovoorder->ORGL_ADDRESS_DESTINATION_LNG       = $destination['lng'];
                $glovoorder->ORGL_ADDRESS_DESTINATION_PHONE     = $destination['contactphone'];
                $glovoorder->ORGL_ADDRESS_DESTINATION_PERSON    = $destination['contactperson'];

                $glovoorder->ORGL_STATE                         = \DB::raw("NULL");
                $glovoorder->ORGL_STATUS                        = 'PENDING';
                $glovoorder->save();



            }


            $this->debug("Local Glovo Order ID[{$glovoorder->ORGL_ORDER_ID}]", 3, true);
            $this->debug("Initializing glovo api", 3, true);
            $this->debug("Key    : ".$this->emsettings->getGlovoApi(), 3, true);
            $this->debug("Secret : ".$this->emsettings->getGlovoSecret(), 3, true);
            $this->debug("Server : ".$this->emsettings->getServer(), 3, true);

            $api = new Api( $this->emsettings->getGlovoApi(), $this->emsettings->getGlovoSecret() );
            $api->sandbox_mode( ($this->emsettings->getServer() == 'Production' ? false : true) );

            //origin
            $AddressOrigin  = new GlovoAddress(GlovoAddress::TYPE_PICKUP, $origin['lat'], $origin['lng'], $origin['label'], $origin['details']);
            $AddressOrigin->setContactPhone($origin['contactphone']);

            //destination
            $AddressDestin  = new GlovoAddress(GlovoAddress::TYPE_DELIVERY, $destination['lat'], $destination['lng'], $destination['label'], $destination['details']);
            $AddressDestin->setContactPhone ($destination['contactphone']);
            $AddressDestin->setContactPerson($destination['contactperson']);

            $GlovoOrder     = new GlovoOrder();
            $GlovoOrder->setDescription( $order['description'] );
            $GlovoOrder->setAddresses( [$AddressOrigin, $AddressDestin] );

            #scheduled time
            $scheduleTime   = $order['scheduletime'];
            if ( !is_null($scheduleTime))
            {
                $this->debug("Have setScheduleTime ", 3, true);
                $this->debug("setScheduleTime      : ". $scheduleTime, 3, true);
                $GlovoOrder->setScheduleTime( $scheduleTime );
            }

            $this->debug(" Invoking API", 3, true);
            $this->debug(" Request JSON ". json_encode($GlovoOrder), 3, false);

            //throw new Exception("Test Error", 3000);
            #-----------------------------------------------------------------------------------------------------------
            # Creating order
            #-----------------------------------------------------------------------------------------------------------
            $NewGlovoOrder = $api->createOrder( $GlovoOrder );
            //$NewGlovoOrder  = ['id'=>54821147, 'state'=>'SCHEDULED'];


            $this->debug(" Order created :". print_r($NewGlovoOrder, true), 3, true);
            $this->debug(" Updating local order", 3, true);

            $update = EMVexglovoorders::where('ORGL_ORDER_ID', $order['order_id'])->update(['ORGL_GLOVO_ID'=>@$NewGlovoOrder['id'], 'ORGL_STATE'=>@$NewGlovoOrder['state'], 'ORGL_STATUS'=>'COMPLETED']);
            $this->debug(" API Glovo Returning success ", 3, true);


            return ['success'=>true, 'order'=> @$NewGlovoOrder, 'emglovoorder'=>$glovoorder ];


        }
        catch (Exception $e)
        {
            $result   = ['success'=>false, 'emglovoorder'=>$glovoorder, "errors"=>['message'=>$e->getMessage()]];
            $update = EMVexglovoorders::where('ORGL_ORDER_ID', $order['order_id'])->update(['ORGL_STATUS'=>'FAILED', 'ORGL_MESSAGE'=>$e->getMessage()]);
            $this->debug(" An error ocurred: ".$e->getMessage(), 3, true);
            Log::critical('Error in GlovoController@createOrder', [$e]);

        }

        return $result;

    }


    /**
     * Retrieve information about a single order.
     * @param $order_id
     * @return array
     */
    public function getOrder($order_id){

        try
        {

            //local order
            $glovoorder = EMVexglovoorders::findByOrderId($order_id);
            if ( is_null($glovoorder) )
            {
                return array('success'=>false, "errors"=>array('message'=>'Invalid glovo order id'));
            }

            # no order id glovo
            if (empty($glovoorder->getGlovoOrderId()))
            {
                return array('success'=>false, "errors"=>array('message'=>'No glovo order id yet'));
            }


            $api = new Api( $this->emsettings->getGlovoApi(), $this->emsettings->getGlovoSecret() );
            $api->sandbox_mode( ($this->emsettings->getServer() == 'Production' ? false : true) );


            $glovoOrder = $api->retrieveOrder($glovoorder->getGlovoOrderId());
            $result     = array('success'=>true, "order"=>$glovoOrder);

        }catch (Exception $e)
        {
            $result     = array('success'=>false, "errors"=>array('message'=>$e->getMessage()));

        }

        return $result;

    }


    /**
     * Return the position (latitude, longitude) of the courier.
     * @param $order_id
     * @return array
     */
    function getOrderTracking($order_id){
        $result   = ['success'=>false];

        try
        {

            //local order
            $glovoorder = EMVexglovoorders::findByOrderId($order_id);


            if ( is_null($glovoorder) )
            {
                return array('success'=>false, "errors"=>array('message'=>'Invalid glovo order id'));
            }

            # no order id glovo
            if (empty($glovoorder->getGlovoOrderId()))
            {
                return array('success'=>false, "errors"=>array('message'=>'No glovo order id yet'));
            }


            $api = new Api( $this->emsettings->getGlovoApi(), $this->emsettings->getGlovoSecret() );
            $api->sandbox_mode( ($this->emsettings->getServer() == 'Production' ? false : true) );



            $tracking   = $api->getOrderTracking( $glovoorder->getGlovoOrderId() );


            #if ( is_null($tracking['lat']) || is_null($tracking['lon']))
            #{
            #    array('success'=>true, 'tracking'=> array('lat'=>-12.0537952, 'lng'=>-77.0547276 ) );
            #    //return array('success'=>false, "errors"=>array('message'=>'Not found' ));
            #}

            $result     = array('success'=>true, 'tracking'=> array('lat'=>$tracking['lat'], 'lng'=>$tracking['lon'] ) );
            return $result;

        }
        catch (Exception $e)
        {
            $result   = ['success'=>false, "errors"=>['message'=>$e->getMessage()]];

        }


        return $result;

    }

    /**
     * Get glovo carrier
     * @return array
     */
    public function getCourierContact($order_id, $orderglovo){
        $result   = ['success'=>false];

        try
        {

            if (is_null($orderglovo)){
                return array('success'=>false, "errors"=>array('message'=>'No glovo order found' ));
            }

            if (empty($orderglovo->getGlovoOrderId())){
                return array('success'=>false, "errors"=>array('message'=>'No glovo order found' ));
            }

            $api = new Api( $this->emsettings->getGlovoApi(), $this->emsettings->getGlovoSecret() );
            $api->sandbox_mode( ($this->emsettings->getServer() == 'Production' ? false : true) );


            $courier   = $api->getCourierContact( $orderglovo->getGlovoOrderId() );

            if ( is_null($courier))
            {
                return array('success'=>false, "errors"=>array('message'=>'Not found' ));
            }

            $result     = array('success'=>true, 'courier'=> $courier );
            return $result;

        }
        catch (Exception $e)
        {
            $result   = ['success'=>false, "errors"=>['message'=>$e->getMessage()]];

        }


        return $result;
    }




    /**
     * @param $order
     * @param $origen
     * @param $destination
     * @return array
     */
    public function TestService($data){

        $result   = ['success'=>false];

        try
        {

            $api = new Api( $this->emsettings->getGlovoApi(), $this->emsettings->getGlovoSecret() );
            $api->sandbox_mode( ($this->emsettings->getServer() == 'Production' ? false : true) );


            $AddressOrigin  = new GlovoAddress( GlovoAddress::TYPE_PICKUP    , $data['address']['origin']['lat']     , $data['address']['origin']['lng']     , $data['address']['origin']['label']      , $data['address']['origin']['details'] );
            $AddressDestin  = new GlovoAddress( GlovoAddress::TYPE_DELIVERY  , $data['address']['destination']['lat'], $data['address']['destination']['lng'], $data['address']['destination']['label'] , $data['address']['destination']['details']);
            $GlovoOrder     = new GlovoOrder();
            $GlovoOrder->setDescription( $data['description'] );
            $GlovoOrder->setAddresses( [$AddressOrigin, $AddressDestin] );

            $estimateorder  = $api->estimateOrderPrice( $GlovoOrder );

            $result         = ['success'=>true, 'message' =>"Successfully connected to the glovo API" ,  'order'=> $estimateorder, 'requestaddress'=>$data];


            return $result;
        }
        catch (Exception $e)
        {
            $result   = ['success'=>false, "errors"=>['message'=>$e->getMessage(), 'code'=>'520']];

        }


        return $result;
    }


    /**
     * Syncroniza con glovo api order (local - api glovo)
     * @return array
     */
    public function synOrder($orderId){

        $apiglovoorder  = $this->getOrder($orderId);
        if ($apiglovoorder['success']==true)
        {
            //local order
            $glovoorder = EMVexglovoorders::findByOrderId($orderId);
            $glovoorder->ORGL_STATE = $apiglovoorder['order']['state'];
            $glovoorder->save();

        }

    }




    /**
     * @param $address
     * @param null $b
     * @return \StdClass
     */
    public function geocode($address, $b=null) {

        $response=new \StdClass;
        $response->street_address               = null;
        $response->route                        = null;
        $response->country                     = null;
        $response->administrative_area_level_1 = null;
        $response->administrative_area_level_2 = null;
        $response->administrative_area_level_3 = null;
        $response->locality                    = null;
        $response->sublocality                 = null;
        $response->neighborhood                = null;
        $response->postal_code                 = null;
        $response->formatted_address           = null;
        $response->latitude                    = null;
        $response->longitude                   = null;
        $response->status                      = 'ERROR';



        $params=array('sensor'=>'false', 'key'   => $this->emsettings->getGoogleApiKey());

        if(is_null($b))
        {
            $params['address']= $address;
        }
        else
        {
            $params['latlng']=implode(',',array($address,$b));
        }

        $url = 'https://maps.google.com/maps/api/geocode/json?'.http_build_query($params,'','&');
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?'.http_build_query($params,'','&');
        //$result= @file_get_contents($url);

        /*
         * Builds the URL and request to the Google Maps API
         */
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode( $address ).'&key='.$this->emsettings->getGoogleApiKey();

        /*
         * Creates a Guzzle Client to make the Google Maps request.
         */
        $client = new \GuzzleHttp\Client();

        /*
         * Send a GET request to the Google Maps API and get the body of the
         * response.
         */
        $geocodeResponse = $client->get( $url )->getBody();
        $json            = @json_decode($geocodeResponse);

        Log::critical('geocode', [$geocodeResponse]);

        if($json)
        {

            $response->status = $json->status;
            if($response->status=='OK')
            {
                $response->formatted_address=$json->results[0]->formatted_address;
                $response->latitude     =$json->results[0]->geometry->location->lat;
                $response->longitude    =$json->results[0]->geometry->location->lng;

                foreach($json->results[0]->address_components as $value)
                {
                    if(array_key_exists($value->types[0],$response))
                    {
                        $response->{$value->types[0]}=$value->long_name;
                    }
                }
            }
        }
        return $response;
    }
}
