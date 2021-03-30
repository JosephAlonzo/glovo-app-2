<?php

namespace App\Http\Controllers\Shopify;

use App\Http\Controllers\Glovo\GlovoController;
use App\Mail\AppInstalled;
use App\Models\EMVexsetting;
use App\Models\EMVexstore;
use Escom\Base\CBase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;
use Exception;

use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use OhMyBrew\ShopifyApp\Models\Shop;
use OhMyBrew\ShopifyApp\Services\ShopSession;
use OhMyBrew\ShopifyApp\Traits\ShopModelTrait;
use App\Models\Shopify\EMProductmetadata;


use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Liquid\Template;
use Utils\UStore;
use Vexsolutions\Utils\Logger\Facades\BufferLog;


class ShopifyApplication extends CBase
{



    /**
     * Shop's themes
     *
     * @var string
     */
    protected $shopThemes;

    /**
     * Shop's scripts
     *
     * @var
     */
    protected $shopScriptTags;



    /**
     * Shop's rate title for delivering glovo service
     * Warning : Not change the titlte
     *
     * @var
     */
    public $shippingTitle   = "";


    public function evaluate(){

        $language = new ExpressionLanguage();
        $userInfo = array(
            'plazatipo'        => 'BASE',
            'antiguedad' => 10
        );
        $expression = 'plazatipo == "BASE" ? ( (antiguedad > 1 and antiguedad <= 10) ? 100:  20 ) : 40 
        and plazatipo=="s"';
        $isLegit = $language->evaluate(
            $expression,
            $userInfo
        );

        $template = new Template();
        $template->parse("
        {% assign today_date = 'now' | date: '%s' %} 
        
        {{ today_date }}
        
        ");

        $result  =  $template->render(array('inicio' => '2019-08-01', 'fin'=>'2019-08-15' ,  'bithday'=>'1981-08-05')) ;


        $result  = preg_replace("/[\n\r]/","",$result);
        $result  = preg_replace("/[\r\n]+/", "", $result);
        $result  = preg_replace("/\s+/",  '', $result);
        return "<textarea>" . $result."</textarea>";




    }

    /**
     * Create a new job instance.
     *
     * @param object $shop The shop object
     *
     * @return void
     */
    public function __construct()
    {

        $a = \Session::all();
        $a = app('session')->all();
        $s = (new ShopSession())->getDomain();

        $shop = ShopifyApp::shop();
        //$this->shop = ShopifyApp::shop();
        //$this->api  = $shop->api();

        $this->shippingTitle = env('SHOPIFY_SHIPPING_TITLE');
    }


    public function privatity(){
        return view('privacity');
    }

    /**
     * Calculate the rate
     * @return array
     */
    public function rate (){

        $total_price    = 0;
        $description    = "";
        $currency       = "";
        $rates          = [];
        $day            = Carbon::now(); //right now


        try
        {

            $hmac   = Request::header('x-shopify-hmac-sha256') ?: '';
            $domain = Request::header('x-shopify-shop-domain');
            $data   = Request::getContent();

            //for logs
            $carbonNow      = Carbon::now('America/Mexico_City');
            $fileToLog      = storage_path("logs/shopify/{$domain}/r/".$carbonNow->format('Y-m-d') .".log");


            $this->debug("",1);
            $this->debug("",1);
            $this->debug("-----------------------------------------------------------------------------------------",1);
            $this->debug(" RATE TEST                                                                               ",1);
            $this->debug("-----------------------------------------------------------------------------------------",1);

            $this->debug($hmac);
            $this->debug($domain);
            $this->debug($data);

            $content        = file_get_contents("php://input");
            $objectrate     = json_decode($content);
            $currency       = $objectrate->rate->currency;


            $shop           = Shop::where('shopify_domain', $domain)->first();
            $csetting       = new ShopifySettingsController();
            $emstore        = EMVexstore::findByDomain($domain);


            if (is_null($emstore)){
                throw new Exception("Invalid domain: {$domain} -> not found in the database", 4004);
            }


            $setting        = EMVexsetting::findByStoreId($emstore->getId());
            if (is_null($emstore) ){
                throw new Exception("Invalid settings -> not found in the database", 4004);
            }

            $default_lang = $setting->getLanguage();
            app('translator')->setLocale($default_lang);


            #-----------------------------------------------------------------------------------------------------------
            # VERIFICAMOS LA DISPONIBILIDAD DE LA TIENDA
            #-----------------------------------------------------------------------------------------------------------
            $items          = collect( $objectrate->rate->items );
            $product_list   = $items->pluck('product_id')->toArray();
            $disponiblidad  = $csetting->isAvalibleService($setting->getId(),  $day->format('Y-m-d H:i:s'), $product_list);

            if ($disponiblidad['status']['code'] !== 200) {
                $this->debug("Services is not available: " . print_r($disponiblidad,true),1);
                throw new Exception("Services is not available", 4004);
            }


            #-----------------------------------------------------------------------------------------------------------
            # ESTIMANDO EL COSTO
            #-----------------------------------------------------------------------------------------------------------
            $this->debug("Estimando el costo",1);
            $origen_address = $objectrate->rate->origin->address1;
            if($objectrate->rate->origin->address2)       $origen_address.=", ".$objectrate->rate->origin->address2;
            if($objectrate->rate->origin->address3)       $origen_address.=", ".$objectrate->rate->origin->address3;
            if($objectrate->rate->origin->city)           $origen_address.=", ".$objectrate->rate->origin->city;
            if($objectrate->rate->origin->province)       $origen_address.=", ".$objectrate->rate->origin->province;
            if($objectrate->rate->origin->postal_code)    $origen_address.=", ".$objectrate->rate->origin->postal_code;
            if($objectrate->rate->origin->country)        $origen_address.=", ".$objectrate->rate->origin->country;

            $destination_address = $objectrate->rate->destination->address1;
            if($objectrate->rate->destination->address2)  $destination_address.=", ".$objectrate->rate->destination->address2;
            //if($rate->rate->destination->address3)  $destination_address.=", ".$rate->rate->destination->address3;
            if($objectrate->rate->destination->city)      $destination_address.=", ".$objectrate->rate->destination->city;
            //if($rate->rate->destination->province)  $destination_address.=", ".$rate->rate->destination->province;
            if($objectrate->rate->destination->postal_code) $destination_address.=", ".$objectrate->rate->destination->postal_code;
            if($objectrate->rate->destination->country)   $destination_address.=", ".$objectrate->rate->destination->country;

            $glovo          = new GlovoController($setting);

            $this->debug("Geodecoding address",1);

            $geoDecodeOrigin    = $glovo->geocode($origen_address);
            $geoDecodeDestin    = $glovo->geocode($destination_address);

            if ( $geoDecodeDestin->status !== 'OK')
            {
                throw new Exception("Unable to decodify  destination address:" .$destination_address, 5002);
            }

            $this->debug("Geodecoding address -> OK",1);

            $pOrigen= [
                'lat'           => $geoDecodeOrigin->latitude,
                'lng'           => $geoDecodeOrigin->longitude,
                'label'         => $geoDecodeOrigin->formatted_address,
                'details'       => '',
                'contactphone'  => $objectrate->rate->origin->phone,
                'contactperson' => $objectrate->rate->origin->name

            ];
            $pDestin= [
                'lat'           => $geoDecodeDestin->latitude   ,
                'lng'           => $geoDecodeDestin->longitude,
                'label'         => $geoDecodeDestin->formatted_address,
                'details'       => '',
                'contactphone'  => $objectrate->rate->destination->phone,
                'contactperson' => $objectrate->rate->destination->name
            ];
            $orden  = ['description'=>'Prueba'];


            $this->debug("Geodecoding address -> OK",1);

            $this->debug("------------------------------------");
            $estimate       = $glovo->estimateOrderPrice($orden, $pOrigen, $pDestin);
            $this->debug("------------------------------------");


            if ($estimate['success'] !== true){

                $this->debug("An error ocurred ", 2, true);
                throw new Exception($estimate['errors']['message'], 5001);

            }




            if ( $setting->getCostType() == 'Free' ){
                $total_price             = 0000;
                $description             = $setting->getMethodTitle() ? $setting->getMethodTitle() : "Free shipping using Glovo service";
                $description             = $description . ' ' .__('glovo.storefront.shipping.rate');

            }elseif ( $setting->getCostType() == 'Fixed' ){
                $total_price             = $setting->getCostDefault() * 100;
                $description             = $setting->getMethodTitle() ? $setting->getMethodTitle() : "Cost shipping using Glovo service";
                $description             = $description . ' ' .__('glovo.storefront.shipping.rate');

                $this->debug("Method Type: " . $description);

            }elseif ( $setting->getCostType() == 'Calculate' ){

                $this->debug("Cost using Glovo Delivery Service");
                $this->debug('json response  ------> ' . print_r($estimate,true));

                //articulos del cart
                $estimate_price     = $estimate['order']['total']['amount'];
                $currency           = $estimate['order']['total']['currency'];
                $description        = $setting->getMethodTitle() ? $setting->getMethodTitle() : "Cost using Glovo Delivery Service";
                $description        = $description . ' ' .__('glovo.storefront.shipping.rate');
                $total_price        = $estimate_price;

                $this->debug("Currency ----> ".$currency);


                if ( $currency == 'PAB'){
                    $this->debug('Fix tax for PAB currency ');
                    $this->debug('Envio  ------> ' . $total_price);
                    $tax            =  $total_price * 0.07;
                    $total_price    = $tax +  $total_price;
                    $total_price    = round( $total_price , 2);

                    $this->debug('Tax    ------> ' . $tax);
                    $this->debug('Total  ------> ' . $total_price);

                }

            }


            $this->debug("Method Type: " . $setting->getCostType());
            $this->debug("Currency   : " . $currency);
            $this->debug("Total price: " . $total_price);

            $rates = [
                "rates" => [
                    "service_name"  => $this->shippingTitle,
                    "service_code"  => "ON",
                    "total_price"   => $total_price,
                    "description"   => $description,
                    "currency"      => $currency,
                    "min_delivery_date" => Carbon::now(),
                    "max_delivery_date" => Carbon::now()
                ]

            ];


        }
        catch (Exception $e)
        {
            $this->debug("try catch error: " . $e->getMessage());
        }


        $this->debug("---- END ----");
        $this->debug("");
        $this->LogToFile($fileToLog);

        return $rates;



    }


    /**
     * Get the working hours of especific day
     * @usage in Front-End
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceAvaliable(){

        $domain             = Request::get('shop');
        $productlist        = Request::get('products');
        $productlist        = collect(json_decode($productlist));



        if (!$domain or $productlist->count() == 0){
            return response()->json(['status'=> ['code'=>410]], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->setCallback(Request::get('callback'));
        }

        $shop               = Shop::where('shopify_domain', $domain)->first();

        if (!$shop){
            return response()->json(['status'=> ['code'=>410]], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->setCallback(Request::get('callback'));
        }

        $emstore            = EMVexstore::findByDomain($domain);
        if (!$emstore){
            return response()->json(['status'=> ['code'=>410]], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->setCallback(Request::get('callback'));
        }

        $emsetting          = EMVexsetting::findByStoreId($emstore->getId());


        if ($emsetting->getEnable() != 1){
            return response()->json(['status'=> ['code'=>510, 'message' =>'Not enable glovo delivery']], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

        }


        app('translator')->setLocale($emsetting->getLanguage());

        //right now
        $day                = Carbon::now($emstore->getTimeZone());
        $csetting           = new ShopifySettingsController();
        $response           = $csetting->isAvalibleService($emsetting->getId(), $day->format('Y-m-d H:i:s') , $productlist->keyBy('product_id')->pluck('product_id')->toArray());
        $response['snippet']= $csetting->htmlSnippet($shop, $emsetting, $productlist );


        return response()->json($response, 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Headers', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->setCallback(Request::get('callback'));

    }



    /**
     * Get the working hours of especific day
     * @usage in Front-End
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWorkingTimes(){

        $domain             = Request::get('shop');
        $productlist        = Request::get('products');
        $day                = Request::get('day'); //day of week
        $productlist        = collect(json_decode($productlist));

        if (!$domain or $productlist->count() == 0){
            return response()->json(['status'=> ['code'=>410]], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        $shop               = Shop::where('shopify_domain', $domain)->first();
        if (!$shop){
            return response()->json(['status'=> ['code'=>410]], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        $emstore            = EMVexstore::findByDomain($domain);
        if (!$emstore){
            return response()->json(['status'=> ['code'=>410]], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        $emsetting          = EMVexsetting::findByStoreId($emstore->getId());
        $csetting           = new ShopifySettingsController();
        $response           = $csetting->getWorkingTimes($emsetting->getId(), $day, $productlist->pluck('product_id')->toArray());

        return response()->json($response, 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Headers', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');

    }


    /**
     * Load array of working days from now
     * is used en shoping cart
     * @param idsetting
     */
    public function getWorkingDays(){
        $settingId  = Request::get('setting');

        $csetting   = new ShopifySettingsController();
        $array      = $csetting->getArrayWorkingDays($settingId);


        return response()->json($array, 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');



    }


    public function getProductAvaliable(){

        $domain         = Request::get('shop');
        $variant        = Request::get('product');
        $shop           = Shop::where('shopify_domain', $domain)->first();

        $localstore     = EMVexstore::findByDomain($domain);
        if (!$localstore){
            Log::critical('Not found - domain');
            Log::critical('getProductAvaliable - domain', [$domain]);
            Log::critical('getProductAvaliable - variant', [$variant]);
            Log::critical('getProductAvaliable - localstore', [$localstore]);

            return response()->json(['status'=> ['code'=>410]], 200)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }


        $settings       = EMVexsetting::findByStoreId($localstore->getId());




        app('translator')->setLocale($settings->getLanguage());

        $cproducts      = new ShopifyProductsController();
        $cproducts->emsettings = $settings;
        $response       = $cproducts->isProductAvaliable($shop, null , $variant);

        return response()->json($response, 200)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    }





    /**
     * @param $shop_id
     * @return bool
     */
    public function onInstall($shop, $shopifyshop, $shop_id){

        try
        {

            #-----------------------------------------------------------------------------------------------------------
            # MARCA COMO INSTALADA POR PRIMERA VEZ
            #-----------------------------------------------------------------------------------------------------------
            $store  = EMVexstore::find($shop_id);
            if ( $store ) {
                if($store->STORE_INSTALLED == 1) {
                    BufferLog::Debug(' Applications is installed');
                    BufferLog::Debug(' Noting to do.....');
                    return true;
                }

                if($store->STORE_INSTALLED  == 0) {
                    $store->STORE_INSTALLED  = 1;
                    $store->STORE_UNINSTALED = 0;
                    $store->save();
                }
            }

            #NO SE ENCUENTRE INSTALADA EN LA TIENDA
            if (is_null($store))
            {
                BufferLog::Debug(' Creating a new store ..... Pending');
                $store  =  new EMVexstore();
                $store->STORE_ID            = $shop_id;
                $store->STORE_DOMAIN        = $shop->shopify_domain; //hay diferencias entre el dominio de la api y el objeto shop
                $store->STORE_TIMEZONE      = $shopifyshop->timezone;
                $store->STORE_IANA_TIMEZONE = $shopifyshop->iana_timezone;

                $store->STORE_INSTALLED = 1;
                $store->STORE_INSTALED_DATE = \DB::raw('now()');
                $store->save();

                BufferLog::Debug(' Created a new store ..... OK');
            }



            #-----------------------------------------------------------------------------------------------------------
            # SI TIENE SETTINGS LOS RECUPERA
            #-----------------------------------------------------------------------------------------------------------
            BufferLog::Debug(' Setting up settings');
            $settings   = EMVexsetting::findByStoreId($shop_id);
            if ($settings)
            {
                if ( $settings->trashed() ){
                    $settings->restore();
                }
            }


            #-----------------------------------------------------------------------------------------------------------
            # No existe la configuracion de la tienda
            # Crea la primera configuracion predeterminada para la tienda
            #-----------------------------------------------------------------------------------------------------------
            if (is_null($settings)){

                BufferLog::Debug(' Initializing settings saving');
                $csetting   = new ShopifySettingsController();
                $csetting->initialize($shop, $shopifyshop);
                BufferLog::Debug(' Initializing settings saved ... OK');
            }
            else
            {
                BufferLog::Debug(' Recovering staring');
                $csetting   = new ShopifySettingsController();
                $csetting->saveMetadata($settings);

                #save default working days
                $csetting->initDefaultHours($shop, $shopifyshop, $settings);

                BufferLog::Debug(' Recovering  ... OK');
            }


            #-----------------------------------------------------------------------------------------------------------
            # CREA EL CARRIER PARA CALCULAR EL RATE DEL SHIPPING
            #-----------------------------------------------------------------------------------------------------------
            BufferLog::Debug(' Creating carrier');
            $carrier = $this->registerCarrier($shop_id);

            if ( $carrier  ){
                 $store->STORE_CARRIER_ID = $carrier->body->carrier_service->id;
                 $store->save();
                BufferLog::Debug(' carrier id -> '.$carrier->body->carrier_service->id);
                BufferLog::Debug(' Creating carrier was successfuly - saved data base');
            }


            #-----------------------------------------------------------------------------------------------------------
            # CREA EL SCRIPT TAG
            #-----------------------------------------------------------------------------------------------------------
            BufferLog::Debug(' Creating script tag');
            $scriptTag  = $this->registerScriptTags($shop_id);

            if ( $scriptTag  ){
                $store->STORE_SCRIPTTAG = $scriptTag->body->script_tag->id;
                $store->save();

                BufferLog::Debug(' ScriptTag id -> '.$scriptTag->body->script_tag->id);
                BufferLog::Debug(' ScriptTag was successfuly - saved data base');
            }


            #-----------------------------------------------------------------------------------------------------------
            # CREA EL SNIPPET PLANTILLA LIQUID
            #-----------------------------------------------------------------------------------------------------------
            $theme_def   = $this->getThemesDefault();
            //$snipedAsset  = $this->registerAsset($shop_id);
            //$this->updateTheme($theme_def);


            try
            {
                Mail::to('josepalonzoalonzo@icloud.com')->queue(new AppInstalled($shopifyshop));

            }catch (Exception $e)
            {
                Log::critical('Sending Mail failed : ' .$e->getMessage() );
            }


        }
        catch (Exception $e)
        {
            Log::critical('onInstall Execpcion ocurred', [$e]);
            BufferLog::Debug('onInstall Execpcion ocurred -> '.$e->getMessage());
        }

    }


    /**
     * @param $shop
     * @param $shop_id
     * @return bool
     */
    public function onUnInstall($shop, $shop_id){


        try
        {
            $store  = EMVexstore::find($shop_id);
            if (!$store){
                return false;
            }

            if ( $store ) {

                if($store->STORE_INSTALLED == 0) {
                    return true;
                }

                if($store->STORE_INSTALLED == 1) {
                    $store->STORE_INSTALLED = 0;
                    $store->save();
                }


            }

            #-----------------------------------------------------------------------------------------------------------
            # MARCA COMO INSTALADA
            #-----------------------------------------------------------------------------------------------------------
            $store->STORE_INSTALLED       = 0;
            $store->STORE_UNINSTALED      = 1;
            $store->STORE_UNINSTALED_DATE = \DB::raw('now()');
            $store->save();


            #-----------------------------------------------------------------------------------------------------------
            # BORRAR LOS SETTINGS
            #-----------------------------------------------------------------------------------------------------------
            $settings = new ShopifySettingsController();
            $settings->clearSettings($shop_id);

            #-----------------------------------------------------------------------------------------------------------
            # BORRAR LOS METAS
            #-----------------------------------------------------------------------------------------------------------
            //EMProductmetadata::where('PROD_SHOP', $shop->id)->delete();



        }
        catch (Exception $e)
        {

        }


        return true;


    }







    /**
     * Register CarrierService For Calculate Rates
     * @param $store_id
     * @return array
     */
    public function registerCarrier($store_id){
        $shop = ShopifyApp::shop();

        $data = array(
            'carrier_service' => array(
                "name"              => "Glovo Delivery Shipping Rate", //
                "callback_url"      => env('SHOPIFY_APP_URL')."/carrier/rate", //"https://dev.acdnomina.com.mx/api/carrier/rate", //env('SHOPIFY_APP_URL').
                "format"            => "json",
                "carrier_service_type"  => "api",
                "service_discovery" => "true",
                "active"            => "true"
            )
        );

        $carrierServices = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/carrier_services.json');
        $carrierServices = $carrierServices->body->carrier_services;

        foreach($carrierServices as $carrierService){
            if($carrierService->name == "Glovo Delivery Shipping Rate"){
                $id = $carrierService->id;
            }
        }
        try{
            if( isset($id) ){
                $response = $shop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/carrier_services/{$id}.json");
            }
        }
        catch(Exception $e){
            BufferLog::Debug(' Someting wrong  when I delete a carrier error number 182 ');
        }

        BufferLog::Debug(' Creating Carrier  ... request data -> ' . print_r( $data, true));


        $carrier = $shop->api()->rest('POST', '/admin/api/'.\Config('shopify-app.api_version').'/carrier_services.json', $data);



        if ( $carrier->errors){
            BufferLog::Debug(' Someting wrong  ... carrier  -> ' . print_r( $carrier->errors, true));
            Log::critical('Error al crear el carrier', [$carrier]);
            return false;

        }

        BufferLog::Debug(' Crearing Carrier  ... created ok -> ' . print_r( $carrier, true));
        Log::info('Carrier created', [$carrier]);

        return $carrier;

    }




    /**
     * Gets the scripts present in the shop.
     *
     * @return array
     */
    public function getScriptTags()
    {

        $shop = ShopifyApp::shop();
        if (!$this->shopScriptTags) {
            $this->shopScriptTags = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/script_tags.json')->body->webhooks;
        }

        return $this->shopScriptTags;
    }



    /**
     * Register ScriptTags
     * @param $store_id
     * @return bool
     */
    public function registerScriptTags($store_id){

        $shop = ShopifyApp::shop();


        //retrive and remove if already exits
        $apiscriptags   = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/script_tags.json");
        $scriptags        = collect($apiscriptags->body->script_tags)->keyBy('id');

        $deleted = [];
        foreach ($scriptags as $scriptag) {
            // This remove the all scripts tags
            $shop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/script_tags/{$scriptag->id}.json");
            $deleted[] = $scriptag;
        }


        //register the script tags
        $scripttag = array(
            'script_tag' => array(
                "event"    => "onload", //
                "src"      => env('SHOPIFY_APP_URL')."/assets/js/glovo/glovodelivery-v2.0.min.js",
            )
        );

        BufferLog::Debug(' Crearing ScripTag  ... data -> ' . print_r( $scripttag, true));

        $script = $shop->api()->rest('POST', '/admin/api/'.\Config('shopify-app.api_version').'/script_tags.json', $scripttag);
        BufferLog::Debug(' Shopify api response  ... scripttag  -> ' . print_r( $script->body, true));

        if ( $script->errors){
            BufferLog::Debug(' Someting wrong  ... carrier  -> ' . print_r( $script->errors, true));
            return false;
        }

        return $script;

    }



    /**
     * Deletes script in the shop tied to the app.
     *
     * @return array
     */
    public function deleteScriptTags()
    {
        $shop = ShopifyApp::shop();
        $shopScripts = $this->getScriptTags();

        $deleted = [];
        foreach ($shopScripts as $script) {
            // Its a script in the config, delete it
            $shop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/script_tags/{$script->id}.json");
            $deleted[] = $script;
        }

        // Reset
        $this->shopScriptTags = null;

        return $deleted;
    }


    public function registerMetaData($store_id){
        $shop = ShopifyApp::shop();
        $metafield = array(
            'metafield' => array(
                "namespace" => "glovo_shipping",
                "key"       => "apikey",
                "value"     => "onload",
                "value_type"=> "string",
            )
        );

        $metafield    = $shop->api()->rest('POST', '/admin/api/'.\Config('shopify-app.api_version').'/metafields.json', $metafield);

        if ( $metafield->errors){
            return false;
        }

        return $metafield;

    }


    /**
     * @return mixed
     */
    public function registerAsset(){


        $shop = ShopifyApp::shop();
        $qasset = array(
            'asset' => array(
                "key"    => "snippets/snippet-glovo-delivery-cart.liquid", //
                "value"  => "
{% assign total_weight = 0 %}
{% for item  in cart.items %}
	{% assign total_quantity = item.quantity | times: item.grams %}
    {% assign total_weight   = total_weight | plus: total_quantity %}
{% endfor %}


{% assign glovo_shipping_available_items = false %}
{% assign estimate_time = 0 %}
{% for item in cart.items %}

  {% if item.product.metafields.glovo_shipping.available_for_glovo == 'true' %}
       {% assign glovo_shipping_available_items = true %}
  {% endif %}

  {% if item.product.metafields.glovo_shipping.preparation_time != '' %}
    {% assign preparation_time = item.product.metafields.glovo_shipping.preparation_time | abs %}
    {% if preparation_time > estimate_time %}
       {% assign estimate_time =  preparation_time %}
    {% endif %}
  {% endif %}
{% endfor %}


  


<script>
  
  var glovo_setting    = {{shop.metafields.glovo_shipping.settingid}};
  var glovo_apikey     = \"{{shop.metafields.glovo_shipping.gloogleapikey}}\";
  var glovo_workindays = {{shop.metafields.glovo_shipping.workingdays}};
  var glovo_totalweight= \"{{ cart.total_weight  | weight_with_unit: 'kg'}}\";
  
</script>


{% if glovo_shipping_available_items == true  and cart.total_weight  < 9000  %} 
  <div id=\"glovo-shpping-delivery\" style=\"display:none\">
    <div id=\"glovo-loading\">Finding available delivery times...</div>
    <hr>
    <h2 style=\"color: rgba(255,194,68,.9);text-shadow: 0px 0px #000000;\">
    	".env('SHOPIFY_APP_NAME')."
    	<img src=\"".env("SHOPIFY_APP_URL")."/assets/images/icons/shipping_glovo.png\">
	</h2>
    {% if estimate_time > 0 %}
       {% assign estimate_hours = estimate_time | divided_by : 60 %}
       {% assign estimate_hours = estimate_hours | floor   %}
       
      
       {% if estimate_hours > 0 %}
          {% assign estimate_min = estimate_time | modulo:60 %}
          {% assign estimate_min = estimate_min | floor   %}
          
           <div style=\"color:#2abb9b\"> The order will take approximately <b>{{ estimate_hours }}:{{ estimate_min }}</b> hours to prepare</div>
       {% else %}
    <div style=\"color:#2abb9b\"> <i>The order will take approximately <b>{{ estimate_time }}</b> min to prepare</i></div>
       {% endif%} 
    
       
    {% endif %}
    <div id=\"glovo-shpping-delivery-form-wrapper\" class=\"clearfix\">
        <p>One or more of the items in your cart are available for Glovo delivery service.<p>
        
        {% if shop.metafields.glovo_shipping.allowscheduled == 'no' %}
        
        <div class='glovo-info'> Choose glovo in the next step to receive your order!. </div>
        
        {% else %}
        <div class='glovo-pikcup-settings'>
            <p>When would you like to receive your order?</p>
            <input type=\"hidden\" id=\"glovo_when_receive\" name=\"attributes[glovo_when_receive]\">
            <div> <input type=\"checkbox\" class=\"jtoggler\" data-jtlabel=\"Choose when you want to receive your order.\" data-jtlabel-success=\"Send as soon as possible\" checked value=\"immediately\"> </div>
            <div class=\"select-schedule uk-grid\">
                <div class=\"uk-width-medium-5-10\">
                    <p>Day: <select name=\"attributes[glovo_schedule_day]\" id=\"glovo_schedule_day\" style=\"width: 220px\"></select></p> 
                </div>
                <div class=\"uk-width-medium-5-10\">
                    <p>Hour: <select name=\"attributes[glovo_schedule_time]\" id=\"glovo_schedule_time\" style=\"width: 220px\"></select></p> 
                </div>
            </div>
        </div>
        {% endif %}
    </div>

    <div id='glovo-map' style=\"height: 400px\" class=\"glovo-map\"></div>

    <div id=\"wrapper-response\"></div>


  </div>
{% endif %}
",
            )
        );

        $theme = $this->getThemesDefault();
        $asset = $shop->api()->rest('PUT', "/admin/api/".\Config('shopify-app.api_version')."/themes/{$theme->id}/assets.json", $qasset);

        return $asset;


    }


    /**
     * @param $theme
     * @param bool $forcechange
     * @return array
     */
    public function updateTheme ($theme, $forcechange = true){

        Log::info("-----------------------------------------------");
        Log::info("|    UPDATE TEMPLATE                           |");
        Log::info("-----------------------------------------------");


        $needChange     = false;
        $template       = "";
        $newTemplate    = "";

        $shop = ShopifyApp::shop();

        // get cart theme assets
        $assetData      = $shop->api()->rest('GET', '/admin/api/2019-04/themes/48583147574/assets.json', ['theme_id'=>$theme->id, 'asset[key]'=>'templates/cart.liquid']);

        if ( $assetData->body )
        {
            $template = $assetValue = $assetData->body->asset->value;
            Log::info($assetValue);
            Log::info("\n\n");

            libxml_use_internal_errors(true);
            $dom    = new \DOMDocument('1.0', 'UTF-8');
            $dom->substituteEntities    = false;
            $dom->encoding              = 'UTF-8';
            $dom->formatOutput          = true;
            $dom->resolveExternals      = false;


            $dom->loadHTML($assetValue); // loads your HTML
            $xpath  = new \DOMXPath($dom);

            //find the section snippet
            $snippet  = $xpath->query("//section[@id='snippet-glovo-delivery']");
            if ($snippet->length) {
                Log::info("Previus snippet-glovo-delivery finded");
                Log::info("data", ['snippet'=>$snippet]);

                $snippet   = $snippet->item(0);
                $snippet->parentNode->removeChild($snippet);
                $assetValue = $dom->saveHTML();
            }

            #replace only snippet without tags
            $assetValue     = preg_replace('/{% include \'snippet-glovo-delivery-cart\' %}/i', '', $assetValue);  # Replace the previus snippet
            $dom    = new \DOMDocument('1.0', 'UTF-8');
            $dom->substituteEntities    = false;
            $dom->encoding              = 'UTF-8';
            $dom->formatOutput          = true;
            $dom->resolveExternals      = false;

            $dom->loadHTML($assetValue); // loads your HTML
            $xpath  = new \DOMXPath($dom);

            // returns a list of all divs with class='rte'
            $targetNode  = $xpath->query("//textarea[@name='note']");
            if ($targetNode->length) {
                Log::info("target ---> textarea[@name='note'] -> finded");

                $needChange = true;
                $textarea   = $targetNode->item(0);
                $newNode    = $dom->createElement('section', '{% include \'snippet-glovo-delivery-cart\' %}');
                $newNode->setAttribute('id','snippet-glovo-delivery');

                //insert after textarea
                if ($textarea->nextSibling){
                    $textarea->parentNode->insertBefore($dom->createTextNode("\n"), $textarea->nextSibling);
                    $textarea->parentNode->insertBefore($newNode, $textarea);
                    $textarea->parentNode->insertBefore($dom->createTextNode("\n"), $textarea->nextSibling);

                }
                $textarea->parentNode->appendChild($dom->createTextNode("\n"));
                $textarea->parentNode->appendChild($newNode);
                $textarea->parentNode->appendChild($dom->createTextNode("\n"));

                Log::info("target ---> after -> " . $textarea->parentNode->nodeValue);
            }

            $newTemplate  = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $dom->saveHTML( $dom->documentElement)));
            $newTemplate  = html_entity_decode($dom->saveHTML($dom->documentElement));

            if ( $needChange == true && $forcechange){
                $params = array('asset' => array('key' => 'templates/cart.liquid', 'value' => $newTemplate));
                $shop->api()->rest('PUT', "/admin/api/".\Config('shopify-app.api_version')."/themes/{$theme->id}/assets.json", $params);
            }
        }

        return array ('original'=> $template , 'modified'=> $newTemplate, 'needchange'=>$needChange);

    }




