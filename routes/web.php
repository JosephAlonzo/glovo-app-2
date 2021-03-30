<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group( ['middleware' => ['web']], function () {
    Route::get('/'                          , function (){ return view('Layouts.public');});

    Route::get('home'                       , 'Shopify\ShopifyHomeController@index')->middleware(['auth.shop','billable'])->name('home');
    Route::get('settings'                   , 'Shopify\ShopifySettingsController@index')->middleware(['auth.shop','billable'])->name('shopify.settings');
    Route::post('settings/save'             , 'Shopify\ShopifySettingsController@save')->middleware(['auth.shop'])->name('shopify.settings.save');
    Route::post('settings/test'             , 'Shopify\ShopifySettingsController@_validateConfigFromForm')->middleware(['auth.shop'])->name('shopify.settings.test');

    Route::get('products'                   , 'Shopify\ShopifyProductsController@index')->middleware(['auth.shop','billable'])->name('shopify.products.index');
    Route::get('products/save'              , 'Shopify\ShopifyProductsController@save')->middleware(['auth.shop'])->name('shopify.products.save');
    Route::get('products/preparation/form'  , 'Shopify\ShopifyProductsController@preparationForm')->middleware(['auth.shop'])->name('shopify.products.preparation.form');
    Route::post('products/preparation/save' , 'Shopify\ShopifyProductsController@preparationTimeSave')->middleware(['auth.shop'])->name('shopify.products.preparation.save');
    Route::post('products/preparation/del'  , 'Shopify\ShopifyProductsController@preparationTimeDelete')->middleware(['auth.shop'])->name('shopify.products.preparation.delete');
    Route::post('products/availability'     , 'Shopify\ShopifyProductsController@setAvailability')->middleware(['auth.shop'])->name('shopify.products.availability');

    Route::get ('orders'                    , 'Shopify\ShopifyOrdersController@index')->middleware(['auth.shop'])->name('shopify.orders.index');
    Route::get ('orders/{id}/detail'        , 'Shopify\ShopifyOrdersController@detail')->middleware(['auth.shop'])->name('shopify.orders.detail');
    Route::post('orders/glovo/create'       , 'Shopify\ShopifyOrdersController@createOrderToGlovoManager')->middleware(['auth.shop'])->name('shopify.orders.glovo.create');
    Route::post('orders/glovo/resend'       , 'Shopify\ShopifyOrdersController@resendOrderToGlovo')->middleware(['auth.shop'])->name('shopify.orders.glovo.resend');
    Route::get ('orders/tracking/'          , 'Shopify\ShopifyOrdersController@tracking')->name('shopify.order.tracking');
    Route::post('orders/tracking/live'      , 'Shopify\ShopifyOrdersController@getLive')->name('shopify.order.tracking.live');
    Route::post('orders/tracking/currier'   , 'Shopify\ShopifyOrdersController@getCourierContact')->name('shopify.order.tracking.currier');
    Route::get ('orders/resend'             , 'Shopify\ShopifyOrdersController@cancel')->middleware(['auth.shop'])->name('shopify.orders.cancel');

    Route::match(['get', 'post']            , 'webhooks/customers-data-request','Shopify\ShopifyWebHookController@CustomerDataRequest' )->name('customers.data.request');
    Route::match(['get', 'post']            , 'webhooks/customers-redact','Shopify\ShopifyWebHookController@CustomerRedact' )->name('customers.redact');
    Route::match(['get', 'post']            , 'webhooks/shop-redact' ,'Shopify\ShopifyWebHookController@ShopRedact' )->name('shop.redact');

});

Route::post('serviceavailability.json'  , 'Shopify\ShopifyApplication@getServiceAvaliable')->name('shopify.setting.availability');
Route::post('productavailability.json'  , 'Shopify\ShopifyApplication@getProductAvaliable')->name('shopify.product.availability');
Route::post('workingdays.json'          , 'Shopify\ShopifyApplication@getWorkingDays')->name('shopify.setting.workingdays');
Route::post('workingtime.json'          , 'Shopify\ShopifyApplication@getWorkingTimes')->name('shopify.setting.workingtimes');


Route::get ('tracking'                  , 'Shopify\ShopifyApplication@tracking')->name('shopify.order.send');
Route::any('carrier/rate'               , 'Shopify\ShopifyApplication@rate')->name('shopify.shipping.rate');


Route::any('carrier/rates'      , 'Shopify\ShopifyApplication@rate')->name('shopify.shipping.rate');
Route::get('carrier/list'       , 'Shopify\ShopifyOrdersController@listCarrier')->name('shopify.carrier.list');
Route::get('carrier/{id}/delete', 'Shopify\ShopifyOrdersController@removeCarrier')->name('shopify.carrier.remove');
Route::get('carrier/create'     , 'Shopify\ShopifyOrdersController@registerCarrier')->name('shopify.carrier.create');

Route::get('webhook/list'       , 'Shopify\ShopifyApplication@webhooklist')->middleware(['auth.shop'])->name('shopify.webhook.list');
Route::get('webhook/create'     , 'Shopify\ShopifyApplication@webhookcreate')->middleware(['auth.shop'])->name('shopify.webhook.create');

Route::any('ai/token'           , 'Shopify\ShopifyApplication@AIToken')->name('AI.token');
Route::any('theme/list'         , 'Shopify\ShopifyApplication@ThemeList')->middleware(['auth.shop'])->name('theme.list');
Route::any('script/list'        , 'Shopify\ShopifyApplication@ScriptList')->middleware(['auth.shop'])->name('script.list');
Route::any('metadata/list'      , 'Shopify\ShopifyApplication@MetadataList')->middleware(['auth.shop'])->name('meta.list');


Route::any('command'            , 'Shopify\ShopifyApplication@command')->name('application.command');
Route::any('orders/{id}/email'  , 'Shopify\ShopifyOrdersController@ManagerMailTracking')->name('order.email');
Route::any('orders/{id}/failed' , 'Shopify\ShopifyOrdersController@ManagerOrderFailed')->name('order.failed');



