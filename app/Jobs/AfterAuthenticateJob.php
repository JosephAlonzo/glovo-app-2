<?php

namespace App\Jobs;

use App\Http\Controllers\Shopify\ShopifyApplication;
use App\Http\Controllers\Shopify\ShopifyOrdersController;
use App\Models\EMVexstore;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use OhMyBrew\ShopifyApp\Models\Shop;
use Vexsolutions\Utils\Logger\Facades\BufferLog;
use Illuminate\Support\Facades\DB;
use App\User;

class AfterAuthenticateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shop)
    {
        $this->shop = $shop;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        BufferLog::Debug("");
        BufferLog::Debug("-------------------------------------------------------------------------------------------");
        BufferLog::Debug("                AFTER INSTALL                                                              ");
        BufferLog::Debug("-------------------------------------------------------------------------------------------");

        if (is_null($this->shop)){
            return false;
        }

        try
        {

            BufferLog::Debug('shop => '.print_r($this->shop, true));

            //for logs
            $carbonNow  = Carbon::now('America/Mexico_City');
            $fileToLog  = storage_path("logs/shopify/{$this->shop->shopify_domain}/app/install-".$carbonNow->format('Y-m-d') .".log");

            $shopifystore   = $this->shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json')->body->shop;
            BufferLog::Debug(' Executing job After Install');

            $shopify        = new ShopifyApplication();
            $shopify->onInstall($this->shop, $shopifystore, $shopifystore->id);
            DB::commit();

        }catch (Exception $e)
        {
            BufferLog::Debug('An error ocurrend in job order-paid -> '.$e->getMessage() );
            Log::critical($e);
        }

        BufferLog::Debug('------ End ----- ');
        BufferLog::Debug('');
        BufferLog::LogToFile($fileToLog);


    }
}
