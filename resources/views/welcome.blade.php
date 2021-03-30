@extends('shopify-app::layouts.default')


@section('styles')
    <link rel="stylesheet"  href="{{ env('SHOPIFY_APP_URL')."/". ('css/shopify.css') }}">
@endsection

@section('content')
    <h2>Glovo Delivery for Shopify </h2>

    <section class="spy-width-medium-9-10">
        <div class="card">
            <div class="col-12 col-md-9">

                <h2 class="text-center" ><img src="{{ env('SHOPIFY_APP_URL') . '/assets/images/icons/shipping_glovo.png' }}">&nbsp;&nbsp; @lang('glovo.welcome.gettinstarted')</h2>

                <div class="text-center" style="margin: auto">
                    <iframe frameborder="0" scrolling="no" marginheight="0" marginwidth="0" width="90%" height="450" type="text/html" src="https://www.youtube.com/embed/HcFPf3OLsY4?autoplay=0&fs=1&iv_load_policy=3&showinfo=1&rel=0&cc_load_policy=0&start=0&end=0"><</iframe>
                </div>

                <br>
                <p> <h2>@lang('glovo.welcome.p1')</h2></p>

                <ol class="colored">

                    <li>
                        <h3> @lang('glovo.welcome.l2.t1')</h3>
                        <p> @lang('glovo.welcome.l2.t2.title')</p>
                        <div style="align-items: center; text-align: left">
                            <img src="{{ env('SHOPIFY_APP_URL')."/assets/images/settings-1.png" }}" width="95%" style="margin: auto">
                            <ul>
                                <li>@lang('glovo.welcome.l2.t2.helps.c1')</li>
                                <li>@lang('glovo.welcome.l2.t2.helps.c2')</li>
                                <li>@lang('glovo.welcome.l2.t2.helps.c3')</li>
                                <li>@lang('glovo.welcome.l2.t2.helps.c4')</li>
                                <li>@lang('glovo.welcome.l2.t2.helps.c5')</li>
                                <li>@lang('glovo.welcome.l2.t2.helps.c6')</li>
                                <li>@lang('glovo.welcome.l2.t2.helps.c7')</li>
                                <li>@lang('glovo.welcome.l2.t2.helps.c8')</li>
                            </ul>
                        </div>

                        <br>
                        <br>

                        <p>@lang('glovo.welcome.l2.t3.title')</p>

                        <div style="align-items: center;">
                            <img src="{{ env('SHOPIFY_APP_URL')."/assets/images/settings-2.png" }}" width="95%" style="margin: auto">
                        </div>

                        <ul>
                            <li>
                                @lang('glovo.welcome.l2.t3.helps.c1')
                                <div style="padding: 12px">
                                    <a target="_blank" href="{{ env('SHOPIFY_APP_URL')."/assets/images/setting-address-1.png" }}"><img src="{{ env('SHOPIFY_APP_URL')."/assets/images/setting-address-1.png" }}" width="40%" style="margin: auto"></a>
                                    <a target="_blank" href="{{ env('SHOPIFY_APP_URL')."/assets/images/setting-address-2.png" }}"><img src="{{ env('SHOPIFY_APP_URL')."/assets/images/setting-address-2.png" }}" width="40%" style="margin: auto"></a>
                                </div>
                            </li>
                            <li>@lang('glovo.welcome.l2.t3.helps.c2')</li>
                            <li>@lang('glovo.welcome.l2.t3.helps.c3')</li>
                        </ul>
                        <br>
                        <br>

                    </li>


                    <li>
                        <h3> @lang('glovo.welcome.l3.t1')  </h3>
                        <p> @lang('glovo.welcome.l3.t2') </p>
                        <div style="align-items: center; text-align: center">
                            <img src="{{ env('SHOPIFY_APP_URL')."/assets/images/products-1.jpg" }}" width="95%" style="margin: auto">
                        </div>
                        <br>
                        @lang('glovo.welcome.l3.t3')
                        <div style="align-items: center; text-align: center">
                            <img src="{{ env('SHOPIFY_APP_URL')."/assets/images/products-2.jpg" }}" width="95%" style="margin: auto">
                        </div>

                    </li>

                    <li>
                        <h3> @lang('glovo.welcome.l4.t1') </h3>
                        <p> @lang('glovo.welcome.l4.t2') </p>
                        <div style="align-items: center; text-align: center">
                            <img src="{{ env('SHOPIFY_APP_URL')."/assets/images/frontstore-product-1.png" }}" width="95%" style="margin: auto">
                        </div>
                        <br>
                    </li>

                    <li>
                        <h3> @lang('glovo.welcome.l5.t1')  </h3>
                        <p> @lang('glovo.welcome.l5.t2') </p>
                        <div style="align-items: center; text-align: center">
                            <img src="{{ env('SHOPIFY_APP_URL')."/assets/images/frontstore-cart-1.png" }}" width="95%" style="margin: auto">
                        </div>
                        <br>
                    </li>
                </ol>

            </div>
        </div>
    </section>


    <section>
        <div style="text-align: center" class="spy-width-medium-9-10">
            <ul style="width: 250px; margin: auto">

                <li>
                    <span class="note">Contact us </span>
                    <span class="email"><a target="_blank" href="mailto:soporte@vexsoluciones.com">soporte@vexsoluciones.com</a></span>
                </li><br>
            </ul>
        </div>
    </section>








@endsection

@section('scripts')
    @parent

@endsection
