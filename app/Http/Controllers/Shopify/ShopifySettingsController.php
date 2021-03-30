<?php

namespace App\Http\Controllers\Shopify;

use App\Http\Controllers\Glovo\GlovoController;
use App\Mail\onServiceTest;
use App\Models\EMVexlanguages;
use App\Models\EMVexstore;
use App\Models\Shopify\EMMetadata;
use App\Models\Shopify\EMOrderstatus;
use App\Models\Shopify\EMProductmetadata;
use App\Models\Store\EMVexhollyday;
use App\Models\Store\EMVexhours;
use App\Models\Store\EMVexlocations;
use Carbon\Carbon;
use Escom\Base\CBase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Utils\Dates;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use App\Models\EMVexsetting;
use App\Http\Controllers\Shopify\ShopifyApplication as ShopApplication;

use Exception;
use Utils\UStore;

class ShopifySettingsController extends CBase
{

    /**
     * file to log
     * @var string
     */
    public $fileToLog;


    public function __construct()
    {

        $carbonNow          = Carbon::now('America/Mexico_City');
        $this->fileToLog    = storage_path("logs/orders/".$carbonNow->format('Y-m-d') .".log");

    }


    /**
     * Index
     * @return \Illuminate\Contracts\View\View
     */
    public function index(){

        $baseurl    = env('SHOPIFY_APP_URL');
        $route      = env('SHOPIFY_APP_URL')."/settings/save";
        $apishop    = ShopifyApp::shop();
        $shop       = $apishop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json');
        $store      = $shop->body->shop;

        $settings   = EMVexsetting::findByStoreId($store->id);
        if (is_null($settings)){

            $default_lang   = UStore::DefLang($store);
            $settings       = new EMVexsetting();
            $settings->setLanguage($default_lang);

        }
        $emstore    = EMVexstore::findByStoreId($store->id);

        app('translator')->setLocale($settings->getLanguage());
        $languages      = EMVexlanguages::select("*")->pluck('LANG_NAME', 'LANG_CODE')->toArray();
        $locations      = $this->adapterStoreLocations($store, $store->id);
        $workinghours   = $this->adapterHours($settings->getId(), $store->id );
        $hollydays      = $this->adapterHollydays($settings->getId(), $store->id );
        $orderstatus    = EMOrderstatus::select("*")->where('enabled',1)->pluck('description', 'status')->toArray();

        $months         = collect(Dates::getMonths())->pluck('text','id');
        $days           = collect(Dates::getDays())->pluck('text','id');

        
        return View::make('shopify.settings.settings')
            ->with('route'      , $route)
            ->with('baseurl'    , $baseurl)
            ->with('store'      , $store)
            ->with('emstore'     , $emstore)
            ->with('languages'  , $languages)
            ->with('orderstatus', $orderstatus)
            ->with('locations'  , $locations)
            ->with('workinghours', $workinghours)
            ->with('hollydays'  , $hollydays)
            ->with('months'     , $months)
            ->with('days'       , $days)
            ->with('settings'   , $settings);

    }


    /**
     * @param $request
     * @param array $data
     */
    public function save($request, $data=[]){

        try
        {
            $apishop        = ShopifyApp::shop();
            $shop           = $apishop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json');
            $apistore       = $shop->body->shop;

            $data           = Input::all();
            $postlocations  = isset($data['locations']) ? $data['locations'] : [];
            $workingdays    = ($data['days']);
            $hollydays      = isset($data['hollydays']) ? $data['hollydays'] : [];
            $allowscheduled = (isset($data['SETT_ALLOWSCHEDULED'])) ? 1 : 0;
            $createStatus   = $data['SETT_CREATE_STATUS'];

            $emstore        = EMVexstore::findByStoreId($apistore->id);
            $emstore->STORE_TIMEZONE      = $apistore->timezone;
            $emstore->STORE_IANA_TIMEZONE = $apistore->iana_timezone;
            $emstore->save();

            if(is_null($emstore->STORE_CARRIER_ID)){
                $shopApp = new ShopApplication();
                $carrier = $shopApp->registerCarrier($apistore->id);
    
                if ( $carrier  ){
                    $emstore->STORE_CARRIER_ID = $carrier->body->carrier_service->id;
                    $emstore->save();
                }
            }


            if (!($setting = EMVexsetting::findByStoreId($apistore->id)))
            {
                $setting = new EMVexsetting();
            }

            $setting->setStoreId( $apistore->id)
                ->setStoreName( $apistore->name )
                ->setLanguage( $data['SETT_LANGUAGE'] )
                ->setEnable( 1) //isset($data['SETT_ENABLE']) ? 1 : 0
                ->setServer($data['SETT_SERVER'])
                ->setGlovoApi($data['SETT_GLOVO_API'])
                ->setGlovoSecret($data['SETT_GLOVO_SECRET'])
                ->setGoogleApiKey($data['SETT_GOOGLE_API'])
                ->setMethodTitle($data['SETT_METHOD_TITLE'])
                ->setCostType($data['SETT_COST_TYPE'])
                ->setCostDefault($data['SETT_COST_DEFAULT'])
                ->setAllowScheduled($allowscheduled)
                ->setCreateStatus($createStatus)
                ->save();


            app('translator')->setLocale($setting->getLanguage());

            #validated setting
            $valitation     = $this->_validateConfig($setting, $postlocations);
            if ($valitation['success'] == true){
                $setting->setValidated(1)->save();
            }else {
                $setting->setValidated(0)->save();
            }
            #save detail
            $trans = $this->saveLocations($setting->getId(), $apistore->id, $postlocations);

            #save working days
            $trans = $this->saveHorarios($setting->getId(), $apistore->id, $workingdays);

            #save hollydays
            $trans = $this->saveHollydays($setting->getId(), $apistore->id, $hollydays);


            #save meta data on store
            $meta  = $this->saveMetadata($setting);

            return Redirect::route('shopify.settings')->with("success", __('glovo.settings.save.success'));


        }
        catch (Exception $e)
        {
            Log::error("SettingController->save : ", [$e->getMessage()]);
        }


        return Redirect::route('shopify.settings')->with("myerror", __('glovo.settings.save.failed'));

    }



