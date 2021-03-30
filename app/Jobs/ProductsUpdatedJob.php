<?php namespace App\Jobs;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


use App\Models\Products\EMProduct;
use App\Models\EMVexstore;

class ProductsUpdatedJob implements ShouldQueue
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
        // Do what you wish with the data
        $hmac = Request::header('x-shopify-hmac-sha256') ?: '';
        $shop = Request::header('x-shopify-shop-domain');
        $data = Request::getContent();
        $store = EMVexstore::where('STORE_DOMAIN',$shop)->get()->first();

        $content            = file_get_contents("php://input");
        $shproduct          = json_decode($content);


        if (!$shproduct)
        {
            return false;
        }



        $emproduct = EMProduct::find($shproduct->id);

        if (is_null($emproduct))
        {


            $emproduct = new  EMProduct();
            $emproduct->PROD_PRODUCT    = $shproduct->id;
            $emproduct->PROD_STORE      = $store->STORE_ID;
            $emproduct->PROD_NAME       = $shproduct->title;
            $emproduct->PROD_IMAGE      = $shproduct->images[0]->src;
            $emproduct->save();

            $client = new \GuzzleHttp\Client([
                'base_uri' => 'http://34.66.29.197',
                'headers'  => ['Content-Type'=> 'application/json']
            ]);
            $response = $client->request('POST', '/api/api-token-auth/', ['json' => ['username' => 'admin', 'password'=>"secure_password"]]);

            $token = (json_decode($response->getBody()))->token;

            $data_product   = [
                'product_id'            => $shproduct->id,
                'product_image_main_url'=>$shproduct->images[0]->src
            ];
            $headers        = [
                'Authorization' => 'Token ' . $token,
                'Accept'        => 'application/json',
            ];
            $AIProduct = $client->request('POST', '/api/product/create/', ['json' => $data_product, 'headers' => $headers]);
            $res  = $AIProduct->getBody()->getContents();

            $o=0;

        }

    }
}
