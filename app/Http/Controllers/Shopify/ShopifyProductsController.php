<?php

namespace App\Http\Controllers\Shopify;

use App\Models\EMVexstore;
use App\Models\Shopify\EMProductmetadata;
use Utils\Dates;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;


use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use OhMyBrew\ShopifyApp\Models\Shop;
use OhMyBrew\ShopifyApp\Services\ShopSession;

use App\Models\EMVexsetting;
use App\Models\Store\EMVexhollyday;
use App\Models\Store\EMVexhours;
use App\Models\Store\EMVexlocations;
use Escom\Base\CBase;

class ShopifyProductsController extends Controller
{
    //
    protected $pageSize = 50;
    public    $emsettings;


    public function __construct()
    {
        ini_set("memory_limit","1G");
        ini_set("max_execution_time","300");
    }

    /**
     * index
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {

        $requestproducts   = $this->getProductsApiV201010();
        $products          = collect($requestproducts['products']->all());
        $paginator         = $requestproducts['paginator'];

        $keys              = $products->pluck('id')->toArray();
        $emprodmeta         = collect(EMProductmetadata::whereIn('PROD_PRODUCT',$keys)->get()->toArray());

        $apishop            = ShopifyApp::shop();
        $shopifyshop        = $apishop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;

        $emsettings   = EMVexsetting::findByStoreId($shopifyshop->id);
        if ( is_null($emsettings) or ( empty($emsettings->getGlovoApi()) or empty($emsettings->getGlovoSecret()) or empty($emsettings->getGoogleApiKey())) or ($emsettings->getValidated() == false))
        {
            return Redirect::route('shopify.settings');

        }

        app('translator')->setLocale($emsettings->getLanguage());

        return view('shopify.products.index')
            ->with('emsetting' , $emsettings)
            ->with('products' , $products)
            ->with('paginator', $paginator)
            ->with('emprodmeta',$emprodmeta);

    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function preparationForm(){

        $shop           = ShopifyApp::shop();
        $route          = env('SHOPIFY_APP_URL')."/products/preparation/save";

        $meta_value     = null;
        $product_id     = Request::get('product');
        $apiproduct     = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/products/{$product_id}.json");
        $shopiproduct   = $apiproduct->body->product;
        $emprodmeta     = EMProductmetadata::where('PROD_PRODUCT',$product_id)->where('PROD_METADATA_KEY','preparation_time')->first();
        $emavailable    = EMProductmetadata::where('PROD_PRODUCT',$product_id)->where('PROD_METADATA_KEY','available_for_glovo')->first();
        $meta_value     = $shop->api()->rest('GET', "/admin/api/".\Config('shopify-app.api_version')."/products/$product_id/metafields.json");


        $shopifyshop    = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;
        $settings   = EMVexsetting::findByStoreId($shopifyshop->id);
        app('translator')->setLocale($settings->getLanguage());


        $preparationTimes    = [
            "immediately"      => __('glovo.preparationform.availability.label'),
            '10'    =>'00:10 Minutes',
            '15'    =>'00:15 Minutes',
            '20'    =>'00:20 Minutes',
            '25'    =>'00:25 Minutes',
            '30'    =>'00:30 Minutes',
            '35'    =>'00:35 Minutes',
            '40'    =>'00:40 Minutes',
            '45'    =>'00:45 Minutes',
            '50'    =>'00:50 Minutes',
            '55'    =>'00:55 Minutes',
            '60'    =>'01:00 Hour',
            '75'    =>'01:15 Hour',
            '90'    =>'01:30 Hour',
            '105'   =>'01:45 Hour',
            '120'   =>'02:00 Hour',
            '150'   =>'02:30 Hour',
            '180'   =>'03:00 Hour',
            '210'   =>'03:30 Hour',
            '240'   =>'04:00 Hour',
            '270'   =>'04:30 Hour',
            '300'   =>'05:00 Hour',

        ];
        return view('shopify.products.preparation')
            ->with('preparationtimes',$preparationTimes)
            ->with('route',$route)
            ->with('emavailable',$emavailable)
            ->with('emprodmeta',$emprodmeta)
            ->with('shopiproduct', $shopiproduct);
    }


    /**
     * @return array
     */
    public function preparationTimeSave(){

        $response = [];

        try
        {
            $shop       = ShopifyApp::shop();
            $id_product = Request::get('product');
            $delivery   = (Request::has('available_for_glovo') ? true : false );
            $time       = Request::get('preparationtime');

            $shopifyshop    = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;




            $metafield_ept = array(
                'metafield' => array(
                    "namespace"     => "glovo_shipping",
                    "key"           => "preparation_time",
                    "value"         => $time,
                    "value_type"    => "string",
                    "description"   => "Glovo Shipping Preparation Time"
                )
            );


            $mintext    =  \Utils\Dates::minutesToHours($time,'%02d hours %02d minutes');
            $prodmeta   = EMProductmetadata::where('PROD_PRODUCT',$id_product)->where('PROD_METADATA_KEY','preparation_time')->first();
            if ( is_null($prodmeta) )
            {

                $metafield    = $shop->api()->rest('POST', "/admin/api/".\Config('shopify-app.api_version')."/products/$id_product/metafields.json", $metafield_ept);
                $prodmeta = new EMProductmetadata();
                $prodmeta->PROD_SHOP            = $shop->id;
                $prodmeta->PROD_PRODUCT         = $id_product;
                $prodmeta->PROD_METADATA_ID     = $metafield->body->metafield->id;
                $prodmeta->PROD_METADATA_KEY    = $metafield->body->metafield->key;
                $prodmeta->PROD_METADATA_VALUE  = $metafield->body->metafield->value;
                $prodmeta->PROD_METADATA_TYPE   = $metafield->body->metafield->value_type;
                $prodmeta->save();


            }else
            {
                //update the data
                $metafield    = $shop->api()->rest('PUT', "/admin/api/".\Config('shopify-app.api_version')."/products/$id_product/metafields/{$prodmeta->PROD_METADATA_ID}.json", $metafield_ept);
                $prodmeta->PROD_SHOP            = $shop->id;
                $prodmeta->PROD_PRODUCT         = $id_product;
                $prodmeta->PROD_METADATA_ID     = $metafield->body->metafield->id;
                $prodmeta->PROD_METADATA_KEY    = $metafield->body->metafield->key;
                $prodmeta->PROD_METADATA_VALUE  = $metafield->body->metafield->value;
                $prodmeta->PROD_METADATA_TYPE   = $metafield->body->metafield->value_type;
                $prodmeta->save();

            }



            $metafield_afg = array(
                'metafield' => array(
                    "namespace"     => "glovo_shipping",
                    "key"           => "available_for_glovo",
                    "value"         => $delivery,
                    "value_type"    => "string",
                    "description"   => "Available for Glovo Delivery"
                )
            );



            //if is checked for delivery
            if ( $delivery )
            {
                $meta_available   = EMProductmetadata::where('PROD_PRODUCT',$id_product)->where('PROD_METADATA_KEY','available_for_glovo')->first();
                if ( is_null($meta_available) )
                {
                    $metafield    = $shop->api()->rest('POST', "/admin/api/".\Config('shopify-app.api_version')."/products/$id_product/metafields.json", $metafield_afg);
                    $prodmeta = new EMProductmetadata();
                    $prodmeta->PROD_SHOP            = $shop->id;
                    $prodmeta->PROD_PRODUCT         = $id_product;
                    $prodmeta->PROD_METADATA_ID     = $metafield->body->metafield->id;
                    $prodmeta->PROD_METADATA_KEY    = $metafield->body->metafield->key;
                    $prodmeta->PROD_METADATA_VALUE  = $metafield->body->metafield->value;
                    $prodmeta->PROD_METADATA_TYPE   = $metafield->body->metafield->value_type;
                    $prodmeta->save();
                }

            }else
            {
                $prodmeta     = EMProductmetadata::where('PROD_PRODUCT',$id_product)->where('PROD_METADATA_KEY','available_for_glovo')->first();
                if ( $prodmeta )
                {
                    $metafield    = $shop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/products/$id_product/metafields/{$prodmeta->PROD_METADATA_ID}.json");
                    $deleted      = $prodmeta->delete();
                }

            }

            return ['success' => true, 'text'=>$mintext, 'product'=>$id_product, 'message'=> __('glovo.preparationform.save.success')];

        }
        catch (Exception $e)
        {
            Log::critical($e->getMessage());
            $response = ['success' => false, 'errors'=>['message'=> __('glovo.preparationform.save.error')] ];
        }

        return $response;

    }