    public function saveLocations ($setting, $storeid, $postlocations=[]){

        if(!is_array($postlocations))
        {
            return true;
        }

        $size   = sizeof($postlocations);

        foreach ($postlocations as $postlocation)
        {
            $idlocation    = $postlocation['id'];
            $enabled       = isset($postlocation['enable']) ? 1 : 0;
            if (!$location = EMVexlocations::find($idlocation))
            {
                $location  = new EMVexlocations();
            }

            $location->setSetting($setting);
            $location->setEnable($enabled);
            $location->setId($idlocation); //location ID
            $location->setStore($storeid); //Store ID
            $location->setLat(@$postlocation['lat']);
            $location->setLng(@$postlocation['lng']);
            $location->setCountry(@$postlocation['country']);
            $location->setProvince(@$postlocation['province']);
            $location->save();

        }

        return true;

    }


    /**
     * @param $setting
     * @param $storeid
     * @param array $workingdays
     * @return bool
     */
    public function saveHorarios($setting, $storeid, $workingdays=[]){


        try
        {
            #truncate
            EMVexhours::where('STHR_SETTING', $setting)->delete();

            #create
            foreach ($workingdays as $key=>$day){

                $emhours    = new EMVexhours();
                $emhours->STHR_SETTING  = $setting;
                $emhours->STHR_DAY      = $key;

                if ( isset($day['enabled']) )
                {
                    $emhours->STHR_ENABLED  = 1;
                    $emhours->STHR_OPEN     = $day['open'];
                    $emhours->STHR_CLOSE    = $day['close'];
                }
                else
                {
                    $emhours->STHR_ENABLED  = 0;
                    $emhours->STHR_OPEN     = DB::raw("NULL");
                    $emhours->STHR_CLOSE    = DB::raw("NULL");
                }

                $emhours->save();
            }

            return true;


        }
        catch (Exception $e)
        {

        }


        return true;

    }


    /**
     * @param $setting
     * @param $storeid
     * @param array $workingdays
     * @return bool
     */
    public function saveHollydays($setting, $storeid, $hollydays=[]){


        try
        {
            #truncate
            EMVexhollyday::where('HODAY_SETTING', $setting)->delete();

            #create
            for($c=0; $c < sizeof($hollydays['day']); $c++)
            {
                $emholly    = new EMVexhollyday();
                $emholly->HODAY_SETTING  = $setting;
                $emholly->HODAY_DAY      = $hollydays['day'][$c];
                $emholly->HODAY_MONTH    = $hollydays['month'][$c];
                $emholly->save();
            }

        }
        catch (Exception $e)
        {

        }


        return true;

    }


