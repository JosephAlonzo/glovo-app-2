<?php namespace App\Jobs;


use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Request;


use App\Http\Controllers\Shopify\ShopifyOrdersController;
use App\Models\EMVexstore;
use Mockery\Exception;
use OhMyBrew\ShopifyApp\Models\Shop;
use Illuminate\Support\Facades\Log;
use Vexsolutions\Utils\Logger\Facades\BufferLog;


class OrdersCreatedJob implements ShouldQueue
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
        $content        = file_get_contents("php://input");
        $order          =   ($content);

        // Do what you wish with the data
        $hmac       = Request::header('x-shopify-hmac-sha256') ?: '';
        $domain     = Request::header('x-shopify-shop-domain');
        $data       = Request::getContent();
        $store      = EMVexstore::where('STORE_DOMAIN',$domain)->get()->first();

        //for logs
        $carbonNow  = Carbon::now('America/Mexico_City');
        $fileToLog  = storage_path("logs/shopify/{$domain}/w/oc/".$carbonNow->format('Y-m-d') .".log");

        try
        {
            BufferLog::Debug("");
            BufferLog::Debug('-------------------------------------------------------------------------------------------');
            BufferLog::Debug('On order created');
            BufferLog::Debug('-------------------------------------------------------------------------------------------');
            BufferLog::Debug($domain);
            BufferLog::Debug('data  =>'.$data, 1, false);
            //BufferLog::Debug('order =>'.print_r($order,true), 1, false);


            $shop = Shop::where('shopify_domain', $domain)->first();
            BufferLog::Debug('shop => '.print_r($shop, true));


            if (!$order)
            {
                BufferLog::Debug('No order recived => ');
                return false;
            }

            $corders    = new ShopifyOrdersController();
            if ($trans = $corders->onCreated($order, $shop) ){
                BufferLog::Debug('Local Order create successfuly ');
            }

        }catch (Exception $e)
        {
            BufferLog::Debug('An error ocurrend in job -> '.$e->getMessage() );
        }

        BufferLog::Debug('------ End ----- ');
        BufferLog::Debug('');
        BufferLog::LogToFile($fileToLog);
        BufferLog::LogToFile(storage_path("logs/orders-".$carbonNow->format('Y-m-d') .".log"));




    }
}