    /**
     * @return array
     */
    public function preparationTimeDelete(){
        $response = [];

        try
        {
            $shop       = ShopifyApp::shop();
            $metaid     = Request::get('metaid');
            $product    = Request::get('product');

            $shopifyshop    = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;
            $settings   = EMVexsetting::findByStoreId($shopifyshop->id);
            app('translator')->setLocale($settings->getLanguage());


            $prodmeta   = EMProductmetadata::where('PROD_PRODUCT',$product)->where('PROD_METADATA_ID', $metaid)->first();
            if(!is_null($prodmeta))
            {
                $meta = $shop->api()->rest('DELETE', "/admin/api/".\Config('shopify-app.api_version')."/products/$product/metafields/$metaid.json");
                $prodmeta->delete();

            }

            return ['success' => true, 'metaid'=>$metaid, 'product'=>$product, 'message'=> __('glovo.preparationform.save.success')];

        }
        catch (Exception $e)
        {
            Log::critical($e->getMessage());
            $response = ['success' => false, 'errors'=>['message'=> __('glovo.preparationform.save.error')]];
        }

        return $response;
    }



    /**
     * @return array
     */
    public function setAvailability(){
        $response = [];

        try
        {
            $shop           = ShopifyApp::shop();
            $allowForAll    = Request::get('allow_for_all');

            $emstore        = EMVexstore::findByDomain( $shop->shopify_domain );

            $settings   = EMVexsetting::findByStoreId($emstore->getId());
            if(!is_null($settings))
            {
                $enabled  = ( $allowForAll == "allow_for_all" ) ? "S" : "N";
                $settings->setEnableAllProducts($enabled);
                $settings->save();

            }

            return ['success' => true, 'message'=> __('glovo.preparationform.save.success')];

        }
        catch (Exception $e)
        {
            Log::critical($e->getMessage());
            $response = ['success' => false, 'errors'=>['message'=> __('glovo.preparationform.save.error')]];
        }

        return $response;
    }