    /**
     * @param EMVexsetting $emsetting
     * @return bool
     */
    public function saveMetadata(EMVexsetting $emsetting){

        $apishop        = ShopifyApp::shop();
        $apimetafields  = $apishop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/metafields.json");
        $metafields     = collect($apimetafields->body->metafields)->keyBy('key');

        #---------------------------------------------------------------------------------------------------------------
        # SETTINGS
        #---------------------------------------------------------------------------------------------------------------
        if (is_null($meta =  $metafields->get('settingid'))  )
        {
            $metadata = array(
                'metafield' => array(
                    "namespace" => "glovo_shipping",
                    "key"       => "settingid",
                    "value"     => $emsetting->getId(),
                    "value_type"=> "string",
                )
            );

            $apirest    = $apishop->api()->rest('POST',"/admin/api/".\Config('shopify-app.api_version')."/metafields.json", $metadata);
            $metafield  = $apirest->body->metafield;

        }else {

            $metadata = array(
                'metafield' => array(
                    "id"    => $meta->id,
                    "value" => $emsetting->getId(),
                    "value_type" => "string",
                )
            );

            $apirest = $apishop->api()->rest('PUT', "/admin/api/".\Config('shopify-app.api_version')."/metafields/{$meta->id}.json", $metadata);
            $metafield  = $apirest->body->metafield;

        }

        EMMetadata::where("META_STORE", $emsetting->getStoreId() )->where("META_KEY",'settingid')->delete();
        $emmetafield = new EMMetadata();
        $emmetafield->META_ID                = $metafield->id;
        $emmetafield->META_SETTING           = $emsetting->getId();
        $emmetafield->META_STORE             = $emsetting->getStoreId();
        $emmetafield->META_KEY               = $metafield->key;
        $emmetafield->META_VALUE             = $metafield->value;
        $emmetafield->META_TYPE              = $metafield->value_type;
        $emmetafield->META_OWNERID           = $metafield->owner_id;
        $emmetafield->META_OWNER_RESOURCE    = $metafield->owner_resource;
        $emmetafield->META_ADMIN_GRAPHQL     = $metafield->admin_graphql_api_id;
        $emmetafield->META_CREATED_AT        = Carbon::parse($metafield->created_at)->format('Y-m-d H:i:s');
        $emmetafield->save();



        #---------------------------------------------------------------------------------------------------------------
        # ALLOW SCHEDULE ORDERS
        #---------------------------------------------------------------------------------------------------------------
        if (is_null($meta =  $metafields->get('allowscheduled'))  )
        {
            $metadata = array(
                'metafield' => array(
                    "namespace" => "glovo_shipping",
                    "key"       => "allowscheduled",
                    "value"     => $emsetting->getAllowScheduled()  ? 'yes' : 'no',
                    "value_type"=> "string",
                )
            );

            $apirest    = $apishop->api()->rest('POST',"/admin/api/".\Config('shopify-app.api_version')."/metafields.json", $metadata);
            $metafield  = $apirest->body->metafield;

        }else {

            $metadata = array(
                'metafield' => array(
                    "id"    => $meta->id,
                    "value" => $emsetting->getAllowScheduled() ? 'yes' : 'no',
                    "value_type" => "string",
                )
            );

            $apirest = $apishop->api()->rest('PUT', "/admin/api/".\Config('shopify-app.api_version')."/metafields/{$meta->id}.json", $metadata);
            $metafield  = $apirest->body->metafield;

        }

        EMMetadata::where("META_STORE", $emsetting->getStoreId() )->where("META_KEY",'settingid')->delete();
        $emmetafield = new EMMetadata();
        $emmetafield->META_ID                = $metafield->id;
        $emmetafield->META_SETTING           = $emsetting->getId();
        $emmetafield->META_STORE             = $emsetting->getStoreId();
        $emmetafield->META_KEY               = $metafield->key;
        $emmetafield->META_VALUE             = $metafield->value;
        $emmetafield->META_TYPE              = $metafield->value_type;
        $emmetafield->META_OWNERID           = $metafield->owner_id;
        $emmetafield->META_OWNER_RESOURCE    = $metafield->owner_resource;
        $emmetafield->META_ADMIN_GRAPHQL     = $metafield->admin_graphql_api_id;
        $emmetafield->META_CREATED_AT        = Carbon::parse($metafield->created_at)->format('Y-m-d H:i:s');
        $emmetafield->save();



        #---------------------------------------------------------------------------------------------------------------
        # GOOGLE APIKEY
        #---------------------------------------------------------------------------------------------------------------
        if (is_null($meta =  $metafields->get('gloogleapikey'))  )
        {
            $metadata = array(
                'metafield' => array(
                    "namespace" => "glovo_shipping",
                    "key"       => "gloogleapikey",
                    "value"     => !empty($emsetting->getGoogleApiKey()) ? $emsetting->getGoogleApiKey() : "X",
                    "value_type"=> "string",
                )
            );

            $apirest    = $apishop->api()->rest('POST',"/admin/api/".\Config('shopify-app.api_version')."/metafields.json", $metadata);
            $metafield  = $apirest->body->metafield;

        }else {

            $metadata = array(
                'metafield' => array(
                    "id"    => $meta->id,
                    "value" => !empty($emsetting->getGoogleApiKey()) ? $emsetting->getGoogleApiKey() : "X",
                    "value_type" => "string",
                )
            );

            $apirest = $apishop->api()->rest('PUT', "/admin/api/".\Config('shopify-app.api_version')."/metafields/{$meta->id}.json", $metadata);
            $metafield  = $apirest->body->metafield;

        }

        EMMetadata::where("META_STORE", $emsetting->getStoreId() )->where("META_KEY",'gloogleapikey')->delete();
        $emmetafield = new EMMetadata();
        $emmetafield->META_ID                = $metafield->id;
        $emmetafield->META_SETTING           = $emsetting->getId();
        $emmetafield->META_STORE             = $emsetting->getStoreId();
        $emmetafield->META_KEY               = $metafield->key;
        $emmetafield->META_VALUE             = $metafield->value;
        $emmetafield->META_TYPE              = $metafield->value_type;
        $emmetafield->META_OWNERID           = $metafield->owner_id;
        $emmetafield->META_OWNER_RESOURCE    = $metafield->owner_resource;
        $emmetafield->META_ADMIN_GRAPHQL     = $metafield->admin_graphql_api_id;
        $emmetafield->META_CREATED_AT        = Carbon::parse($metafield->created_at)->format('Y-m-d H:i:s');
        $emmetafield->save();



        #---------------------------------------------------------------------------------------------------------------
        # WORKING DAYS
        #---------------------------------------------------------------------------------------------------------------
        $getArrayWorkingDays = $this->getArrayWorkingDays($emsetting->getId());
        if (is_null($meta =  $metafields->get('workingdays'))  )
        {
            $metadata = array(
                'metafield' => array(
                    "namespace" => "glovo_shipping",
                    "key"       => "workingdays",
                    "value"     => json_encode($getArrayWorkingDays),
                    "value_type"=> "string",
                )
            );

            $apirest    = $apishop->api()->rest('POST',"/admin/api/".\Config('shopify-app.api_version')."/metafields.json", $metadata);
            $metafield  = $apirest->body->metafield;

        }else {

            $metadata = array(
                'metafield' => array(
                    "id"    => $meta->id,
                    "value" => json_encode($getArrayWorkingDays),
                    "value_type" => "string",
                )
            );

            $apirest = $apishop->api()->rest('PUT', "/admin/api/".\Config('shopify-app.api_version')."/metafields/{$meta->id}.json", $metadata);
            $metafield  = $apirest->body->metafield;

        }

        EMMetadata::where("META_STORE", $emsetting->getStoreId() )->where("META_KEY",'workingdays')->delete();
        $emmetafield = new EMMetadata();
        $emmetafield->META_ID                = $metafield->id;
        $emmetafield->META_SETTING           = $emsetting->getId();
        $emmetafield->META_STORE             = $emsetting->getStoreId();
        $emmetafield->META_KEY               = $metafield->key;
        $emmetafield->META_VALUE             = $metafield->value;
        $emmetafield->META_TYPE              = $metafield->value_type;
        $emmetafield->META_OWNERID           = $metafield->owner_id;
        $emmetafield->META_OWNER_RESOURCE    = $metafield->owner_resource;
        $emmetafield->META_ADMIN_GRAPHQL     = $metafield->admin_graphql_api_id;
        $emmetafield->META_CREATED_AT        = Carbon::parse($metafield->created_at)->format('Y-m-d H:i:s');
        $emmetafield->save();


        return true;



    }


