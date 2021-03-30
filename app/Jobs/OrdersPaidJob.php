<?php namespace App\Jobs;

use App\Models\EMVexglovoorders;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Exception;


use App\Http\Controllers\Shopify\ShopifyOrdersController;
use App\Models\EMVexstore;
use App\Models\EMVexsetting;
use OhMyBrew\ShopifyApp\Models\Shop;
use Vexsolutions\Utils\Logger\Facades\BufferLog;


class OrdersPaidJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string $shopDomain The shop's myshopify domain
     * @param object $data    The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        BufferLog::Debug("");
        BufferLog::Debug("");
        BufferLog::Debug("");
        BufferLog::Debug("-------------------------------------------------------------------------------------------");
        BufferLog::Debug('   Listening order paid');
        BufferLog::Debug('-------------------------------------------------------------------------------------------');
        BufferLog::Debug('webhook orders-paid');

        try
        {
            $content        = file_get_contents("php://input");
            $order          = json_decode($content);


            BufferLog::Debug('Order -> '. $content);


            // Do what you wish with the data
            $hmac           = Request::header('x-shopify-hmac-sha256') ?: '';
            $domain         = Request::header('x-shopify-shop-domain');
            $topic          = Request::header('x-shopify-topic');
            $data           = Request::getContent();


            BufferLog::Debug("domain : $domain");


            //for logs
            $carbonNow  = Carbon::now('America/Mexico_City');
            $fileToLog  = storage_path("logs/shopify/{$domain}/w/op/".$carbonNow->format('Y-m-d') .".log");

            $store          = EMVexstore::where('STORE_DOMAIN',$domain)->first();
            $shop           = Shop::where('shopify_domain', $domain)->first();
            $corders        = new ShopifyOrdersController();

            // Verify glovo order
            $glovoOrder     = EMVexglovoorders::findByOrderId($order->id);


            //local settings
            $emstore    = EMVexstore::findByDomain($domain);
            if ( is_null($emstore))
            {
                throw new Exception("Not found domain $domain in table vex_store_general",404);
            }

            $settings   = EMVexsetting::findByStoreId($emstore->getId());
            app('translator')->setLocale($settings->getLanguage());


            #for log events
            if (count($order->shipping_lines) == 0){
                BufferLog::Debug('Order no shipping_lines');
            }


            BufferLog::Debug('Order Source Name ->'. $order->source_name);

            if( isset($order->shipping_lines) and count($order->shipping_lines)>0)
            {

                BufferLog::Debug('Cheking shipping method');
                BufferLog::Debug("Selected method -> ". (  (isset($order->shipping_lines) and is_array($order->shipping_lines)) ? $order->shipping_lines[0]->title : ''));

                #-------------------------------------------------------------------------------------------------------
                # Validar los que no tienen un plan avanzado o anual
                # En automatico solo pasan aquellos donde se installa el API del Carrier para el calculate rates
                #-------------------------------------------------------------------------------------------------------
                if ( $order->shipping_lines[0]->title === env('SHOPIFY_SHIPPING_TITLE'))
                {
                    BufferLog::Debug('User choose glovo delivering');
                    if (is_null($glovoOrder) and $order)
                    {
                        BufferLog::Debug('Proced to dispatch order to glovo:'.$order->id);
                        $newOrder       = $corders->createOrderToGlovo($order->id, $shop);

                    }elseif ( $glovoOrder )
                    {
                        BufferLog::Debug('The order to glovo has already been generated. :( ');
                    }
                }else
                {
                    BufferLog::Debug('Order not need proccessed ');
                }

            }else
            {

                    BufferLog::Debug('Order dont have shipping_lines ');
            }


        }catch (Exception $e)
        {
            BufferLog::Debug('An error ocurrend in job order-paid -> '.$e->getMessage() );
            Log::critical($e);
        }

        BufferLog::Debug('------ End ----- ');
        BufferLog::Debug('');
        BufferLog::LogToFile($fileToLog);
        BufferLog::LogToFile(storage_path("logs/orders-paid".$carbonNow->format('Y-m-d') .".log"));


    }
}