    /**
     * @param $shop
     * @param null $product_id
     * @param null $variant
     * @return array
     */
    public function isProductAvaliable ($shop, $product_id=null, $variant=null)
    {

        $response = ['status'=> ['code'=>300, 'message' =>'Not available for glovo']];

        try
        {

            if (is_null($product_id) and is_null($variant)){
                return $response=['status'=> ['code'=>401, 'message' =>'No product or variant submited']];
            }

            if ( $variant )
            {
                $domain = $shop->shopify_domain;
                $emstore        = EMVexstore::findByDomain( $shop->shopify_domain );
                $settings       = EMVexsetting::findByStoreId($emstore->getId());

                if ($settings->getEnable() != 1){
                    return $response=['status'=> ['code'=>401, 'message' =>'Not enable glovo delivery']];
                }

                $apivariant     = $shop->api()->rest('GET', "/admin/api/".Config('shopify-app.api_version')."/variants/{$variant}.json");
                if ($apivariant->errors == true) {
                    return $response=['status'=> ['code'=>$apivariant->status, 'message' =>'No product or variant submited', 'apimessage'=>$apivariant->body]];
                }

                $variant         = $apivariant->body->variant;
                $apiproduct      = $shop->api()->rest('GET',"/admin/api/".Config('shopify-app.api_version')."/products/{$variant->product_id}.json");
                $product         = $apiproduct->body->product;

                //Log::alert('variant ->', [$variant]);
                //Log::alert('product ->', [$product]);
               
                //verificamos la disponibilidad inmediata
                if ( $settings->getEnableAllProducts())
                {

                    $mintext                = "immediately";
                    $response               = ['status'=>['code'=>200,'message'=>'OK'], 'avalible' => 'enable_all_for_all', 'product' => ['name'=> $product->title, 'available'=>true, 'prepatation_time'=> "immediately", 'prepatation_time_format'=> $mintext ]];
                    $response['snippet']    = $this->snippetProductAvailable($response);

                    return $response;
                }else
                {

                }



                $apiproductmetas = $shop->api()->rest('GET',"/admin/api/".Config('shopify-app.api_version')."/products/{$variant->product_id}/metafields.json");
                $metas           = collect( $apiproductmetas->body->metafields )->keyBy('key');

                if ( $metas->count() == 0) {
                    return $response=['status'=> ['code'=>400, 'message' =>'No settings for glovo']];
                }

                //only glovo namespace
                $metas  = collect( $apiproductmetas->body->metafields )->where('namespace', 'glovo_shipping')->keyBy('key');

                //only glovo products have service
                if ( !$metas->has('available_for_glovo') )
                {
                    return $response=['status'=> ['code'=>405, 'message' =>'Not available for glovo']];
                }


                if (  $metas->get('available_for_glovo')->value =='true' )
                {
                    $preparation_time = $metas->get('preparation_time')->value;
                    $mintext          = ($preparation_time == "immediately" ) ? $preparation_time : \Utils\Dates::minutesToHours($preparation_time,'%02d hours %02d minutes');

                    $response = ['status'=>['code'=>200,'message'=>'OK'], 'product' => ['name'=> $product->title, 'available'=>true, 'prepatation_time'=> $metas->get('preparation_time')->value, 'prepatation_time_format'=> $mintext ]];
                    $response['snippet'] = $this->snippetProductAvailable($response);
                }


            }elseif ($product_id)
            {

            }


        }
        catch (Exception $e)
        {
            Log::critical($e->getMessage());
            $response = ['status'=>['code'=>500], 'errors'=>['message'=>$e->getMessage()]];
        }

        return $response;
    }