    /**
     * @param $apishop
     */
    public function deleteMetadata($apishop)
    {
        $apimetafields = $apishop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/metafields.json");
        if ($apimetafields->errors == false)
        {
            $metafields = collect($apimetafields->body->metafields)->keyBy('key');

            if ($meta =  $metafields->get('settingid')){
                $apishop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/metafields/{$meta->id}.json");
            }

            if ($meta =  $metafields->get('allowscheduled')){
                $apishop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/metafields/{$meta->id}.json");
            }

            if ($meta =  $metafields->get('gloogleapikey')){
                $apishop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/metafields/{$meta->id}.json");
            }

            if ($meta =  $metafields->get('workingdays')){
                $apishop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/metafields/{$meta->id}.json");
            }
        }
    }


    /**
     * @param $storeid
     * @return \Illuminate\Support\Collection
     */
    public function adapterStoreLocations($shopifystore, $storeid){

        $stores = [];
        try
        {
            $shop           = ShopifyApp::shop();


            //primary location
            $reqLocations   = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/locations.json');
            $locations      = $reqLocations->body->locations;


            foreach ($locations as $location){

                if ($location->id == $shopifystore->primary_location_id)
                {
                    $storelocation = new EMVexlocations();
                    $storelocation->STLO_STOREID        = $storeid;
                    $storelocation->STLO_ID             = $location->id;
                    $storelocation->STLO_NAME           = $location->name;
                    $storelocation->STLO_CITY           = $location->city;
                    $storelocation->STLO_ADDRESS1       = $location->address1;
                    $storelocation->STLO_ADDRESS2       = $location->address2;
                    $storelocation->STLO_POSTCODE       = $location->zip;
                    $storelocation->STLO_PHONE          = $location->phone;
                    $storelocation->STLO_PROVINCE       = $location->province;
                    $storelocation->STLO_PROVINCE_CODE  = $location->province_code;
                    $storelocation->STLO_COUNTRY        = $location->country;
                    $storelocation->STLO_COUNTRY_CODE   = $location->country_code;
                    $storelocation->STLO_COUNTRY_NAME   = $location->country_name;
                    $storelocation->LAT_ATTRIBUTES      = ['id'=>'lat', 'class'=>'disabled', 'readonly'=>'readonly'];
                    $storelocation->LNG_ATTRIBUTES      = ['id'=>'lng', 'class'=>'disabled', 'readonly'=>'readonly'];


                    #Exits on local
                    $emlocal    = EMVexlocations::find( $location->id );
                    if ( $emlocal )
                    {
                        $storelocation->STLO_LAT        = $emlocal->STLO_LAT;
                        $storelocation->STLO_LNG        = $emlocal->STLO_LNG;
                        $storelocation->STLO_ENABLE     = $emlocal->STLO_ENABLE;

                        if ( $storelocation->STLO_ENABLE == 1)
                        {
                            $storelocation->LAT_ATTRIBUTES      = ['id'=>'lat', 'required'=>'required'];
                            $storelocation->LNG_ATTRIBUTES      = ['id'=>'lng', 'required'=>'required'];

                        }
                    }



                    $stores[]   = $storelocation;
                }
            }



            return collect($stores);


        }catch (Exception $e)
        {

        }

    }


    /**
     * @param $setting
     * @param $storeid
     * @return array
     */
    public function adapterHours($setting, $storeid){

        $days           = [0,1,2,3,4,5,6];
        $workinhours    = [];

        #get working hours
        $settingworkinhours = collect(EMVexhours::where('STHR_SETTING', $setting)->get());


        #if dont have values on setting working days. By default set values
        if (  $settingworkinhours->count() == 0)
        {
            foreach ($days as $day){
                $temp       = [
                    'day'       => $day,
                    'enabled'   => false,
                    'hours'     => [
                        0 => ['open'=> '09:00', 'close'=> '21:00']
                    ],
                    'default' => [
                        0 => ['open'=> '08:00', 'close'=> '21:00']
                    ]
                ];

                if ( $day == 6 ){
                     $temp['default'][0] = ['open'=> '09:00', 'close'=> '20:00'];
                }

                $workinhours[$day] = $temp;
            }
        }
        #have setting values
        else

        {

            foreach ($days as $day){

                $temp       = ['day' => $day];
                $hoursDay   = $settingworkinhours->where('STHR_DAY', $day)->first();

                if ( $hoursDay['STHR_ENABLED'] == 1  ) {
                    $temp['enabled']   = true;
                    $temp['hours'][]   = ['open'=> $hoursDay['STHR_OPEN'], 'close'=> $hoursDay['STHR_CLOSE']];
                    $temp['default'][] = ['open'=> $hoursDay['STHR_OPEN'], 'close'=> $hoursDay['STHR_CLOSE']];
                }else {
                    $temp['enabled']    = false;
                    $temp['hours'][]    = ['open'=> '', 'close'=> ''];
                    $temp['default'][]  = ['open'=> '08:00', 'close'=> '21:00'];
                }

                $workinhours[$day]= $temp;

            }

        }






        return collect($workinhours);



    }


    /**
     *
     */
    public function adapterHollydays($setting, $storeid){

        $hollydays = [];
        #get setting record
        $emsettings = EMVexsetting::find($setting);

        if ( is_null($emsettings)){
            return [];
        }

        #get hollydays
        if ( $emsettings->hollydays()->exists() )
        {

            foreach ($emsettings->hollydays as $day)
            {
                $hollydays[] = [
                    'HODAY_HOLLYDAY'    => $day->HODAY_HOLLYDAY,
                    'HODAY_SETTING'     => $day->HODAY_SETTING,
                    'HODAY_DAY'         => $day->HODAY_DAY,
                    'HODAY_MONTH'       => $day->HODAY_MONTH,
                    'HODAY_CREATED_AT'  => $day->HODAY_CREATED_AT,
                ];
            }
        }

        return collect($hollydays);

    }