    /**
     * Gets the webhooks present in the shop.
     *
     * @return array
     */
    public function getThemes()
    {
        $shop = ShopifyApp::shop();

        if (!$this->shopThemes) {
            $this->shopThemes = $shop->api()->rest(
                'GET',
                '/admin/api/'.\Config('shopify-app.api_version').'/themes.json',
                [
                    'limit'  => 250,
                    'fields' => 'id,name,role',
                ]
            )->body->themes;
        }

        return $this->shopThemes;
    }

    /**
     * Check the theme defaults.
     *
     * @param array $themes with The Themes
     *
     * @return bool
     */
    public function getThemesDefault()
    {
        $shopThemes = $this->getThemes();
        foreach ($shopThemes as $theme) {
            if ($theme->role == 'main') {
                // Found the role in our list
                return $theme;
            }
        }

        return false;
    }


    /**
     * @param $carrier_service_id
     */
    public function removeCarrier($carrier_service_id){

        $shop = ShopifyApp::shop();
        $request = $shop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/carrier_services/{$carrier_service_id}.json");

    }



    public function webhooklist(){

        $apishop        = ShopifyApp::shop();
        $wbs = $apishop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/webhooks.json');
        dd($wbs);



    }


    public function ScriptList(){

        $apishop        = ShopifyApp::shop();

        $apishop->api()->rest('DELETE', '/admin/api/'.\Config('shopify-app.api_version').'/script_tags/40440922166.json');
        $apishop->api()->rest('DELETE', '/admin/api/'.\Config('shopify-app.api_version').'/script_tags/40463237174.json');
        $scripts = $apishop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/script_tags.json');
        dd($scripts);



    }