    public function snippetProductAvailable($data){


        $html = "
        <fieldset class=\"shipping-glovo-delivery\">
            <legend><img src=\"".env('SHOPIFY_APP_URL')."/assets/images/icons/shipping_glovo.png\" width=\"80\"> Glovo delivery  </legend>
            <div class=\"shipping-glovo-delivery-body\">
                <div class=\"shipping-glovo-delivery-estimated\">
                    <div class=\"shipping-glovo-delivery-icon-estimated\">
                        <img src=\"".env('SHOPIFY_APP_URL')."/assets/images/icons/clock.png\" width=\"22\">
                    </div>
                    <div style=\"float: left\">
                        <span class=\"estimated-time\">".$data['product']['prepatation_time_format']."</span>
                    </div>
                    
                </div>
                <div class=\"shipping-glovo-delivery-info\">
                ".__('glovo.storefront.template.products.available'). " " .$this->emsettings->getMethodTitle(). "".( $this->emsettings->getAllowScheduled() ? (".<br> ".__('glovo.storefront.template.products.choosewhen')) : "")."
                </div>
            </div>
        </fieldset>
        ";

        return $html;
    }

    /**
     * @return array
     */
    public function getProductsApiV201010(){

        $ids    = Request::get('ids');
        $shop   = ShopifyApp::shop();

        $query = ['limit' => $this->pageSize];
        if ($ids)
        {
            $query['ids']   = $ids;
        }

        if (\Request::get('page_info') ){
            $query['page_info']   = Request::get('page_info');
        }
        if (\Request::get('rel') ){
            $query['rel']   = Request::get('rel');
        }

        $apiproduts = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/products.json', $query);
        $linkHeader = collect($apiproduts->response->getHeader('Link'))->first();


        $products= [];
        foreach ($apiproduts->body->products as $key=> $product){
            $products[] =$product;
        }



        // Create a new Laravel collection from the array data
        $itemCollection = collect($products);


        $next = null; $previous = null;
        if ($apiproduts->link ){
            $params = [];
            $query = \Request::getQueryString();
            parse_str($query, $params);

            if ($apiproduts->link->previous) {
                $previous   =  env("SHOPIFY_APP_URL"). "/products?". http_build_query(  array_merge($params, ['page_info'=>$apiproduts->link->previous, "rel"=>'previous']));

            }

            if ($apiproduts->link->next){
                $next       =  env("SHOPIFY_APP_URL"). "/products?". http_build_query( array_merge($params, ['page_info'=>$apiproduts->link->next, "rel"=>'next']));

            }


        }

        $paginator = new \StdClass();
        $paginator->previous =  $previous;
        $paginator->next     =  $next;

        return ['products'=>$itemCollection, 'paginator' => $paginator ];
    }



    /**
     * @param array $params
     */
    public function getProductsAPI ($params=[]){

        $ids    = Request::get('ids');
        $shop   = ShopifyApp::shop();

        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();


        //Total Items
        $apitotal = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/products/count.json');
        $total    = $apitotal->body->count;

        $query = ['limit' => $this->pageSize, 'published_status'=> 'any'];
        if ($ids)
            $query['ids']   = $ids;

        $apiproduts = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/products.json', $query);
        $linkHeader = collect($apiproduts->response->getHeader('Link'))->first();

        $products= [];
        foreach ($apiproduts->body->products as $key=> $product){
            $products[] =$product;
        }



        // Create a new Laravel collection from the array data
        $itemCollection = collect($products);

        unset($product);
        unset($products);

        // Define how many items we want to be visible in each page
        $perPage = $this->pageSize;

        // Slice the collection to get the items to display in current page
        //$currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();

        // Create our paginator and pass it to the view
        $paginatedItems= new LengthAwarePaginator($itemCollection , $total, $perPage);

        // set url path for generted links
        $paginatedItems->setPath(env("SHOPIFY_APP_URL"). "/products");


        return $paginatedItems;


    }



}
