<?php

namespace App\Http\Controllers\Shopify;

use App\Models\EMVexsetting;
use App\Models\Shopify\EMMetadata;
use App\Models\Shopify\EMProductmetadata;
use Carbon\Carbon;
use Exception;


use App\Models\EMVexstore;
use App\Models\Customer\EMRequestdata;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use OhMyBrew\ShopifyApp\Models\Shop;
use Vexsolutions\Utils\Logger\Facades\BufferLog;

class ShopifyWebHookController extends Controller
{



    /***
     * @return \Illuminate\Http\JsonResponse
     */
    public function CustomerRedact(){

        try
        {
            $response       = ['success'=>false];
            $content        = file_get_contents("php://input");
            $payload        = @json_decode($content);

            BufferLog::Debug("");
            BufferLog::Debug('-------------------------------------------------------------------------------------------');
            BufferLog::Debug('Request : customers/redact');
            BufferLog::Debug('-------------------------------------------------------------------------------------------');

            $emrequest          = new EMRequestdata();
            $emrequest->date    = \DB::raw('now()');
            $emrequest->status  = 0;
            $emrequest->payload = $content;
            $emrequest->topic   = 'customer-redact';
            $emrequest->save();


            if ( !$payload)
            {
                throw new \Exception("Invalid payload o inreconocible json", 201);

            }

            $hmac           = Request::header('x-shopify-hmac-sha256') ?: '';
            $domain         = Request::header('x-shopify-shop-domain');
            $data           = Request::getContent();
            $shop           = Shop::where('shopify_domain', $payload->shop_domain)->withTrashed()->first();;

            BufferLog::Debug('headers=>' . print_r(request()->headers->all(), true) );

            if ( $shop)
            {

                //for logs
                $carbonNow  = Carbon::now('America/Mexico_City');
                $fileToLog  = storage_path("logs/shopify/{$payload->shop_domain}/w/cr/customer-redact-".$carbonNow->format('Y-m-d') .".log");

                //registramos en la tabla requests
                $emrequest->shop_id         = $payload->shop_id;
                $emrequest->shopify_domain  = $payload->shop_domain;
                $emrequest->save();

                //settings
                $emsettings = EMVexsetting::findByStoreId($payload->shop_id);
                BufferLog::Debug('settings =>' . print_r($emsettings, true) );

                if ( $emsettings )
                {
                    //products metadata
                    EMProductmetadata::where('PROD_SHOP',$shop->id )->delete();
                    BufferLog::Debug('Deleting EMProductmetadata ...' );

                    //store metadata
                    EMMetadata::where('META_SETTING', $emsettings->getId())->delete();
                    BufferLog::Debug('Deleting EMMetadata ...' );


                    $emsettings->locations()->delete();
                    $emsettings->workinghours()->delete();
                    $emsettings->hollydays()->delete();
                    $emsettings->metadata()->delete();
                    $emsettings->delete();
                    $emsettings->forceDelete();

                    BufferLog::Debug('Deleting EMVexsetting ...' );

                }

                //store
                $store          = EMVexstore::where('STORE_DOMAIN',$payload->shop_domain)->first();
                BufferLog::Debug('EMVexstore =>' . print_r($store, true) );
                if ( $store)
                {
                    $store->delete();
                    $store->forceDelete();

                    BufferLog::Debug('Deleting EMVexstore ...' );

                }

                //shop
                $shop->delete();
                $shop->forceDelete();

                BufferLog::Debug('Deleting Shop ...' );


                $response=['success'=>true, 'message'=>"{$payload->shop_domain}, Store data will be deleted from the database"];
            }




        }catch (\Exception $e)
        {
            $response=['success'=>false, 'errors'=>['message'=>$e->getMessage()]];
            BufferLog::Debug('Error CustomerRedact ->  '.$e->getMessage());
        }



        BufferLog::Debug('------ End ----- ');
        BufferLog::Debug('');
        BufferLog::LogToFile( isset($fileToLog) ? $fileToLog : storage_path('logs/erros.log') );

        if ($emrequest) {
            $emrequest->update(['response'=>json_encode($response)]);
        }

        return  response()->json($response, 200);


    }