    public function MetadataList(){

        $apishop        = ShopifyApp::shop();
        $apimetafields  = $apishop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/metafields.json");
        $metafields     = collect($apimetafields->body->metafields)->keyBy('key');

        dd($apimetafields);



    }


    public function webhookcreate(){

        $apishop        = ShopifyApp::shop();


        $webhookdata = array(
            'webhook' => array(
                "topic"     => "products/create",
                "address"   => env('SHOPIFY_APP_URL')."/webhooks/products",
                "format"   => "json",
            )
        );


        $wbs = $apishop->api()->rest('POST', '/admin/api/'.\Config('shopify-app.api_version').'/webhooks.json',$webhookdata);
        dd($wbs);



    }



    public function AIToken(){


        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://34.66.29.197',
            'headers'  => ['Content-Type'=> 'application/json']
        ]);
        $response = $client->request('POST', '/api/api-token-auth/', ['json' => ['username' => 'admin', 'password'=>"secure_password"]]);

        $token = (json_decode($response->getBody()))->token;


        $data_product = [
            'product_id' => 'admin',
            'product_image_main_url'=>"secure_password"
        ];
        $headers = [
            'Authorization' => 'Token ' . $token,
            'Accept'        => 'application/json',
        ];
        $requestProd = $client->request('POST', '/api/product/create/', ['json' => $data_product, 'headers' => $headers]);

    }



    public function ThemeList(){

        $shop = ShopifyApp::shop();
        //$request = $shop->api()->rest('GET', '/admin/api/".\Config('shopify-app.api_version')."/themes.json');
        $theme = $this->getThemesDefault();
        $response = $this->updateTheme($theme, false);



    }



    public function command(){

        $comand = 'cd '.base_path().'
        composer dump-autoload -o';
        $process = new Process($comand);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();

    }







}