    /**
     * Save the first configutarion settings
     * @param $shopifyshop
     */
    public function initialize($shop, $shopifyshop){

        try
        {

            $default_lang   = UStore::DefLang($shopifyshop);
            $emsetting = new EMVexsetting();
            $emsetting->setStoreId( $shopifyshop->id)
                ->setStoreName( $shopifyshop->name )
                ->setLanguage( $default_lang )
                ->setEnable( 1)
                ->setServer('Production')
                ->setGlovoApi("")
                ->setGlovoSecret("")
                ->setGoogleApiKey("AIzaSyB0imoCMF83g1Yxn_USoSXnWXrtzNgt3pA")
                ->setMethodTitle(env("SHOPIFY_SHIPPING_TITLE"))
                ->setCostType("Free")
                ->setCostDefault("0")
                ->setAllowScheduled(1)
                ->setCreateStatus("paid")
                ->save();

            $this->saveMetadata($emsetting);

            #save working days
            $this->initDefaultHours($shop, $shopifyshop, $emsetting);


        }
        catch (Exception $e)
        {
            Log::critical('Error ocurren in controller ShopifySettingsController@initialize', [$e]);
        }


        return $emsetting;
    }


    /**
     * @param $shop
     * @param $shopifyshop
     */
    public function initDefaultHours($shop, $shopifyshop, $emsetting){

        $workingdays = [
            0   => ['enabled'=>1, 'open'=> '08:00', 'close'=>'23:00'],
            1   => ['enabled'=>1, 'open'=> '08:00', 'close'=>'23:00'],
            2   => ['enabled'=>1, 'open'=> '08:00', 'close'=>'23:00'],
            3   => ['enabled'=>1, 'open'=> '08:00', 'close'=>'23:00'],
            4   => ['enabled'=>1, 'open'=> '08:00', 'close'=>'23:00'],
            5   => ['enabled'=>1, 'open'=> '08:00', 'close'=>'23:00'],
            6   => ['enabled'=>1, 'open'=> '08:00', 'close'=>'23:00'],
        ];
        $trans = $this->saveHorarios($emsetting->getId(), $shopifyshop->id, $workingdays);

    }



    /**
     * Softdelete for settings and delete another configuration
     * @param $store_id
     * @return bool
     * @throws Exception
     */
    public function clearSettings ($store_id){

        $emsettings       = EMVexsetting::findByStoreId($store_id);

        //if exists a valid setting
        if ($emsettings)
        {
            if( !$emsettings->trashed()){
                 $emsettings->locations()->delete();
                 $emsettings->workinghours()->delete();
                 $emsettings->hollydays()->delete();
                 $emsettings->metadata()->delete();
                 $emsettings->delete();
            }
        }


        return true;

    }


    /**
     * @usage in cart front-end, rate
     * @param $setting_id
     * @param string $dateservice = Y-m-d H:i:s
     * @param $productlist
     * @return array
     */
    public function isAvalibleService($setting_id, $dateservice, $productlist){
        $response       = [];
        $emsetting      = EMVexsetting::find($setting_id);

        if (is_null($emsetting))
        {
            return $response=['status'=> ['code'=>404, 'message' =>'Not found setting store key']];
        }

        $emstore        = EMVexstore::findByStoreId($emsetting->getStoreId());
        $date           = Carbon::parse($dateservice);
        $date->setTimezone( $emstore->getTimeZone() );

        $workingdays    = collect($this->getArrayWorkingDays($setting_id, $date, $productlist));
        $dayofweek      = $date->dayOfWeek;

        if ($emsetting->getEnable() != 1){
            return $response=['status'=> ['code'=>401, 'message' =>'Not enable glovo delivery']];
        }


        if ($emsetting->getValidated() == false){
            return $response=['status'=> ['code'=>402, 'message' =>'Store address is not valid or outside of glovo area']];
        }

        if ($workingdays->where('enable', true)->count() == 0){
            return $response=['status'=> ['code'=>401, 'message' =>'None day enable for glovo deliverys']];
        }



        return $response=['status'=>['code'=>200,'message'=>'OK'], 'workingdays' => $workingdays];


    }



    /**
     * @param $setting
     */
    public function getWorkinDays($setting){

        $workingdays = [];

        #get working days and hours
        $settingworkindays = EMVexhours::where('STHR_SETTING', $setting)->get();


        foreach ($settingworkindays as $wday){
            $temp       = [
                'day'       => $wday->STHR_DAY,
                'enabled'   => $wday->STHR_ENABLED,
                'hours' => [
                    0 => ['open'=> $wday->STHR_OPEN, 'close'=> $wday->STHR_CLOSE]
                ],
            ];
            $workingdays[$wday->STHR_DAY] = $temp;
        }


        return $workingdays;

    }