    public function ShopRedact(){

        try
        {
            $response       = ['success'=>false];
            $content        = file_get_contents("php://input");
            $payload        = @json_decode($content);

            BufferLog::Debug("");
            BufferLog::Debug('-------------------------------------------------------------------------------------------');
            BufferLog::Debug('Request : customers/redact');
            BufferLog::Debug('-------------------------------------------------------------------------------------------');

            $emrequest          = new EMRequestdata();
            $emrequest->date    = \DB::raw('now()');
            $emrequest->status  = 0;
            $emrequest->payload = $content;
            $emrequest->topic   = 'customer-redact';
            $emrequest->save();


            if ( !$payload)
            {
                throw new \Exception("Invalid payload o inreconocible json", 201);

            }

            $hmac           = Request::header('x-shopify-hmac-sha256') ?: '';
            $domain         = Request::header('x-shopify-shop-domain');
            $data           = Request::getContent();
            $shop           = Shop::where('shopify_domain', $payload->shop_domain)->withTrashed()->first();;

            BufferLog::Debug('headers=>' . print_r(request()->headers->all(), true) );

            if ( !$shop)
            {
                throw new \Exception("{$payload->shop_domain} Not present in database", 201);

            }


            //for logs
            $carbonNow  = Carbon::now('America/Mexico_City');
            $fileToLog  = storage_path("logs/shopify/{$payload->shop_domain}/w/cr/customer-redact-".$carbonNow->format('Y-m-d') .".log");

            //registramos en la tabla requests
            $emrequest->shop_id         = $payload->shop_id;
            $emrequest->shopify_domain  = $payload->shop_domain;
            $emrequest->save();

            //settings
            $emsettings = EMVexsetting::findByStoreId($payload->shop_id);
            BufferLog::Debug('settings =>' . print_r($emsettings, true) );

            if ( $emsettings )
            {
                //products metadata
                EMProductmetadata::where('PROD_SHOP',$shop->id )->delete();
                BufferLog::Debug('Deleting EMProductmetadata ...' );

                //store metadata
                EMMetadata::where('META_SETTING', $emsettings->getId())->delete();
                BufferLog::Debug('Deleting EMMetadata ...' );


                $emsettings->locations()->delete();
                $emsettings->workinghours()->delete();
                $emsettings->hollydays()->delete();
                $emsettings->metadata()->delete();
                $emsettings->delete();
                $emsettings->forceDelete();

                BufferLog::Debug('Deleting EMVexsetting ...' );

            }

            //store
            $store          = EMVexstore::where('STORE_DOMAIN',$payload->shop_domain)->first();
            BufferLog::Debug('EMVexstore =>' . print_r($store, true) );
            if ( $store)
            {
                $store->delete();
                $store->forceDelete();

                BufferLog::Debug('Deleting EMVexstore ...' );

            }

            //shop
            $shop->delete();
            $shop->forceDelete();

            BufferLog::Debug('Deleting Shop ...' );


            $response=['success'=>true, 'message'=>"{$payload->shop_domain}, Store data will be deleted from the database"];

        }catch (\Exception $e)
        {
            $response=['success'=>false, 'errors'=>['message'=>$e->getMessage()]];
            BufferLog::Debug('Error CustomerRedact ->  '.$e->getMessage());
        }



        BufferLog::Debug('------ End ----- ');
        BufferLog::Debug('');
        BufferLog::LogToFile( isset($fileToLog) ? $fileToLog : storage_path('logs/erros.log') );

        if ($emrequest) {
            $emrequest->update(['response'=>json_encode($response)]);
        }

        return  response()->json($response, 200);


    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function CustomerDataRequest(){

        try
        {
            $response       = ['success'=>false];
            $content        = file_get_contents("php://input");
            $payload        = @json_decode($content);

            BufferLog::Debug("");
            BufferLog::Debug('-------------------------------------------------------------------------------------------');
            BufferLog::Debug('Request : customers/data-request');
            BufferLog::Debug('-------------------------------------------------------------------------------------------');

            $emrequest          = new EMRequestdata();
            $emrequest->date    = \DB::raw('now()');
            $emrequest->status  = 0;
            $emrequest->payload = $content;
            $emrequest->topic   = 'customer-redact';
            $emrequest->save();


            if ( !$payload)
            {
                throw new \Exception("Invalid payload o inreconocible json", 201);

            }

            $hmac           = Request::header('x-shopify-hmac-sha256') ?: '';
            $domain         = Request::header('x-shopify-shop-domain');
            $data           = Request::getContent();
            $shop           = Shop::where('shopify_domain', $payload->shop_domain)->withTrashed()->first();;

            BufferLog::Debug('headers=>' . print_r(request()->headers->all(), true) );

            if ( !$shop)
            {
                throw new \Exception("{$payload->shop_domain} Not present in database", 201);

            }


            //for logs
            $carbonNow  = Carbon::now('America/Mexico_City');
            $fileToLog  = storage_path("logs/shopify/{$payload->shop_domain}/w/cr/customer-redact-".$carbonNow->format('Y-m-d') .".log");

            //registramos en la tabla requests
            $emrequest->shop_id         = $payload->shop_id;
            $emrequest->shopify_domain  = $payload->shop_domain;
            $emrequest->save();

            //settings
            $emsettings = EMVexsetting::findByStoreId($payload->shop_id);
            BufferLog::Debug('settings =>' . print_r($emsettings, true) );

            if ( $emsettings )
            {
                //products metadata
                EMProductmetadata::where('PROD_SHOP',$shop->id )->delete();
                BufferLog::Debug('Deleting EMProductmetadata ...' );

                //store metadata
                EMMetadata::where('META_SETTING', $emsettings->getId())->delete();
                BufferLog::Debug('Deleting EMMetadata ...' );

                BufferLog::Debug('Deleting EMVexsetting ...' );

            }

            //store
            $store          = EMVexstore::where('STORE_DOMAIN',$payload->shop_domain)->first();
            BufferLog::Debug('EMVexstore =>' . print_r($store, true) );
            if ( $store)
            {

                BufferLog::Debug('Deleting EMVexstore ...' );

            }

            //shop
            //$shop->delete();

            BufferLog::Debug('Deleting Shop ...' );


            $response=['success'=>true, 'message'=>"{$payload->shop_domain}, Store data will be deleted from the database"];

        }catch (\Exception $e)
        {
            $response=['success'=>false, 'errors'=>['message'=>$e->getMessage()]];
            BufferLog::Debug('Error CustomerRedact ->  '.$e->getMessage());
        }



        BufferLog::Debug('------ End ----- ');
        BufferLog::Debug('');
        BufferLog::LogToFile( isset($fileToLog) ? $fileToLog : storage_path('logs/erros.log') );

        if ($emrequest) {
            $emrequest->update(['response'=>json_encode($response)]);
        }

        return  response()->json($response, 200);


    }

}
