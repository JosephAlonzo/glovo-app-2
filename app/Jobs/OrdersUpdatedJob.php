<?php

namespace App\Jobs;

use App\Http\Controllers\Shopify\ShopifyOrdersController;
use App\Models\EMVexstore;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Request;
use OhMyBrew\ShopifyApp\Models\Shop;
use Vexsolutions\Utils\Logger\Facades\BufferLog;
use Exception;

class OrdersUpdatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $content        = file_get_contents("php://input");
        $order          = json_decode($content);

        // Do what you wish with the data
        $hmac       = Request::header('x-shopify-hmac-sha256') ?: '';
        $domain     = Request::header('x-shopify-shop-domain');
        $data       = Request::getContent();
        $store      = EMVexstore::where('STORE_DOMAIN',$domain)->get()->first();

        //for logs
        $carbonNow  = Carbon::now('America/Mexico_City');
        $fileToLog  = storage_path("logs/shopify/{$domain}/w/ou/". $order->id .".log");

        try
        {
            BufferLog::Debug("");
            BufferLog::Debug('-------------------------------------------------------------------------------------------');
            BufferLog::Debug('On order updated');
            BufferLog::Debug('-------------------------------------------------------------------------------------------');
            BufferLog::Debug($domain);
            BufferLog::Debug('data  =>'.$data, 1, false);
            BufferLog::Debug('order =>'.print_r($order,true), 1, false);


            $shop = Shop::where('shopify_domain', $domain)->first();
            BufferLog::Debug('shop => '.print_r($shop, true));

            if (!$order)
            {
                throw new \Exception("No order recived", 404);
            }

            if(is_null($shop)){
                throw new \Exception("No shop found with domain {$domain}", 404);
            }

            $corders    = new ShopifyOrdersController();

            if ($trans = $corders->updateLocalOrder($shop, $order) ){
                BufferLog::Debug('Local Order Updated successfuly ');
            }


        }catch (Exception $e)
        {
            BufferLog::Debug('An error ocurrend in job -> '.$e->getMessage() );
        }

        BufferLog::Debug('------ End ----- ');
        BufferLog::Debug('');
        BufferLog::LogToFile($fileToLog);


        return true;


    }
}
