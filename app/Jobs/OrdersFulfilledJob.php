<?php

namespace App\Jobs;

use App\Http\Controllers\Shopify\ShopifyOrdersController;
use App\Models\EMVexglovoorders;
use App\Models\EMVexsetting;
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

class OrdersFulfilledJob implements ShouldQueue
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
        $hmac           = Request::header('x-shopify-hmac-sha256') ?: '';
        $domain         = Request::header('x-shopify-shop-domain');
        $topic          = Request::header('x-shopify-topic');
        $data           = Request::getContent();
        $store          = EMVexstore::where('STORE_DOMAIN',$domain)->get()->first();

        //for logs
        $carbonNow  = Carbon::now('America/Mexico_City');
        $fileToLog  = storage_path("logs/shopify/{$domain}/w/of/".$carbonNow->format('Y-m-d') .".log");


        $shop           = Shop::where('shopify_domain', $domain)->first();
        $corders        = new ShopifyOrdersController();

        // Verify glovo order
        $glovoOrder     = EMVexglovoorders::findByOrderId($order->id);


        //local settings
        $emstore    = EMVexstore::findByDomain($domain);
        $settings   = EMVexsetting::findByStoreId($emstore->getId());
        app('translator')->setLocale($settings->getLanguage());

        BufferLog::Debug("");
        BufferLog::Debug("      |                  ");
        BufferLog::Debug("      ↓                  ");
        BufferLog::Debug("-------------------------------------------------------------------------------------------");
        BufferLog::Debug('   Listening order fulfilled');
        BufferLog::Debug('-------------------------------------------------------------------------------------------');
        BufferLog::Debug('webhook ');
        BufferLog::Debug('Order -> '. print_r($order,true));


        if( !empty($order->shipping_lines) and $order->shipping_lines[0]->title === env('SHOPIFY_SHIPPING_TITLE'))
        {
            BufferLog::Debug('User choose glovo delivering');
            if (is_null($glovoOrder) and $order)
            {
                BufferLog::Debug('Proced to dispatch order:'.$order->id);
                $newOrder       = $corders->createOrderToGlovo($order->id, $shop);

            }elseif ( $glovoOrder )
            {
                BufferLog::Debug('The order has already been processed. :( ');
            }
        }

        BufferLog::Debug('------ End ----- ');
        BufferLog::Debug('');
        BufferLog::LogToFile($fileToLog);
    }
}
