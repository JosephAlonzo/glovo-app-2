<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use Exception;

use App\Http\Controllers\Shopify\ShopifyApplication;
use App\Http\Controllers\Shopify\ShopifySettingsController;
use App\Models\EMVexsetting;
use OhMyBrew\ShopifyApp\Models\Shop;
use App\Models\EMVexstore;
use OhMyBrew\ShopifyApp\Services\ShopSession;
use Vexsolutions\Utils\Logger\Facades\BufferLog;

class AppUninstalledJob extends \OhMyBrew\ShopifyApp\Jobs\AppUninstalledJob
{

    /**
     * Shop's instance.
     *
     * @var string
     */
    protected $shop;
    /**
     * Shop's myshopify domain.
     *
     * @var string
     */
    protected $shopDomain;
    /**
     * The webhook data.
     *
     * @var object
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param string $shopDomain The shop's myshopify domain
     * @param object $data       The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->data = $data;
        $this->shopDomain = $shopDomain;
        $this->shop = $this->findShop();
    }




    /**
     * Run when de applications was uninstalled
     *  Softdelete for setting model
     * @return bool
     */
    public function handle()
    {

        $content        = file_get_contents("php://input");
        $datashop       = json_decode($content);

        try
        {

            BufferLog::Debug("");
            BufferLog::Debug("-------------------------------------------------------------------------------------------");
            BufferLog::Debug("                UNINSTALLING                                                               ");
            BufferLog::Debug("-------------------------------------------------------------------------------------------");
            BufferLog::Debug("shopifyshop =>" .print_r($datashop, true));

            // Do what you wish with the data
            $hmac           = Request::header('x-shopify-hmac-sha256') ?: '';
            $domain         = Request::header('x-shopify-shop-domain');
            $data           = Request::getContent();
            $store          = EMVexstore::where('STORE_DOMAIN',$domain)->get()->first();

            BufferLog::Debug("shopify_domain =>" .$domain);


            //for logs
            $carbonNow      = Carbon::now('America/Mexico_City');
            $fileToLog      = storage_path("logs/shopify/{$domain}/w/au/".$carbonNow->format('Y-m-d') .".log");

            $shop           = Shop::where('shopify_domain', $domain)->first();

            if (!$datashop or !$shop){
                return false;
            }

            BufferLog::Debug("cleaning data ... " );
            $this->cancelCharge();
            $this->cleanShop();
            $this->softDeleteShop();

            BufferLog::Debug("cleaning data ... OK" );

            $shopify    = new ShopifyApplication();
            $shopify->onUnInstall($shop, $datashop->id);

            BufferLog::Debug("App onUnInstall... OK" );


        }catch (Exception $e)
        {
            BufferLog::Debug('An error ocurrend in job -> '.$e->getMessage() );
        }

        BufferLog::Debug('------ End ----- ');
        BufferLog::Debug('');
        BufferLog::LogToFile($fileToLog);

        #$shopsession = new ShopSession();
        #$shopsession->forget();

        return true;
    }










}