    /**
     * @param int $setting_id
     * @param Carbon $date
     */
    public function getArrayWorkingDays($setting_id, $date=null, $productlist=[]){
        $workindays = collect([]);
        $hollydays  = collect([]);
        $days       = [0,1,2,3,4,5,6];

        $emsetting  = EMVexsetting::find($setting_id);
        $emstore    = EMVexstore::findByStoreId($emsetting->getStoreId());

        //translate texts
        app('translator')->setLocale($emsetting->getLanguage());
        Carbon::setLocale('en');

        $settingdays= $this->getWorkinDays($setting_id);

        if ( $emsetting->hollydays()->exists() )
        {
             $hollydays = collect( $emsetting->hollydays()->first()->getHollyDays() );
        }

        if (is_null($date))
        {
            $now        = Carbon::now( $emstore->getTimeZone() );

        }else
        {
            $now        = Carbon::parse( $date->format('Y-m-d H:i:s'), $emstore->getTimeZone() );
        }

        $dayofweek  = $now->dayOfWeek;

        // '0'   => 'Sunday',
        // '1'   => 'Monday',
        // '2'   => 'Tuesday',
        // '3'   => 'Wednesday',
        // '4'   => 'Thursday',
        // '5'   => 'Friday',
        // '6'   => 'Saturday',
        $wdays      = __('glovo.workinghours.days');


        foreach ($days as $day){

            if ($day == 0){
                $immediately        = false;
                $enableday          = isset($settingdays[$now->isoFormat('d')])  ? ( $settingdays[$now->isoFormat('d')]['enabled'] ? true : false) : false;
                //rewrite
                if($hollydays->where('MDAY',$now->format('md'))->count()){
                    $enableday  = false;
                }else
                {
                    //get horarios disponibles
                    $horarios           = $this->getWorkingTimes($setting_id, $now->format('Ymd'), $productlist);
                    if ($horarios['success']==false){
                        $enableday  = false;
                    }

                    $immediately = $horarios['immediately'];
                }


                $workindays[$day]   = ['id' => $now->format('Ymd'), 'enable'=>$enableday, 'immediately'=> $immediately, 'text'=> __('glovo.workinghours.today')];


            }elseif ($day == 1){
                $cday               = $now->copy()->addDays($day);
                $enableday          = isset($settingdays[$cday->isoFormat('d')])  ? ($settingdays[$cday->isoFormat('d')]['enabled'] ? true : false) : false;

                if($hollydays->where('MDAY',$cday->format('md'))->count()){
                    $enableday  = false;
                }

                $workindays[$day]   = ['id' => $cday->format('Ymd'), 'enable'=>$enableday, 'text'=> __('glovo.workinghours.tomorrow')];
            }else {
                $cday               = $now->copy()->addDays($day);
                $cday->locale('es');
                $enableday          = isset($settingdays[$cday->isoFormat('d')]) ? ($settingdays[$cday->isoFormat('d')]['enabled'] ? true : false) : false;

                //rewrite
                if($hollydays->where('MDAY',$cday->format('md'))->count()){
                    $enableday  = false;
                }
                $cday->locale($emsetting->getLanguage());
                $workindays[$day]   = ['id' => $cday->format('Ymd'), 'enable'=>$enableday,  'text'=> ucfirst($cday->isoFormat('dddd').', '.$cday->isoFormat('DD') )];
            }
        }


        return $workindays;

    }


    /**
     * @param int $setting
     * @param string $day 20190201 Ymd
     * @param array $productlist [1323213123, 21324324, 324324324]
     * @return array
     */
    public function getWorkingTimes($setting, $day, $productlist){
        $immediately    = false;
        $arrayhours     = [];
        $max_time       = 0;
        $ranges         = 1; //hours
        $now            = Carbon::now(); //Local setting eje. (GMT-05:00) Eastern Time (US & Canada) America/New_York
        $dtStar         = null;
        $dtStop         = null;
        $stop           = 50;


        try
        {

            if(empty($day))
            {
                return   ['success'=>false, 'hours'=>[], 'errors'=>['message'=>'invalid day']];
            }

            $emsetting   = EMVexsetting::find($setting);
            if ($emsetting->workinghours()->count() == 0){
                return   ['success'=>false, 'immediately'=>false, 'hours'=>[], 'errors'=>['message'=>'service not available']];

            }

            $store  = EMVexstore::findByStoreId($emsetting->getStoreId());
            $now->setTimezone($store->getTimeZone());


            $setting_days = $emsetting->workinghours()->first()->getWorkinDays('all');
            $meta_times   = EMProductmetadata::whereIn('PROD_PRODUCT', $productlist)->where('PROD_METADATA_VALUE','<>', 'immediately')->get();

            #max preparation time
            if ($meta_times)
            {
                foreach ($meta_times as $pmeta){
                    if ($pmeta->PROD_METADATA_VALUE > $max_time) {
                        $max_time = $pmeta->PROD_METADATA_VALUE;
                    }
                }
            }


            #take a day selected
            $fromNow        = Carbon::createFromFormat('Ymd H:i', $day. $now->format('H:i'), $store->getTimeZone() );
            $dayselected    = $fromNow->dayOfWeek;
            $setting_day    = $setting_days[$dayselected];

            if (!$setting_day['enabled'])
            {
                return   ['success'=>false, 'immediately'=>false, 'hours'=>[], 'errors'=>['message'=>"day {$dayselected} is not avaliable"]];
            }


            //for the same day (today)
            if ($now->format('Ymd') == $fromNow->format('Ymd'))
            {
                $dopen          = Carbon::createFromFormat('Ymd H:i', $fromNow->format('Ymd') . " ". $setting_day['hours'][0]['open'], $store->getTimeZone());
                $dclose         = Carbon::createFromFormat('Ymd H:i', $fromNow->format('Ymd') . " ". $setting_day['hours'][0]['close'],$store->getTimeZone() );


                if($now->gt($dopen)){
                    $dopen  = $now->clone();//->ceilUnit('minutes',30);
                }


                //service not available
                if ($now->gt($dclose))
                {
                    return  ['success'=>false, 'hours'=>[],'immediately'=>false,  'errors'=>['message'=>'service not available']];
                }



            }else
                //for other day
            {
                $dopen          = Carbon::createFromFormat('Ymd H:i', $fromNow->format('Ymd') . " ". $setting_day['hours'][0]['open'] , $store->getTimeZone());
                $dclose         = Carbon::createFromFormat('Ymd H:i', $fromNow->format('Ymd') . " ". $setting_day['hours'][0]['close'] , $store->getTimeZone());

            }

            //Preparation Time
            if ($max_time > 0){
                // open  09:00
                // preparation time = 45min
                // (open + preparation time) 09:45
                // start service = 09:45
                $dtStar     = $dopen->copy()->addMinutes($max_time);//->ceilUnit('minutes',$max_time);


                // close 22:00
                // preparation time 45min
                // (close - preparation) = 21:15
                // last service until = 21:15
                $dtStop     = $dclose->clone()->subMinutes($max_time);

                $dtStar       = $dtStar->ceilUnit('minutes',60);
                //Log::critical('after ceil',[$dtStar]);


            }else
            {
                $dtStar     = $dopen->clone();
                $dtStop     = $dclose->clone();

                //Log::critical('$max_time <> 0',[$dtStar]);


            }

            # [09:45, 10:00, 10:30, 11:00, 12:00, 13:00, 14:00, 15:00, 16:00, 17:00, 18:00, 19:00, 20:00, 21:00, 21:15]
            # pueden ser rangos de 30min

            //si despues del ajuste, no se excede a la hora de cierre
            // Se determina el primer turno
            if ($dtStar->lte($dclose)){
                $arrayhours[] = ['id'=>$dtStar->format('YmdHi'), 'text'=>$dtStar->format('H:i'). " - " . $dtStar->addHour()->minute(0)->second(0)->format('H:i')];
            }

            //Log::critical('start',[$dtStar]);
            //Log::critical('array',$arrayhours);





            if ($now->gte($dopen)  && $now->lte($dclose)){
                $immediately = true;
            }

            //calculate ranges hours
            $i=0;
            while (  $dtStar <  $dtStop)
            {
                $arrayhours[] = ['id'=>$dtStar->format('YmdHi'), 'text'=>$dtStar->format('H:i') . " - " . $dtStar->clone()->addMinutes(60)->format('H:i')];


                if ( $dtStar->clone()->addMinutes(60) >=  $dtStop){
                    break;
                }

                $dtStar->addMinutes(60);

            }

            //ultimo turno
            //$arrayhours[] = ['id'=>$dtStop->format('YmdHi'), 'text'=>$dtStop->format('H:i')];

            return ['success'=>true, 'immediately' =>$immediately, 'hours'=>$arrayhours];


        }
        catch (Exception $e)
        {

        }


        return ['success'=>false, 'immediately' =>false, 'hours'=>[]];
    }


