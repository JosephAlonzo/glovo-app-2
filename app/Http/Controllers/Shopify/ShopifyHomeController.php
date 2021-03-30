<?php

namespace App\Http\Controllers\Shopify;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use Log;
use Exception;
use DateTime;
use DateTimeZone;
use Carbon\Carbon;


use OhMyBrew\ShopifyApp\Facades\ShopifyApp;
use OhMyBrew\ShopifyApp\Models\Shop;
use OhMyBrew\ShopifyApp\Services\ShopSession;


use App\Http\Controllers\Glovo\GlovoController;

use App\Models\EMVexsetting;
use App\Models\EMVexlanguages;
use App\Models\Store\EMVexhollyday;
use App\Models\Store\EMVexhours;
use App\Models\Store\EMVexlocations;
use App\Models\EMVexglovoorders;
use App\Models\EMVexstore;
use Escom\Base\CBase;
use Utils\UStore;


class ShopifyHomeController extends Controller
{

    public function __construct()
    {
        app('translator')->setLocale('en');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {

        $shop           = ShopifyApp::shop();
        $shopifyapi     = $shop->api()->rest('GET', '/admin/api/'.\Config('shopify-app.api_version').'/shop.json');

        if ( $shopifyapi->errors ){
            return redirect('login');
        }

        $shopifyshop    = $shopifyapi->body->shop;
        $settings       = new EMVexsetting();
        $default_lang   = UStore::DefLang($shopifyshop);

        $emsettings   = EMVexsetting::findByStoreId($shopifyshop->id);
        if ( $emsettings)
        {
            if (!$emsettings->trashed() ) //if null
            {
                $default_lang = $emsettings->getLanguage();

            }
        }

        $emstore        = EMVexstore::findByStoreId($emsettings->getStoreId());
        $emstore->setEmail($shopifyshop->customer_email );
        $emstore->save();




        app('translator')->setLocale($default_lang);


        return view('welcome');

    }

}
