<?php

namespace App\Http\Controllers\Shopify;



use App\Mail\OrderFailed;
use App\Mail\OrderTracking;
use App\Models\Orders\EMVexorders;
use Utils\Dates;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Pagination\LengthAwarePaginator;

use Log;
use Exception;
use DateTime;
use DateTimeZone;
use Carbon\Carbon;


use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use OhMyBrew\ShopifyApp\Models\Shop;
use OhMyBrew\ShopifyApp\Services\ShopSession;


use App\Http\Controllers\Glovo\GlovoController;

use App\Models\EMVexsetting;
use App\Models\Store\EMVexhollyday;
use App\Models\Store\EMVexhours;
use App\Models\Store\EMVexlocations;
use App\Models\EMVexglovoorders;
use App\Models\EMVexstore;
use Escom\Base\CBase;
use Vexsolutions\Utils\Logger\Facades\BufferLog;


class ShopifyOrdersController extends CBase
{

    /*
     * var $pageSize
     *
     */
    protected $pageSize = 25;



    public function __construct()
    {
        app('translator')->setLocale('es');
        ini_set("memory_limit","1G");
        ini_set("max_execution_time","300");

    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {

        $shop           = ShopifyApp::shop();
        $shopifyshop    = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;


        $paginatedOrders    = $this->getOrders($shop);
        $collection             = collect($paginatedOrders->all());

        $keys           = $collection->pluck('id')->toArray();
        $emglovoorders  = collect(EMVexglovoorders::whereIn('ORGL_ORDER_ID',$keys)->get());

        $emsettings   = EMVexsetting::findByStoreId($shopifyshop->id);
        if ( is_null($emsettings) or ( empty($emsettings->getGlovoApi()) or empty($emsettings->getGlovoSecret()) or empty($emsettings->getGoogleApiKey())))
        {
            return Redirect::route('shopify.settings');

        }
        $emstore    = EMVexstore::findByStoreId($shopifyshop->id);

        app('translator')->setLocale($emsettings->getLanguage());

        return view('shopify.orders.index')
            ->with('emstore'        , $emstore)
            ->with('orders'         , $paginatedOrders)
            ->with('emglovoorders'  , $emglovoorders);

    }


    /**
     * Orden details
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($orderid){

        $quantity   = 0;
        $route      = env('SHOPIFY_APP_URL')."/orders/glovo/create";
        $routeresend= env('SHOPIFY_APP_URL')."/orders/glovo/resend";

        $shop       = ShopifyApp::shop();
        $restshop           = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json');

        //order details
        $restOrder      = $shop->api()->rest('GET', "/admin/api/".Config('shopify-app.api_version')."/orders/{$orderid}.json");
        if ($restOrder->errors == true)
        {
            return Redirect::route('shopify.orders.index')->with("errors", "The order not found");
        }


        #the order
        $shopifyorder   = $restOrder->body->order;

        $tempProductList    =[];
        $line_items         =[];


        foreach ($shopifyorder->line_items as $item){

            if ( $item->product_id )
            {
                $apiproductmetas = $shop->api()->rest('GET',"/admin/api/".Config('shopify-app.api_version')."/products/{$item->product_id}/metafields.json");

                $metas           = collect( $apiproductmetas->body->metafields )->keyBy('key');
                $item->metas     = $metas;

                //only glovo products have service
                if ( $metas->has('available_for_glovo') and $metas->get('available_for_glovo')->value =='true')
                {
                    $tempProductList[] = $item->product_id;//$item->product_id;
                    $quantity         += $item->quantity;
                }

                $line_items[]          = $item;

            }

        }


        //adjusting
        $shopifyorder->quantity     = $quantity;
        $shopifyorder->line_items   = $line_items; unset($line_items);

        //products
        $restProducts    = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/products.json", ['ids'=>implode(',', $tempProductList)]);
        $shopifyproducts = collect( $restProducts->body->products )->keyBy('id');


        //metas
        $apimetas           = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/orders/{$orderid}/metafields.json");
        $shopifyordermetas  = collect( $apimetas->body->metafields )->keyBy('key');


        //locations
        $reqLocations   = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/locations/{$restshop->body->shop->primary_location_id}.json");
        $shopifylocation  = $reqLocations->body->location;




        //local settings
        $settings   = EMVexsetting::findByStoreId($restshop->body->shop->id);
        $emstore    = EMVexstore::findByStoreId( $restshop->body->shop->id );
        app('translator')->setLocale($settings->getLanguage());
        $cglovo     = new GlovoController($settings);


        //local order
        $emglovoorder = EMVexglovoorders::findByOrderId($orderid);


        //info glovo order
        $glovocurier = null;
        if($emglovoorder){

            $cglovo->synOrder($orderid);
            $emglovoorder->refresh();
            $apicourier = $cglovo->getCourierContact($orderid, $emglovoorder);
            if ($apicourier['success']==true) {
                $emglovoorder->ORGL_CARRIER_NAME     = $apicourier['courier']['courierName'];
                $emglovoorder->ORGL_CARRIER_PHONE    = $apicourier['courier']['phone'];
                $glovocurier = $apicourier['courier'];

            }
        }




        return view('shopify.orders.detail')
            ->with('shopifyorder'       , $shopifyorder)
            ->with('shopifyordermetas'  , $shopifyordermetas)
            ->with('shopifyproducts'    , $shopifyproducts)
            ->with('shopifylocation'    , $shopifylocation)
            ->with('glovoorder'         , $emglovoorder)
            ->with('emstore'            , $emstore)
            ->with('glovocontroller'    , $cglovo)
            ->with('glovocurier'        , $glovocurier)
            ->with('routeresend'        , $routeresend)
            ->with('route'              , $route);


    }


    /***
     * Tracks a especified order
     * @param $data
     * @return mixed
     */
    public function tracking(){

        $baseurl        = env('SHOPIFY_APP_URL');
        $glovocurier    = null;


        //get the parameter code
        $code   = Request::get('code');
        if (empty($code)){
            return view('shopify.tracking.404');
        }

        //try decode
        $decoded    = Crypt::decrypt($code); // -> [domain=> shopify-domain , order=> orderid]
        $params     = @json_decode($decoded);

        if (!$params){
            return view('shopify.tracking.404');
        }

        //emglovo order model
        $emglovoorder   = EMVexglovoorders::findByOrderId($params->order);
        if (is_null($emglovoorder)){
            return view('shopify.tracking.404');
        }

        //store
        $emstore        = EMVexstore::findByDomain($emglovoorder->ORGL_DOMAIN);
        if (is_null($emstore)){
            return view('shopify.tracking.404');
        }

        //setttings
        $emsettings     = EMVexsetting::findByStoreId($emstore->getId());
        app('translator')->setLocale($emsettings->getLanguage());



        //shopify store
        $shop           = Shop::where('shopify_domain', $emstore->getDomain())->first();
        if (is_null($shop)){
            return view('shopify.tracking.404');
        }

        //order details
        $shopifyorder   = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/orders/{$params->order}.json")->body->order;

        //store
        $shopifyshop  = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;


        $tempProductList=[];
        foreach ($shopifyorder->line_items as $item){
            $tempProductList[] = $item->product_id;
        }

        //products
        $restProducts    = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/products.json", ['ids'=>implode(',', $tempProductList)]);
        $shopifyproducts = collect( $restProducts->body->products )->keyBy('id');

        $product = $shopifyproducts->where('id', $shopifyorder->line_items[0]->product_id )->first();
        $image   = ($product ? $product->image : null);


        $cglovo     = new GlovoController($emsettings);
        if($emglovoorder){
            $cglovo->synOrder($shopifyorder->id);
            $emglovoorder->refresh();
            $apicourier = $cglovo->getCourierContact($shopifyorder->id, $emglovoorder);
            if ($apicourier['success']==true)
                $glovocurier = $apicourier['courier'];
        }


        return view('shopify.tracking.track')
            ->with('shopifyorder'       , $shopifyorder)
            ->with('shopifyshop'        , $shopifyshop)
            ->with('shopifyproducts'    , $shopifyproducts)
            ->with('emsettings'         , $emsettings)
            ->with('baseurl'            , $baseurl)
            ->with('glovocurier'        , $glovocurier)
            ->with('glovoorder'         , $emglovoorder);

    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createOrderToGlovoManager(){

        $orderid        = Request::get('order_id');
        $shop           = ShopifyApp::shop();
        try
        {


            if (is_null($shop)){
                throw new Exception('Invalid session', 3000);
            }



            $emstore    = EMVexstore::findByDomain($shop->shopify_domain);
            $emsettings = EMVexsetting::findByStoreId( $emstore->getId());
            app('translator')->setLocale($emsettings->getLanguage());

            //for logs
            $carbonNow  = Carbon::now('America/Mexico_City');
            $fileToLog  = storage_path("logs/shopify/{$shop->shopify_domain}/o/ma/".$carbonNow->format('Y-m-d') .".log");


            BufferLog::Debug("");
            BufferLog::Debug("-------------------------------------------------------------------------------------------");
            BufferLog::Debug('   createOrderToGlovoManager');
            BufferLog::Debug('-------------------------------------------------------------------------------------------');

            $order          = $this->createOrderToGlovo($orderid, $shop, $force=true);
            if ($order['success']== false){
                $this->debug("An error ocurred ", 2, true);
                throw new Exception($order['errors']['message'], 5001);
            }

            BufferLog::Debug('------ End ----- ');
            BufferLog::Debug('');
            BufferLog::LogToFile($fileToLog);

            return Redirect::route('shopify.orders.detail',['id'=>$orderid])->with("success", __('glovo.orderdetail.create.success'));

        }catch(Exception $e)
        {
            $errors = __('glovo.orderdetail.create.fail'). ". <br>\"". $e->getMessage() ."\"";
            BufferLog::Debug('Error ->'.$e->getMessage());
            Log::critical('createOrderToGlovoManager' . $e->getMessage());
        }

        BufferLog::Debug('------ End ----- ');
        BufferLog::Debug('');
        BufferLog::LogToFile($fileToLog);

        return Redirect::route('shopify.orders.detail',['id'=>$orderid])->with("errors", $errors);


    }


    /**
     * Prepar los datos para crear la orden en glovo
     * @param int $orderid - Shopify Order Id
     * @return array
     */
    public function createOrderToGlovo($orderid, $shop, $force=false, $from=null){
        $result         = ['success'=> false];
        $message        = "";
        $quantity       = 0;
        $orderLabel     = "";
        $notes          = "";
        $scheduleTime   = null;

        try
        {
            $this->debug("");
            $this->debug("ShopifyOrdersController@createOrderToGlovo");
            $this->debug("-----------------------------------------------------------------------------------------");
            $this->debug(" Triying to create order $orderid                                                        ");
            $this->debug("-----------------------------------------------------------------------------------------");

            $carbonNow  = Carbon::now('America/Mexico_City');
            $fileToLog  = storage_path("logs/orders/".$carbonNow->format('Y-m-d') .".log");

            $shopifyshop    = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;

            $this->debug( "Store domain          -> ".$shop->shopify_domain, 2);


            #local settings
            $emstore        = EMVexstore::findByDomain( $shop->shopify_domain );
            $emsetting      = EMVexsetting::findByStoreId( $emstore->getId() );
            $ctrlsetting    = new ShopifySettingsController();

            app('translator')->setLocale($emsetting->getLanguage());


            $this->debug("Local settings Id -> ".$emsetting->getId(), 2);
            $this->debug("Local store       -> ".$emsetting->getStoreId(), 2);


            //order details
            $restOrder      = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/orders/{$orderid}.json");
            $shopifyorder   = $restOrder->body->order;


            #-----------------------------------------------------------------------------------------------------------
            # verify status order
            # Determinate if generate glovo order
            #-----------------------------------------------------------------------------------------------------------
            $this->debug("Order   financial_status                      -> ".$shopifyorder->financial_status, 2);
            $this->debug("Order   fulfillment_status                    -> ".$shopifyorder->fulfillment_status, 2);
            $this->debug("Setting order status creating when iqualsTo   -> ".$emsetting->getCreateStatus(), 2);
            $triggerOrder   = false;

            #-----------------------------------------------------------------------------------------------------------
            # CREATING ORDERS
            #-----------------------------------------------------------------------------------------------------------
            $this->debug("Automatic determinate when create order", 2);
            if($emsetting->getCreateStatus() == 'paid'){
                $this->debug("Testing verification on 'paid' ", 2);
                if (  $shopifyorder->financial_status == $emsetting->getCreateStatus()) {
                    $triggerOrder = true;
                    $this->debug("Test passed - order need created with status   -> ".$emsetting->getCreateStatus(), 2);
                }
            }elseif( $emsetting->getCreateStatus() == 'authorized' )
            {   $this->debug("Testing verification on 'authorized' ", 2);
                if (  $shopifyorder->financial_status == $emsetting->getCreateStatus()) {
                    $triggerOrder = true;
                    $this->debug("Test passed - order need created with status   -> ".$emsetting->getCreateStatus(), 2);
                }
            }elseif( $emsetting->getCreateStatus() == 'manual' )
            {   $this->debug("Testing verification on 'manual' ", 2);
                if (  $force == true) {
                    $triggerOrder = true;
                    $this->debug("Test passed - order need created with status   -> ".$emsetting->getCreateStatus(), 2);
                }
            }


            if (!$triggerOrder){
                throw new Exception("The configuration of the order status to generate the glovo order is not yet ready. Required status: \"{$emsetting->getCreateStatus()}\" ", 5010);
            }

            #-----------------------------------------------------------------------------------------------------------
            # Validamos si se puede realizar la orden
            #-----------------------------------------------------------------------------------------------------------
            $this->debug("Validar si se puede generar la orden' ", 2);

            #tiene un plan basico y no puede generar de forma automatica
            if ( is_null($emstore->getCarrierId()) )
            {
                $this->debug("Cuenta con un Plan Basico' ", 2);
                $this->debug("Se permite enviar la orden manualmente' ", 2);

            }
            else
            {
                $this->debug("Cuenta con un Plan Avanzado' ", 2);

                if (!empty($shopifyorder->shipping_lines) and $shopifyorder->shipping_lines[0]->title !== env('SHOPIFY_SHIPPING_TITLE')){
                    throw new Exception("Invalid shipping method. Expected:".env('SHOPIFY_SHIPPING_TITLE'), 5010);
                }
            }


            #add the metas
            $tempProductList = [];
            $line_items      = [];
            $product_list    = []; //same as frontStore
            foreach ($shopifyorder->line_items as $item){

                if (is_null($item->product_id) or empty($item->product_id)) continue;

                $apiproductmetas = $shop->api()->rest('GET',"/admin/api/".Config('shopify-app.api_version')."/products/{$item->product_id}/metafields.json");

                $metas           = collect( $apiproductmetas->body->metafields )->keyBy('key');
                $item->metas     = $metas;
                $product_list[]  = ['product_id'=>$item->product_id, 'variant_id'=>$item->variant_id];


                if ( $emsetting->getEnableAllProducts() )
                {
                    $tempProductList[] = $item->product_id;
                    $quantity         += $item->quantity;
                    $line_items[]      = $item;

                }else
                {
                    //only glovo products have service
                    if ( $metas->has('available_for_glovo') and $metas->get('available_for_glovo')->value =='true')
                    {
                        $tempProductList[] = $item->product_id;
                        $quantity         += $item->quantity;
                    }
                    $line_items[]          = $item;
                }
            }


            //adjusting
            $shopifyorder->quantity     = $quantity;
            $shopifyorder->line_items   = $line_items; unset($line_items);

            $this->debug( "Total products          -> ".$quantity, 2);

            //products
            $restProducts    = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/products.json", ['ids'=>implode(',', $tempProductList)]);
            $shopifyproducts = collect( $restProducts->body->products )->keyBy('id');

            //locations
            $reqLocations           = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/locations/{$shopifyshop->primary_location_id}.json");
            $shopifylocation        = $reqLocations->body->location;


            //metas
            $apimetas           = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/orders/{$orderid}/metafields.json");
            $shopifyordermetas  = collect( $apimetas->body->metafields )->keyBy('key');
            $this->debug( "Metas -> ". $shopifyordermetas->pluck('value','key')->toJson(), 2);



            #-----------------------------------------------------------------------------------------------------------
            # Preparing data for order loop products
            #-----------------------------------------------------------------------------------------------------------
            $orderLabel="Order ". $shopifyorder->name . ". ";
            $tempProductList=[];
            foreach ($shopifyorder->line_items as $item){
                if (is_null($item->product_id) or empty($item->product_id)) continue;
                
                $metas  = $item->metas;

                if ( $emsetting->getEnableAllProducts() )
                {
                    $tempProductList[] = $item->product_id;
                    $quantity         += $item->quantity;
                    $line_items[]      = $item;
                    $orderLabel.=       "(".$item->quantity .") ". $item->name .", ";

                }else
                {
                    //only glovo products have service
                    if ( $metas->has('available_for_glovo') and $metas->get('available_for_glovo')->value =='true')
                    {
                        $product       = $shopifyproducts->where('id', $item->product_id )->first();
                        $variant       = $item->variant_title;
                        $orderLabel.= "(".$item->quantity .") ". $item->name .", ";

                    }
                }
            }


            $orderLabel = rtrim($orderLabel,", \t\n\r\0\x0B");


            #-----------------------------------------------------------------------------------------------------------
            #schedule time
            #-----------------------------------------------------------------------------------------------------------
            if ($shopifyordermetas->has('glovo_when_receive'))
            {
                $when   = $shopifyordermetas->get('glovo_when_receive')->value ;
                if($when == "scheduled")
                {
                    if ($shopifyordermetas->has('glovo_schedule_time'))
                    {
                        $meta           = $shopifyordermetas->get('glovo_schedule_time');
                        $this->debug( "Meta glovo_schedule_time -> ".print_r($meta,true), 2);

                        $carbondatetime = Carbon::createFromFormat('YmdHi', $meta->value, $emstore->getTimeZone());

                        $this->debug( "scheduleTime TimeZone    -> ". $emstore->getTimeZone(), 2, false);
                        $this->debug( "scheduleTime HR Local    -> ". $carbondatetime->format('Y-m-d H:i:s') ."    -  ". $carbondatetime->isoFormat('LLL'), 2, false);
                        $carbondatetime->setTimezone('UTC');

                        $this->debug( "scheduleTime HR UTC      -> ".$carbondatetime->format('Y-m-d H:i:s'), 2, false);
                        $this->debug( "scheduleTime epoch       -> ".$carbondatetime->isoFormat('x'),2, false);

                        $scheduleTime   = (float) $carbondatetime->isoFormat('x');
                        //false or instance DateTime
                        //$scheduleTime   = $this->createFromFormat('Y-m-d H:i:s',  $carbondatetime->format('Y-m-d H:i:s'));

                    }
                }
            }else
            {
                #-----------------------------------------------------------------------------------------------------------
                # Si no trae la etiqueta glovo_when_receive
                #-----------------------------------------------------------------------------------------------------------
                # Buscamos los horarios del dia actual
                $now   = Carbon::now($emstore->getTimeZone());
                $hours = $ctrlsetting->getWorkingTimes($emsetting->getId(), $now->format('Ymd'),$product_list);

                # {"success":false,"hours":[],"immediately":false,"errors":{"message":"service not available"}}
                #si ya no hay servicio para el dia de hoy, buscamos para mañana en el primer horario disponible
                if ($hours['success'] == false)
                {
                    $tries  = 2; $attemp = 1;
                    while  ( $hours['success'] == false and $attemp <=3 )
                    {
                        $this->debug( "The arent senrvice for today -> ".$now->format('Ymd') ."--->".json_encode($hours),3, false);
                        $this->debug( "Check service for tomorrow",3, false);

                        $hours = $ctrlsetting->getWorkingTimes($emsetting->getId(), $now->clone()->addDay()->format('Ymd'),$product_list);
                        $attemp++;
                    }

                    #si encontro disponibilidad para el dia siguiente
                    if ($hours['success'] == true)
                    {
                        $this->debug( "Finded one option",2, false);
                        #encontrar el primero
                        $posibleschedule = $hours['hours'];
                        if( isset($posibleschedule[0]) ){
                            $this->debug( "Option candidate text ".$posibleschedule[0]['text'],3);
                            $this->debug( "Option candidate time ".$posibleschedule[0]['id'],3);

                            $carbondatetime = Carbon::createFromFormat('YmdHi', $posibleschedule[0]['id'], $emstore->getTimeZone());
                            $carbondatetime->setTimezone('UTC');
                            $scheduleTime   = (float) $carbondatetime->isoFormat('x');
                        }

                    }
                }elseif ($hours['success'] == true)
                {
                    $this->debug( "There is service on the store",3, false);
                    $this->debug( "Checkin if can send immediately",3, false);

                    if ( $hours['immediately'] == true ){
                        $this->debug( " Can send immediately ... OK",4, false);
                    }


                    #cant send immediately
                    if ( $hours['immediately'] == false ){
                        $this->debug( "Cant send immediately",3, false);
                        #encontrar el primero
                        $posibleschedule = $hours['hours'];
                        if( isset($posibleschedule[0]) ){
                            $this->debug( "Option candidate text ".$posibleschedule[0]['text'],3);
                            $this->debug( "Option candidate time ".$posibleschedule[0]['id'],3);

                            $carbondatetime = Carbon::createFromFormat('YmdHi', $posibleschedule[0]['id'], $emstore->getTimeZone());
                            $carbondatetime->setTimezone('UTC');
                            $scheduleTime   = (float) $carbondatetime->isoFormat('x');
                        }
                    }
                    #si hay servicio aun en la tienda,se puede enviar de forma inmediata
                    #   - levanta la orden inmediatamente


                }

            }














            #location origin (pickup address)
            #-----------------------------------------------------------------------------------------------------------
            $origin_address         = $shopifylocation->address1;
            if ($shopifylocation->address2) $origin_address.= ", ".$shopifylocation->address2;
            $origin_address        .= ", ".$shopifylocation->city . ", ". $shopifylocation->province .", ". $shopifylocation->zip. ", ".$shopifylocation->country;

            #location destination (delivery address)
            #-----------------------------------------------------------------------------------------------------------
            $destination_address    = $shopifyorder->shipping_address->address1;
            if ($shopifyorder->shipping_address->address2) $destination_address.=  ", ".$shopifyorder->shipping_address->address2;
            $destination_address   .= ", ".$shopifyorder->shipping_address->city. ", ".$shopifyorder->shipping_address->province .", ". $shopifyorder->shipping_address->zip . ", ". $shopifyorder->shipping_address->country ;


            $metas = [
                'customer'         => $shopifyorder->customer,
                'shipping_address' => $shopifylocation,
                'delivery_address' => $shopifyorder->shipping_address
            ];

            #-----------------------------------------------------------------------------------------------------------
            # Geodecode the address pickup and address destination
            #-----------------------------------------------------------------------------------------------------------
            $glovo                      = new GlovoController($emsetting);
            $origen_address_geo         = $glovo->geocode($origin_address);
            $destination_address_geo    = $glovo->geocode($destination_address);

            $this->debug("Geodecode address pickup       -> ". $origen_address_geo->status,2, false);
            $this->debug("Geodecode address destin       -> ". $destination_address_geo->status,2, false);


            # Validate the working area
            if ( $origen_address_geo->status !== 'OK')
            {

            }

            if ( $destination_address_geo->status !== 'OK')
            {

            }



            # Notes
            if ( $shopifyorder->note){
                $notes          = ". ".__('glovo.mixed.notes').$shopifyorder->note;
            }


            $order = [
                'order_id'      => $shopifyorder->id,
                'description'   => $orderLabel . $notes,
                'scheduletime'  => $scheduleTime,
                'timezone'      => $emstore->getTimeZone(),
                'preparationtime'=> null,
                'metas'         => json_encode($metas),
                'domain'        => $shop->shopify_domain
            ];

            $origen = [
                'address'       => $origin_address,
                'label'         => $origin_address,
                'details'       => $shopifylocation->name . " Contact phone: ". $shopifylocation->phone ,
                'contactphone'  => $shopifylocation->phone,
                'contactperson' => $shopifylocation->name,
                'lat'           => $origen_address_geo->latitude,
                'lng'           => $origen_address_geo->longitude

            ];

            $destinaton = [
                'address'       => $destination_address,
                'label'         => $destination_address,
                'details'       => "Contact person: ".$shopifyorder->shipping_address->first_name . " ". $shopifyorder->shipping_address->last_name.", Contact phone: ".$shopifyorder->customer->phone ,
                'contactphone'  => $shopifyorder->shipping_address->phone,
                'contactperson' => $shopifyorder->shipping_address->first_name ." ".$shopifyorder->shipping_address->last_name,
                'lat'           => $destination_address_geo->latitude,
                'lng'           => $destination_address_geo->longitude

            ];

            $this->debug("Order       -> ". print_r($order,true),  2, false);
            $this->debug("Origin      -> ". print_r($origen,true),  2, false);
            $this->debug("Destination -> ". print_r($destinaton,true), 2, false);
            $this->debug("Triying to create glovo order with API ", 2, false);


            #-----------------------------------------------------------------------------------------------------------
            # Try send the order
            #-----------------------------------------------------------------------------------------------------------
            $this->debug("------------------------------------");
            $glovoOrder     =  $glovo->createOrder($order, $origen, $destinaton);
            $this->debug("------------------------------------");


            if ($glovoOrder['success'] !== true){
                $this->debug("An error ocurred ",2, true);

                $glovoorder         = EMVexglovoorders::findByOrderId($orderid);
                $errors             = ['message' => $glovoOrder['errors']['message']];
                $this->sendMailToAdmin($orderid, $shopifyshop, $shopifyorder, $glovoorder, $errors);

                $this->updateOrderStatus($shopifyorder, $shop, "FAILED");
                throw new Exception($glovoOrder['errors']['message'], 5001);

            }


            #-----------------------------------------------------------------------------------------------------------
            $this->debug(" Update the shopify order ");
            $this->updateOrderStatus($shopifyorder, $shop, $glovoOrder['order']['state']);

            $emglovoorder   = EMVexglovoorders::findByOrderId($orderid);
            $this->debug("------------------------------------");
            $this->debug("Send Email Tracking ", 2, false);
            $tracking   = $this->sendMailTracking($orderid, $shopifyshop, $shopifyorder,  $emglovoorder);
            $this->debug("------------------------------------");


            //locations
            $reqLocations           = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/locations/{$shopifyshop->primary_location_id}.json");
            $shopifylocation        = $reqLocations->body->location;
            $fulfillment = [
                "fulfillment"           => [
                    'location_id'       => $shopifylocation->id,
                    'tracking_company'  => 'Glovo delivery',
                    'tracking_number'   => $emglovoorder->getGlovoOrderId(),
                    'tracking_url'     => $tracking['url'],
                    'notify_customer'   => true
                ]
            ];

            $this->debug("apifulfillment requesting -> ". print_r($fulfillment,true));
            $apifulfillment = $shop->api()->rest('POST',"/admin/api/2019-10/orders/{$shopifyorder->id}/fulfillments.json", $fulfillment);

            if ($apifulfillment->errors== true)
            {
                $this->debug("apifulfillment failed -> ". print_r($apifulfillment, true));

            }else
            {
                $this->debug("apifulfillment success -> ". print_r($apifulfillment->body, true));
                $localorder = EMVexorders::findByOrderId($orderid);
                $localorder->fulfilled = 'S';
                $localorder->fulfillment_number = $apifulfillment->body->fulfillment->id;
                $localorder->tracking_url       = $tracking['url'];
                $localorder->save();


            }

            $this->debug("Order created Success");



            return ["success"=>true, "shopifyorder"=>$orderid, 'glovoorder'=>$glovoOrder['order']];


        }catch(Exception $e)
        {
            $this->debug("An error ocurred en Exception : ". $e->getMessage(), 2, true);
            $message = $e->getMessage();
            Log::critical('ShopifyOrdersController@createOrderToGlovo', [$e]);
        }

        return ["success"=>false, "errors"=>['message'=> $message] ];

    }



    /**
     * Manual Resend the order to glovo when some error ocurr
     * @param int $orderid - Shopify Order Id
     * @return array
     */

    public function resendOrderToGlovo(){

        $shopifyorderid     = Request::get('order_id');
        $shop               = ShopifyApp::shop();

        try
        {

            $this->debug("",1);
            $this->debug("",1);
            $this->debug("-----------------------------------------------------------------------------------------",1);
            $this->debug(" Triying to resend order $shopifyorderid                                                 ",1);
            $this->debug("-----------------------------------------------------------------------------------------",1);



            $shopifyshop    = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;
            $this->debug("Store domain          -> ".$shop->shopify_domain,2);


            //for logs
            $carbonNow  = Carbon::now('America/Mexico_City');
            $fileToLog  = storage_path("logs/shopify/{$shop->shopify_domain}/o/rs/".$carbonNow->format('Y-m-d') .".log");


            //order details
            $shopifyorder      = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/orders/{$shopifyorderid}.json")->body->order;


            #local settings
            $emstore        = EMVexstore::findByDomain( $shop->shopify_domain );
            $emsetting      = EMVexsetting::findByStoreId( $emstore->getId() );
            app('translator')->setLocale($emsetting->getLanguage());


            $this->debug("Config store         -> OK",2);
            $this->debug("Settings domain      -> OK",2);


            #-----------------------------------------------------------------------------------------------------------
            # Orden local debe de existir previamente
            #-----------------------------------------------------------------------------------------------------------
            $emglovoorder = EMVexglovoorders::findByOrderId($shopifyorderid);

            #-----------------------------------------------------------------------------------------------------------
            # VERIFICAMOS EL SCHEDULE TIME
            #-----------------------------------------------------------------------------------------------------------
            $FieldScheduledTime = trim($emglovoorder->ORGL_SCHEDULE_TIME);
            $scheduledTime      = null;
            if (!empty($FieldScheduledTime)){
                $now            = Carbon::now('UTC');
                $savedScheduledTime  = Carbon::createFromTimestamp($FieldScheduledTime/1000)->setTimezone('UTC');
                $LocalScheduledTime  = Carbon::createFromTimestamp($FieldScheduledTime/1000)->setTimezone($emglovoorder->getTimeZone());

                $this->debug(" Have a schedule Time :".$FieldScheduledTime, 3, true);
                $this->debug("              Now UTC :".$now->format('Y-m-d H:i:s'), 3, true);
                $this->debug("  scheduleTime in UTC :".$savedScheduledTime->format('Y-m-d H:i:s') . " IN LOCAL TIME:".$LocalScheduledTime->format('Y-m-d H:i:s'), 3, true);

                if ($now < $savedScheduledTime){
                    $scheduledTime = (float) $savedScheduledTime->isoFormat('x') ;
                }
            }


            $order = [
                'order_id'      => $emglovoorder->ORGL_ORDER_ID,
                'description'   => $emglovoorder->ORGL_DESCRIPTION,
                'scheduletime'  => $scheduledTime,
                'timezone'      => $emglovoorder->ORGL_DATETIMEZONE,
                'preparationtime'=> null,
                'domain'        => $shopifyshop->domain
            ];

            $origen = [
                'label'         => $emglovoorder->ORGL_ADDRESS_ORIGIN_LABEL,
                'details'       => $emglovoorder->ORGL_ADDRESS_ORIGIN_DETAILS ,
                'contactphone'  => $emglovoorder->ORGL_ADDRESS_ORIGIN_PHONE,
                'contactperson' => $emglovoorder->ORGL_ADDRESS_ORIGIN_PERSON,
                'lat'           => $emglovoorder->ORGL_ADDRESS_ORIGIN_LAT,
                'lng'           => $emglovoorder->ORGL_ADDRESS_ORIGIN_LNG

            ];

            $destinaton = [
                'label'         => $emglovoorder->ORGL_ADDRESS_DESTINATION_LABEL,
                'details'       => $emglovoorder->ORGL_ADDRESS_DESTINATION_DETAILS ,
                'contactphone'  => $emglovoorder->ORGL_ADDRESS_DESTINATION_PHONE,
                'contactperson' => $emglovoorder->ORGL_ADDRESS_DESTINATION_PERSON,
                'lat'           => $emglovoorder->ORGL_ADDRESS_DESTINATION_LAT,
                'lng'           => $emglovoorder->ORGL_ADDRESS_DESTINATION_LNG

            ];

            $this->debug("Order       -> ". print_r($order,true), 2, false);
            $this->debug("Origin      -> ". print_r($origen,true), 2, false);
            $this->debug("Destination -> ". print_r($destinaton,true), 2, false);
            $this->debug("Triying to create glovo order with API ", 2, false);


            #-----------------------------------------------------------------------------------------------------------
            # Instance Glovo Controller
            #-----------------------------------------------------------------------------------------------------------
            $glovo  = new GlovoController($emsetting);



            $this->debug("------------------------------------");
            $glovoOrder     =  $glovo->createOrder($order, $origen, $destinaton);
            $this->debug("------------------------------------");


            if ($glovoOrder['success'] !== true){
                $this->debug("An error ocurred ", 2, true);

                $this->debug("Send a Email to Admin Store ", 2, true);
                $errors             = ['message' => $glovoOrder['errors']['message']];
                $this->sendMailToAdmin($shopifyorderid, $shopifyshop, $shopifyorder, $emglovoorder, $errors);
                $this->debug("Maid sended ", 2, true);

                $this->updateOrderStatus($shopifyorder, $shop, "FAILED");

                throw new Exception($glovoOrder['errors']['message'], 5001);

            }


            #-----------------------------------------------
            $this->debug(" Update the shopify order ");
            $this->updateOrderStatus($shopifyorder, $shop, $glovoOrder['order']['state']);




            $this->debug("------------------------------------");
            $this->debug("Send Email Tracking ", 2, false);
            $tracking = $this->sendMailTracking($shopifyorderid, $shopifyshop, $shopifyorder,  $emglovoorder);
            $this->debug("------------------------------------");


            //locations
            $reqLocations           = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/locations/{$shopifyshop->primary_location_id}.json");
            $shopifylocation        = $reqLocations->body->location;
            $fulfillment = [
                "fulfillment"           => [
                    'location_id'       => $shopifylocation->id,
                    'tracking_company'  => 'Glovo delivery',
                    'tracking_number'   => $emglovoorder->getGlovoOrderId(),
                    'tracking_url'     => $tracking['url'],
                    'notify_customer'   => true
                ]
            ];

            $this->debug("apifulfillment requesting -> ". print_r($fulfillment,true));
            $apifulfillment = $shop->api()->rest('POST',"/admin/api/2019-10/orders/{$shopifyorder->id}/fulfillments.json", $fulfillment);

            if ($apifulfillment->errors== true)
            {
                $this->debug("apifulfillment failed -> ". print_r($apifulfillment, true));

            }else
            {
                $this->debug("apifulfillment success -> ". print_r($apifulfillment, true));
            }


            $this->debug("Order created Success");
            $this->LogToFile($fileToLog);


            return Redirect::route('shopify.orders.detail',['id'=>$shopifyorderid])->with("success", __('glovo.orderdetail.resend.success'));


        }catch(Exception $e)
        {
            $errors = __('glovo.orderdetail.create.fail'). $e->getMessage();
        }

        $this->debug("Saving logs ", 2, true);
        $this->LogToFile($fileToLog);
        $this->debug("Redirecting to order detail with errors", 2, true);

        return Redirect::route('shopify.orders.detail',['id'=>$shopifyorderid])->with("errors", $errors);

    }





    public function updateLocalOrder($localshop, $shopifyorder){

        try
        {

            BufferLog::Debug("Updating local order", 2);
            BufferLog::Debug("Order id recived ->".$shopifyorder->id, 2);


            BufferLog::Debug("Finding order ", 2);
            $localorder = EMVexorders::where('id', $shopifyorder->id)->first();

            if ( is_null($localorder) ){
                BufferLog::Debug("Local order not found");
                return false;
            }

            $localorder->financial_status = $shopifyorder->financial_status;
            $localorder->save();

            BufferLog::Debug("Order updated", 2);

            return true;


        }catch(Exception $e)
        {
            BufferLog::Debug('An error ocurred on ShopifyOrdersController@updateLocalOrder -> '. $e->getMessage(), 2);
            Log::critical('An error ocurred on ShopifyOrdersController@updateLocalOrder -> '. $e->getMessage());
        }


        return false;

    }



    /**
     * @return array
     */
    public function getLive(){

        $orderid        = Request::get('orderid');
        $emglovoorder   = EMVexglovoorders::findByOrderId($orderid);

        //store
        $emstore        = EMVexstore::findByDomain($emglovoorder->ORGL_DOMAIN);

        //setttings
        $emsettings     = EMVexsetting::findByStoreId($emstore->getId());

        $glovo          = new GlovoController($emsettings);
        $tracking       = $glovo->getOrderTracking($orderid);

        return $tracking;
    }



    /**
     * @return array
     */
    public function getCourierContact(){

        $orderid        = Request::get('orderid');
        $emglovoorder   = EMVexglovoorders::findByOrderId($orderid);

        //store
        $emstore        = EMVexstore::findByDomain($emglovoorder->ORGL_DOMAIN);

        //setttings
        $emsettings     = EMVexsetting::findByStoreId($emstore->getId());

        $glovo          = new GlovoController($emsettings);
        $courier        = $glovo->getCourierContact($orderid, $emglovoorder);

        return $courier;

    }





    /**
     * @param $shipifyOrderId
     */
    public function sendMailTracking($shipifyOrderId, $shopifyshop, $shopifyorder, $glovoorder){


        try
        {
            $code   = "/orders/tracking/?code=".Crypt::encrypt( json_encode(['domain'=>$shopifyshop->domain, 'order'=>$shipifyOrderId]));
            $url    =  env('SHOPIFY_APP_URL')."".$code;

            $email = $shopifyorder->customer->email;
            $name  = $shopifyorder->customer->first_name.' '.$shopifyorder->customer->last_name;



            if ( !empty($email)) {
                Mail::to($email,  $name)->queue( new OrderTracking($shopifyshop, $shopifyorder, $glovoorder, ['url'=> $url]));
            }


            return ['url'=>$url];

        }catch (Exception $e)
        {
            \Log::critical('sendMailTracking', [$e]);
        }

        return false;


    }



    /**
     * @param $shipifyOrderId
     */
    public function sendMailToAdmin($shopifyorderid, $shopifyshop, $shopifyorder, $glovoorder, $errors=[]){


        $email = $shopifyshop->email;
        $name  = $shopifyshop->name;

        $settings   = EMVexsetting::findByStoreId($shopifyshop->id);
        app('translator')->setLocale($settings->getLanguage());


        Mail::to($email,  $name)->queue( new OrderFailed($shopifyorderid, $shopifyshop, $shopifyorder, $glovoorder, $errors));

    }




    /**
     * @param $shipifyOrderId
     * @param $shopifyshop
     * @param $shopifyorder
     * @param $glovoorder
     */
    public function ManagerMailTracking($shipifyOrderId){

        //em glovo
        $glovoorder         = EMVexglovoorders::findByOrderId($shipifyOrderId);

        $shop           = ShopifyApp::shop();
        $shop           = Shop::where('shopify_domain', $glovoorder->ORGL_DOMAIN)->first();

        //store
        $shopifyshop  = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;

        //order details
        $shopifyorder      = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/orders/{$shipifyOrderId}.json")->body->order;

        $meta               = json_decode($glovoorder->ORGL_METAS);


        //local settings
        $emstore    = EMVexstore::findByDomain( $glovoorder->ORGL_DOMAIN);
        $emsettings = EMVexsetting::findByStoreId($emstore->getId());

        app('translator')->setLocale($emsettings->getLanguage());

        return $this->sendMailTracking($shipifyOrderId, $shopifyshop, $shopifyorder, $glovoorder);



    }


    public function ManagerOrderFailed($shipifyOrderId){

        //em glovo
        $glovoorder         = EMVexglovoorders::findByOrderId($shipifyOrderId);
        $shop               = ShopifyApp::shop();
        $shop               = Shop::where('shopify_domain', $glovoorder->ORGL_DOMAIN)->first();

        //store
        $shopifyshop        = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;

        //order details
        $shopifyorder       = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/orders/{$shipifyOrderId}.json")->body->order;


        $errors            = ['code'=>500, 'message'=>'Algo paso mal'];

        $this->sendMailToAdmin($shipifyOrderId, $shopifyshop, $shopifyorder, $glovoorder, $errors);



    }


    /**
     * @link https://www.shopify.com/partners/blog/relative-pagination
     * @return array
     */
    public function getProductsApiV201910()
    {

        $ids = Request::get('ids');
        $shop = ShopifyApp::shop();

        $query = [
            'limit'         => $this->pageSize,
            'status'        => 'any',
            //'created_at_min' => Carbon::now()->subMonths(6)->format('Y-m-dTH:i:s'),
            //'financial_status'=>'paid'
        ];

        if ($ids) {
            $query['ids'] = $ids;
        }

        if (\Request::get('page_info')) {
            $query['page_info'] = Request::get('page_info');
        }
        if (\Request::get('rel')) {
            $query['rel'] = Request::get('rel');
        }


        $apiorders = $shop->api()->rest('GET', '/admin/api/' . \Config('shopify-app.api_version') . '/orders.json', $query);
        $linkHeader = collect($apiorders->response->getHeader('Link'))->first();


        $orders = [];
        foreach ($apiorders->body->orders as $key => $order) {
            if( $order->shipping_lines[0]->title === env('SHOPIFY_SHIPPING_TITLE'))
                $orders[] = $order;
        }


        // Create a new Laravel collection from the array data
        $itemCollection = collect($orders);


        $next = null;
        $previous = null;
        if ($apiorders->link) {
            $params = [];
            $query = \Request::getQueryString();
            parse_str($query, $params);

            if ($apiorders->link->previous) {
                $previous = env("SHOPIFY_APP_URL") . "/orders?" . http_build_query(array_merge($params, ['page_info' => $apiorders->link->previous, "rel" => 'previous']));

            }

            if ($apiorders->link->next) {
                $next = env("SHOPIFY_APP_URL") . "/orders?" . http_build_query(array_merge($params, ['page_info' => $apiorders->link->next, "rel" => 'next']));

            }


        }

        $paginator = new \StdClass();
        $paginator->previous = $previous;
        $paginator->next = $next;

        return ['orders' => $itemCollection, 'paginator' => $paginator];
    }


        /**
     * @param array $params
     */
    public function getOrdersAPI ($params=[]){

        $ids    = Request::get('ids');
        $shop   = ShopifyApp::shop();
        $total  = 0;


        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();


        //Total Items
        #$apitotal = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/orders/count.json');
        #$total    = $apitotal->body->count;
        $query = [
            'status'            => 'any',
            'created_at_min'    => Carbon::now()->subMonths(3)->format('Y-m-dTH:i:s'),
            'financial_status'  =>'paid'
        ];

        if ($ids) $query['ids']   = $ids;
        $apiproduts = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/orders.json', $query);
        $orders     = [];
        foreach ($apiproduts->body->orders as $key=> $order){
            if( $order->shipping_lines[0]->title === env('SHOPIFY_SHIPPING_TITLE'))
                $total++;
        }



        $query = [
            'limit'         => $this->pageSize,
            'page'          => $currentPage,
            'status'        => 'any',
            'created_at_min' => Carbon::now()->subMonths(3)->format('Y-m-dTH:i:s'),
            'financial_status'=>'paid'
        ];
        if ($ids) $query['ids']   = $ids;

        $apiproduts = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/orders.json', $query);
        $orders     = [];
        foreach ($apiproduts->body->orders as $key=> $order){
            if( $order->shipping_lines[0]->title === env('SHOPIFY_SHIPPING_TITLE'))
                $orders[] = $order;
        }


        // Create a new Laravel collection from the array data
        $itemCollection = collect($orders);

        unset($order);
        unset($orders);

        // Define how many items we want to be visible in each page
        $perPage = $this->pageSize;

        // Slice the collection to get the items to display in current page
        //$currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();

        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($itemCollection , $total, $perPage);

        // set url path for generted links
        $paginatedItems->setPath(env("SHOPIFY_APP_URL"). "/orders");


        return $paginatedItems;


    }



    public function getOrders ($shop){

        try
        {

            $request    = request();
            $shipping_title = env('SHOPIFY_SHIPPING_TITLE');
            $builderOrders     =
                EMVexorders::where('shop',$shop->id)
                ->where( function ($query) use ($shipping_title) {
                    $query->where('shipping_method', 'like', '%' . $shipping_title . '%' )
                    ->orWhere('glovo_attemp', 'S');;
                })
            ->orderBy('number','desc');

            $totalCount = $builderOrders->count();

            $page = $request->input('page') ?:1;
            if ($page) {
                $skip = $this->pageSize * ($page - 1);
                $orders = $builderOrders->take($this->pageSize)->skip($skip);
            } else {
                $orders = $builderOrders->take($this->pageSize)->skip(0);
            }

            $parameters = $request->getQueryString();
            $parameters = preg_replace('/&page(=[^&]*)?|^page(=[^&]*)?&?/','', $parameters);
            $path = url('/') . '/orders?' . $parameters;

            $orders     = $builderOrders->get();

            #$paginator = new \Illuminate\Pagination\LengthAwarePaginator($orders, $totalCount, 10, $page);
            #$paginator = $paginator->withPath(env("SHOPIFY_APP_URL"). "/orders");

            // Define how many items we want to be visible in each page
            $perPage = $this->pageSize;

            // Slice the collection to get the items to display in current page
            //$currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();

            // Create our paginator and pass it to the view
            $paginatedItems= new LengthAwarePaginator($orders , $totalCount, $perPage, $page);

            // set url path for generted links
            $paginatedItems->setPath(env("SHOPIFY_APP_URL").  '/orders?' . $parameters);


            return $paginatedItems;


        }catch (Exception $e)
        {
            Log::critical( $e->getFile() .'@getOrders:'.$e->getLine()." --> " .$e->getMessage());
        }

        return false;

    }





    /**
     * When the order is created, execute this job for write metas
     * @param $order
     * @param $shop
     */
    public function onCreated($order, $shop)
    {

        $schedule_when  = "glovo_when_receive";
        $schedule_day   = "glovo_schedule_day";
        $schedule_time  = "glovo_schedule_time";

        $schedule_when_val  = null;
        $schedule_time_val  = null;
        $glovo_attemp       = 'N';

        try
        {
            $this->debug("onCreated - Creating local order", 3);

            $notes          = collect($order->note_attributes)->keyBy('name');

            $this->debug("Number of metas in order [notes] => ".$notes->count(), 4);
            $this->debug("Result Metas                     => ".$notes->toJson(), 4);


            //metas
            $apimetas           = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/orders/{$order->id}/metafields.json");
            $shopifyordermetas  = collect( $apimetas->body->metafields )->keyBy('key');



            $this->debug("Shop Domain -> ".$shop->shopify_domain, 4);
            $emstore        = EMVexstore::findByDomain($shop->shopify_domain);
            $emsettings     = EMVexsetting::findByStoreId($emstore->getId());

            $this->debug("Evaluating Store Status ........", 4);
            $this->debug("Plan of Store  ................" . (is_null($emstore->getCarrierId()) ? ' is a Basic Plan' : ' is Advanced Plan'), 4);
            $this->debug("Store is validated  ............" . ($emsettings->getValidated() ? ' is validated and can delivery by glovo' : 'not validated'), 4);
            $this->debug("Avalilable products ............" . ($emsettings->getEnableAllProducts() ? ' is available for all' : 'is enable for custom products'), 4);
            $this->debug("Determinate when create the order", 4);
            $this->debug("  Setting for create Orderd is set to .....".$emsettings->getCreateStatus(), 4);
            $this->debug("  Setting for Schedule Orders is set to ...". ( $emsettings->getAllowScheduled() ? ' allow schedules' : ' no schedulled' ), 4);
            $this->debug("  Order financial status is................".$order->financial_status, 4);
            $this->debug("  Shipping method is: ", 4);

            if ( isset($order->shipping_lines) and count($order->shipping_lines)>0)
            {
                $this->debug( "[". $order->shipping_lines[0]->title ."]", 4);
            }else
            {
                $this->debug( '----> Cant determinate shipping method' , 5);
                $this->debug( '----> Some bad ocurred - check order json', 5);
            }


            $this->debug("Evaluating Allow Scheduled", 4);

            if ( is_null($emstore->getCarrierId()) )
            {
                $this->debug("Store have a Basic Plan", 4);
                if ($emsettings->getValidated()  ) //and $emsettings->getAllowScheduled() == false
                {   $this->debug("Allow Scheduled is Off", 4);
                    $this->debug("Attemp send manualy", 4);
                    $glovo_attemp       = 'S';
                }


            }else

            {
                $this->debug("Store have a Advanced Plan", 4);
                $this->debug("Use the chosen shipping option", 4);
                if ( isset($order->shipping_lines) and is_array($order->shipping_lines) and $order->shipping_lines[0]->title === env('SHOPIFY_SHIPPING_TITLE'))
                {
                    $glovo_attemp       = 'S';

                }


            }




            $this->debug("Evaluating Metas", 4);
            if ($notes->has($schedule_when))
            {
                $this->debug("Allow Scheduled is On", 5);
                $schedule_when_val  = $notes->has($schedule_when) ?  $notes->get($schedule_when)->value : "scheduled";
                $metadata = array(
                    'metafield' => array(
                        "namespace" => "glovo_shipping",
                        "key"       => $schedule_when,
                        "value"     => $schedule_when_val,
                        "value_type"=> "string",
                    )
                );

                $this->debug("value of attribute $schedule_when is ".$schedule_when_val, 5);

                if ( !$shopifyordermetas->has($schedule_when) )
                    $apirest    = $shop->api()->rest('POST',"/admin/api/".\Config('shopify-app.api_version')."/orders/{$order->id}/metafields.json", $metadata);

            }


            if ($notes->has($schedule_day)){
                $metadata = array(
                    'metafield' => array(
                        "namespace" => "glovo_shipping",
                        "key"       => $schedule_day,
                        "value"     => $notes->get($schedule_day)->value,
                        "value_type"=> "string",
                    )
                );
                $this->debug("value of attribute $schedule_day is ".$notes->get($schedule_day)->value, 5);

                if ( !$shopifyordermetas->has($schedule_day) )
                    $apirest    = $shop->api()->rest('POST',"/admin/api/".\Config('shopify-app.api_version')."/orders/{$order->id}/metafields.json", $metadata);

            }



            if ($notes->has($schedule_time)){
                $schedule_time_val  = $notes->get($schedule_time)->value;
                $metadata = array(
                    'metafield' => array(
                        "namespace" => "glovo_shipping",
                        "key"       => $schedule_time,
                        "value"     => $schedule_time_val,
                        "value_type"=> "string",
                    )
                );

                $this->debug("value of attribute $schedule_time is ".$schedule_time_val, 5);

                if ( !$shopifyordermetas->has($schedule_time) )
                    $apirest    = $shop->api()->rest('POST',"/admin/api/".\Config('shopify-app.api_version')."/orders/{$order->id}/metafields.json", $metadata);

            }




            #convert dates
            $created_at = Carbon::parse($order->created_at);

            #registramos la orden local
            $this->debug("Preparing to save local order", 4);

            $localorder      = EMVexorders::where('id', $order->id)->first();

            if ( is_null($localorder))
            {
                $this->debug("Creating order", 4);

                $localorder = new EMVexorders();
                $localorder->shop               = $shop->id;
                $localorder->store_id           = $emstore->getId();
                $localorder->id                 = $order->id;
                $localorder->name               = $order->name;
                $localorder->number             = $order->number;
                $localorder->order_number       = $order->order_number;
                $localorder->currency           = $order->currency;
                $localorder->shipping_method    = implode(", ", $this->getShippingMethods($order));
                $localorder->financial_status   = $order->financial_status;
                $localorder->fulfillment_status = $order->fulfillment_status;
                $localorder->created_at         = $created_at->format('Y-m-d H:i:s');
                $localorder->customer           = json_encode(isset($order->customer) ? $order->customer : "");
                $localorder->shipping_address   = json_encode( isset($order->shipping_address) ? $order->shipping_address : "" );
                $localorder->order_status_url   = $order->order_status_url;
                $localorder->glovo_attemp       = $glovo_attemp;
                $localorder->deliverywhen       = $schedule_when_val;
                $localorder->scheduletime       = $schedule_time_val;
                $localorder->source             = json_encode($order);
                $localorder->save();

                $this->debug("Creating order .... OK", 4);
                $this->debug("Result attemp to glovo : $glovo_attemp", 4);
            }

            $this->debug("Returning .... true", 4);

            return true;


        }catch (Exception $e)
        {
            $this->debug("Error in -> ShopifyOrdersController.onCreated", 4);
            $this->debug("Error message -> ".$e->getMessage(), 4);

            Log::critical("Error in -> ShopifyOrdersController.onCreated ". $e->getMessage() ."- Line:".$e->getLine() );
        }


        return false;

    }




    /**
     * Update order status when glovo is completed
     * @param $order
     * @param $shop
     */
    public function updateOrderStatus($shopifyorder, $shop, $state){

        //metas
        $apimetas           = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/orders/{$shopifyorder->id}/metafields.json");
        $shopifyordermetas  = collect( $apimetas->body->metafields )->keyBy('key');


        $this->debug("Updating order status to -> ".$state, 2, true);

        $metadata = array(
            'metafield' => array(
                "namespace" => "glovo_shipping",
                "key"       => "glovo_state",
                "value"     => $state,
                "value_type"=> "string",
            )
        );


        if ( !$shopifyordermetas->has('glovo_state') )
        {
            $this->debug("Not exists creating -> OK ", 2, true);
            $shop->api()->rest('POST',"/admin/api/".\Config('shopify-app.api_version')."/orders/{$shopifyorder->id}/metafields.json", $metadata);

        }else
        {
            $this->debug("Exists creating -> OK ", 2, true);
            $themeta    = $shopifyordermetas->get('glovo_state');
            $shop->api()->rest('PUT',"/admin/api/".\Config('shopify-app.api_version')."/orders/{$shopifyorder->id}/metafields/{$themeta->id}.json", $metadata);
        }


        $this->debug("Returning ", 2, true);


    }







    public function listCarrier(){

        $shop = ShopifyApp::shop();

        $request = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/carrier_services.json');

        dd($request);

    }


    public function updateCarrier(){

    }

    public function registerCarrier(){
        $shop = ShopifyApp::shop();

        $carrier = array(
            'carrier_service' => array(
                "name"              => "My Delivery Shipping Rate Provider is already configured", //
                "callback_url"      => env('SHOPIFY_APP_URL')."/carrier/rate", //"https://dev.acdnomina.com.mx/api/carrier/rate", //env('SHOPIFY_APP_URL').
                "format"            => "json",
                "carrier_service_type"  => "api",
                "service_discovery" => "true",
                "active"            => "true"
            )
        );


        $carrier = $shop->api()->rest('POST', '/admin/api/'.\Config('shopify-app.api_version').'/carrier_services.json', $carrier);



        $carriers = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/carrier_services.json');
        dd($carriers);
    }


    public function removeCarrier($carrier_service_id){
        $shop = ShopifyApp::shop();

        $request = $shop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/carrier_services/{$carrier_service_id}.json");


        dd($request);
    }


    /**
     * Extrae which details a shipping method used
     * @param $shopifyorder
     */
    public function getShippingMethods ( $shopifyorder){

        $methods = [];

        if ( ! is_array($shopifyorder->shipping_lines)){
            return false;
        }

        foreach ($shopifyorder->shipping_lines as $line){
            $methods[] = $line->title;
        }

        return $methods;

    }


    /**
     * Check if a string is a valid date(time)
     *
     * DateTime::createFromFormat requires PHP >= 5.3
     *
     * @param string $str_dt
     * @param string $str_dateformat
     * @param string $str_timezone (If timezone is invalid, php will throw an exception)
     * @return bool
     */
    function createFromFormat($format, $time, $tz=null) {
        if ($tz !== null) {
            $date = DateTime::createFromFormat($format, $time, new DateTimeZone($tz));
        } else {
            $date = DateTime::createFromFormat($format, $time);
        }

        if ($date instanceof DateTime) {
            return $date;
        }

        return $date && DateTime::getLastErrors()["warning_count"] == 0 && DateTime::getLastErrors()["error_count"] == 0;
    }






}