    /**
     * @param $emsetting
     * @param $location
     * @return array
     */
    public function _validateConfig($emsetting, $locations){

        $location   = collect($locations)->first();
        $json   = [

            'credentials'   => [
                'googleapi' => $emsetting->getGoogleApiKey(),
                'secret'    => $emsetting->getGlovoSecret(),
                'key'       => $emsetting->getGlovoApi(),
                'server'    => $emsetting->getServer()
            ],
            'address'       => [
                'address1'  => $location['address1'],
                'address2'  => $location['address2'],
                'city'      => $location['city'],
                'province'  => $location['province'],
                'country'   => $location['country'],
                'zip'       => $location['postcode'],

            ]
        ];

        return $this->connectToGlovoService(json_encode($json), $email = false);
    }


    /**
     * @return mixed
     */
    public function _validateConfigFromForm(){
        $pparams    = Request::getContent();
        return $this->connectToGlovoService($pparams, $email = true);
    }



    /**
     * @return array
     */
    private function connectToGlovoService($jsonstring, $emailme = false){

        try
        {
            $shop       = ShopifyApp::shop();



            if ( !$params = json_decode($jsonstring) ){


            }

            $credentials    = $params->credentials;

            $emsetting  = new EMVexsetting();
            $emsetting->setGoogleApiKey($credentials->googleapi);
            $emsetting->setGlovoSecret($credentials->secret);
            $emsetting->setGlovoApi($credentials->key);
            $emsetting->setServer($credentials->server);

            $plocation      = $params->address; //primary location
            $address        = $plocation->address1;
            if ($plocation->address2) $address.= ", ".$plocation->address2;
            $address        .= ", ".$plocation->city . ", ". $plocation->province .", ". $plocation->zip. ", ".$plocation->country;

            //geodecode location store
            $cglovo         = new GlovoController($emsetting);
            $location       = $cglovo->geocode($address);

            Log::log('critical','emsettings', [$emsetting]);
            Log::log('critical','decoding address', [$address]);
            Log::log('critical','decoding result', [$location]);
            //$location->status == 'REQUEST_DENIED'
            if ($location->status !== 'OK'){
                throw  new Exception("Verifica la clave de la API Google Maps, posiblemente sea invalidad o no este activada, verifica que no tenga restricciones para google maps", "503");
            }


            $apiRequest = array(
                'description'   => "Checking availability",
                'scheduletime'  => null,
                'address'       => [
                    'origin'    => [
                        'lat'               => $location->latitude,
                        'lng'               => $location->longitude,
                        'label'             => "Pick up point",
                        'details'           => ""
                    ],
                    'destination'    => [
                        'lat'               => $location->latitude,
                        'lng'               => $location->longitude,
                        'label'             => "Delivery point",
                        'details'           => "",
                    ]
                ]
            );

            $result        = $cglovo->TestService($apiRequest);


        }catch (Exception $e)
        {
            $result = ['success'=>false, 'errors'=>['message'=>$e->getMessage()]];
        }

        #-----------------------------------------------------------------------------------------------------------
        # SEND EMAIL
        #-----------------------------------------------------------------------------------------------------------
        try
        {
            if ( $emailme )
            {
                Mail::to('leshcoff@gmail.com')->queue(new onServiceTest($shop, $result ));
            }

        }catch (Exception $e)
        {
            Log::critical('Sending Mail failed : ' .$e->getMessage() );
        }


        return $result;

    }



    public function htmlSnippet ($shop, EMVexsetting $emsettings, Collection $product_list){


        try
        {

            $html               = "";
            $glovo_shipping_available_items = false;
            $estimate_time      = 0;
            $estimate_hours     = 0;
            $estimate_min       = 0;
            $total_weight       = 0;
            $pvariants          = $product_list->keyBy('variant_id');
            $products           = $product_list->keyBy('product_id')->pluck('product_id')->toArray();


            #get all products
            //$apiproducts     = $shop->api()->rest('GET',"/admin/api/".Config('shopify-app.api_version')."/products.json",['ids'=> implode(",",$products)]);

            #-----------------------------------------------------------------------------------------------------------
            # variants
            #-----------------------------------------------------------------------------------------------------------
            foreach ($pvariants as $variant){
                $apiproductvariant = $shop->api()->rest('GET',"/admin/api/".Config('shopify-app.api_version')."/variants/{$variant->variant_id}.json");
                $apiproductvariant = $apiproductvariant->body->variant;

                $total_weight+=  $apiproductvariant->grams;

                $apiproductmetas    = $shop->api()->rest('GET',"/admin/api/".Config('shopify-app.api_version')."/products/{$variant->product_id}/metafields.json");
                $metas              = collect( $apiproductmetas->body->metafields )->keyBy('key');

                if ( $metas->has('available_for_glovo') and $metas->get('available_for_glovo')->value =='true')
                {
                    $glovo_shipping_available_items = true;
                    $preparation_time   = $metas->has('preparation_time') ? $metas->get('preparation_time')->value : 0;

                    if ($preparation_time != "immediately")
                    {
                        if ( $preparation_time > $estimate_time ) {
                            $estimate_time = $preparation_time;
                        }
                    }

                }
            }

            #determinate time
            if($estimate_time > 0 )
            {
                $estimate_hours = floor($estimate_time / 60);
                $estimate_min   = $estimate_time % 60;
            }

            //verificamos la disponibilidad inmediata settings->avalible_all_producsts
            if ( $emsettings->getEnableAllProducts())
            {
                $glovo_shipping_available_items = true;

            }

            if ( $total_weight  > 9000 ) {
                Log::critical('total_weight exced 9000', [$total_weight]);
                Log::critical('store id : '. $emsettings->getStoreId());
            }

            if ( $glovo_shipping_available_items == true  and $total_weight  < 9000 )
            {
                //date_default_timezone_set("Europe/Madrid");

                $html.="<hr>";
                $html.= "<div title='" . $emsettings->store->getTimeZone() .' - ' .Carbon::now($emsettings->store->getTimeZone())->format('d-m-Y H:i:s') ."'></div>";
                $html.="<fieldset id=\"glovo-shpping-delivery\" class='shipping-glovo-delivery' style=\"display:none\">";
                #$html.="    <div id=\"glovo-loading\" style=\"display:none\">".__('glovo.storefront.template.cart.loading').".</div>";

                $html.="    <legend style=\"color: rgba(255,194,68,.9);text-shadow: 0px 0px #000000;\">";
                $html.="        <img src=\"".env("SHOPIFY_APP_URL")."/assets/images/icons/shipping_glovo.png\">";
                $html.="        ". $emsettings->getMethodTitle();
                $html.="    </legend>";

                if ( $estimate_hours > 0 )
                    $html.="    <div style=\"color:#2abb9b\">". str_replace(["{estimate_hours}","{estimate_min}"],[$estimate_hours,$estimate_min ], __('glovo.storefront.template.cart.estimated.hour'))." </div>";
                elseif($estimate_time > 0)
                    $html.="    <div style=\"color:#2abb9b\">". str_replace(["{estimate_time}"],[$estimate_time ], __('glovo.storefront.template.cart.estimated.minute'))."</div>";
                else
                    $html.="    <div style=\"color:#2abb9b\">".__('glovo.storefront.template.cart.estimated.immediately')."</div>";

                $html.="    <div id=\"glovo-shpping-delivery-form-wrapper\" class=\"clearfix\">";
                $html.="        <p>".__('glovo.storefront.template.cart.available')."<p> ";

                if ( ! $emsettings->getAllowScheduled() ){
                    $html .= "   <div class='glovo-info'> ".__('glovo.storefront.template.cart.noallowscheduled');
                    $html .= "      <input type=\"hidden\" id=\"glovo_when_receive\" name=\"attributes[glovo_when_receive]\" value='immediately'>";
                    $html .= "   </div>";
                }else {
                    $html.= "    <div class='glovo-pikcup-settings'>";
                    $html.="            <p style=\"color:#2abb9b\">".__('glovo.storefront.template.cart.when.label')."</p>";
                    $html.="            <input type=\"hidden\" id=\"glovo_when_receive\" name=\"attributes[glovo_when_receive]\">";
                    $html.="            <div> <input type=\"checkbox\" class=\"jtoggler\" data-jtlabel=\"".__('glovo.storefront.template.cart.when.question')."\" data-jtlabel-success=\"".__('glovo.storefront.template.cart.when.assoon')."\" checked value=\"immediately\"> </div>";
                    $html.="            <div class=\"select-schedule uk-grid\">";
                    $html.="                <div class=\"uk-width-medium-5-10\">";
                    $html.="                    <p>".__('glovo.storefront.template.cart.day').": <select name=\"attributes[glovo_schedule_day]\" id=\"glovo_schedule_day\" style=\"width: 80%\"></select></p>";
                    $html.="                </div>";
                    $html.="                <div class=\"uk-width-medium-5-10\">";
                    $html.="                    <p>".__('glovo.storefront.template.cart.hour').": <select name=\"attributes[glovo_schedule_time]\" id=\"glovo_schedule_time\" style=\"width: 70%\"></select></p>";
                    $html.="                </div>";
                    $html.="            </div>";
                    $html.="     </div>";
                }

                $html.= "   </div>";
                $html.="    <div id='glovo-map' style=\"height: 400px; display: none\" class=\"glovo-map\"></div>";
                $html.="    <div id=\"wrapper-response\"></div>";
                $html.="</fieldset>";
            }



        }catch (Exception $e)
        {


        }


        return $html;
    }


}
