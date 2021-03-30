@extends('shopify-app::layouts.default')
@section('styles')
    <meta name="_token" content="{{ csrf_token() }}">
    <link rel="stylesheet"  href="{{ env('SHOPIFY_APP_URL')."/". ('css/shopify.css') }}">
    <link rel="stylesheet"  href="{{ env('SHOPIFY_APP_URL'). ('/assets/vendors/jquery-toogle/jtoggler.styles.css') }}">
@endsection


@section('content')

    <header>
        <article>
            <h2>@lang('glovo.products.header.title')</h2>
            <h3>@lang('glovo.products.header.subtitle')</h3>
        </article>
    </header>

    <section class="spy-width-medium-9-10">
        <div>
            <input type="checkbox" class="jtoggler" data-jtlabel="Elegir y configurar de mi colleción de productos" data-jtlabel-success="Todos los productos estan disponibles para entrega inmmediata por Glovo."  value="allow_for_all" {{ ($emsetting->getEnableAllProducts() ? "checked" :"") }}>
        </div>
    </section>

    <section class="spy-width-medium-9-10" id="product-list" style="display: none">
            <div class="card">

                <div class="next-tab__container divider ">
                    <div class="ui-title-bar__actions-group">
                        <div class="ui-title-bar__actions ui-filter-product">
                        </div>
                    </div>
                </div>


                <table class="table bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th>@lang('glovo.products.table.headers.product')</th>
                            <th>@lang('glovo.products.table.headers.type')</th>
                            <th>@lang('glovo.products.table.headers.vendor')</th>
                            <th>@lang('glovo.products.table.headers.available')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr data-row-product="{{ $product->id }}">
                            <td width="60">
                                <a class="image-ratio image-ratio--square image-ratio--square--50 image-ratio--interactive" href="javascript:void(0)">
                                    @if ( isset( $product->images ))
                                        @if (is_array( $product->images) && count($product->images))
                                            <img title="{{ $product->title }}" class="image-ratio__content" src="{{ @$product->images[0] ? @$product->images[0]->src : '' }}">
                                        @else
                                           @php \Log::critical('images', [$product]); @endphp
                                        @endif
                                    @endif

                                </a>

                            </td>
                            <td><a data-product="{{ $product->id }}">{{ $product->title }}</a></td>
                            <td>{{ $product->product_type }}</td>
                            <td>{{ $product->vendor }}</td>
                            <td class="st">@php
                                $available_meta = $emprodmeta->where('PROD_PRODUCT',$product->id )->where('PROD_METADATA_KEY', 'available_for_glovo')->first();
                                if ($available_meta)
                                {
                                    $eta_meta   = $emprodmeta->where('PROD_PRODUCT',$product->id )->where('PROD_METADATA_KEY', 'preparation_time')->first();
                                    $minutes    = $eta_meta['PROD_METADATA_VALUE'];

                                    if ($eta_meta )
                                    {
                                        if ($minutes == "immediately")
                                        {
                                           echo ("<img src=". env('SHOPIFY_APP_URL'). "/assets/images/icons/clock.png" ." width=\"22\"/> &nbsp; <span>Immeditely</span>");

                                        }else
                                        {
                                            echo ("<img src=". env('SHOPIFY_APP_URL'). "/assets/images/icons/clock.png" ." width=\"22\"/> &nbsp;");
                                            echo(\Utils\Dates::minutesToHours($minutes,'%02d hours %02d minutes'));

                                        }

                                    }
                                }else
                                {

                                    echo __('glovo.preparationform.availability.no');
                                }

                                @endphp



                            </td>

                        </tr>
                    @endforeach



                    </tbody>
                </table>

                <div>
                    <ul class="pagination-simple" role="navigation">

                        <li class="page-item  {{ ($paginator->previous) ?  "" : "disabled"}}" aria-disabled="true" aria-label="« Previous">
                            <a class="page-link" href="{{ $paginator->previous ? $paginator->previous : "#" }}" rel="next" aria-label="Next »">
                                <img src="{{ asset('assets/images/icons/arrow_back.png') }}" width="32">
                            </a>
                        </li>

                        <li class="page-item {{ ($paginator->next) ?  "" : "disabled"}}">
                            <a class="page-link" href="{{ $paginator->next ? $paginator->next : "#" }}" rel="next" aria-label="Next »">
                                <img src="{{ asset('assets/images/icons/arrow_right.png') }}" width="32">
                            </a>
                        </li>
                    </ul>

                    {{--{{ $products->render() }}--}}
                </div>

            </div>
    </section >


@endsection




@section('scripts')
    @parent


    {{ Html::script( env('SHOPIFY_APP_URL') .'/assets/vendors/jquery-toogle/jtoggler.js') }}
    {{ Html::script( env('SHOPIFY_APP_URL') .'/assets/vendors/handlebars/handlebars.min.js') }}
    {{ Html::script( env('SHOPIFY_APP_URL') .'/assets/vendors/jquery-validate/jquery.validate.min.js') }}
    <script type="text/javascript">


        var jProducts = new function() {

            var $this       = this;
            var $filter     = null;

            this.init = function () {
                $this.AppBridge   = window['app-bridge'];
                $this.actions     = $this.AppBridge.actions;
                $this.TitleBar    = $this.actions.TitleBar;
                $this.Button      = $this.actions.Button;
                $this.Redirect    = $this.actions.Redirect;
                $this.Modal       = $this.actions.Modal;
                $this.Loading     = $this.actions.Loading;
                $this.Features    = $this.actions.Features;
                $this.Group       = $this.actions.Group;

                $this.toogle    = $('.jtoggler'); //switch
                $(document).on('jt:toggled', function(event, target) {
                    $this.enableWrapper(event, target);
                    $this.setAvailivility();
                });

                $this.toogle.jtoggler();
                if( $this.toogle.prop('checked') ==false ) {
                    $this.enableWrapper('click', $this.toogle);
                }

                $this.productClick();
                $this.setFilter();


            }


            this.enableWrapper  = function(event, target){
                if ($(target).prop('checked') ){
                    $('#product-list').slideUp();
                }else{
                    $('#product-list').slideDown();
                }
            }


            /**
             *
             */
            this.setAvailivility = function(){

                var enable = null;
                if ($this.toogle.prop('checked') ){
                    enable = "allow_for_all";
                }

                $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')}});


                $.ajax({
                    type: 'post',
                    url:  "{{ env("SHOPIFY_APP_URL") }}/products/availability",
                    data: {'allow_for_all': enable},
                    dataType : 'json',
                    beforeSend: function( xhr ) { }
                }).done(function( response ) {
                    if (response.success == 200)
                    {

                    }
                });
            }



            this.productClick   = function () {

                $('[data-product]').each(function (i,v) {
                    $(this).on('click',function () {
                        $this.openModal ( $(this).data('product'))
                    });
                });
            }

            this.openModal = function($product){

                /*var okButton        = $this.Button.create(app, { label: 'Save'});
                var cancelButton    = $this.Button.create(app, {label: 'Cancel'});
                okButton.subscribe($this.Button.Action.CLICK, () => {
                    // Do something with the click action
                });
                cancelButton.subscribe($this.Button.Action.CLICK, () => {
                    $this.myModal.dispatch($this.Button.Action.CLOSE, {'e':'levi'});
                });



                var  modalOptions = {
                    title: '@lang('glovo.preparationform.modal.title')',
                    url: "{{ env("SHOPIFY_APP_URL"). "/products/preparation/form?product=" }}" + $product,
                    size: $this.Modal.Size.Auto,
                    footer: {
                        buttons: {
                            primary: okButton,
                            secondary: [cancelButton],
                        },
                    },
                };


                $this.myModal = $this.Modal.create(app, modalOptions);

                $this.myModal.subscribe($this.Modal.Action.CLOSE, data => {
                    // Do something with the close event
                    console.log(data)
                });


                $this.myModal.subscribe($this.Modal.Action.OPEN, (data,tres) => {
                    // Do something with the open event
                    console.log("data")
                    console.log(data)
                    console.log(tres)
                });


                var closeUnsubscribe = $this.myModal.subscribe($this.Modal.Action.CLOSE, (data) => {
                    // Do something with the close event
                    console.log(data)
                });



                // Unsubscribe to actions
                //openUnsubscribe('sdsdsd');
                //closeUnsubscribe('sdsdsd');
                $this.myModal.dispatch($this.Modal.Action.OPEN, {
                    text: "Hey check this out!",
                    url: "https://www.reallyawesomesite.com"
                });


                var features = $this.Features.create(app);
                features.subscribe($this.Features.Action.REQUEST_UPDATE, function (payload) {
                    alert('Putos')
                });*/




                // Unsubscribe to actions
                //openUnsubscribe();
                //closeUnsubscribe();

                ShopifyApp.Modal.open({
                    src: "{{ env("SHOPIFY_APP_URL"). "/products/preparation/form?product=" }}" + $product,
                    title: '@lang('glovo.preparationform.modal.title')',
                    width: 'small',
                    height: 300,
                    data : {varw:'e'},
                    type: 'POST',
                    buttons: {
                        secondary: [
                            { label: "Cancel", callback: function (label) { ShopifyApp.Modal.close();  } }
                        ]
                    }
                }, function(event, data){


                    if (event == 'aftersave'){
                        if (data.success == true)
                        {

                            $('[data-row-product='+data.product+']').find('.st').html(data.text);
                            ShopifyApp.flashNotice(data.message);

                        }else if(data.success == false)
                        {
                            ShopifyApp.flashError(data.errors.message)
                        }
                    }



                    if (event == 'afterdelete'){

                        if (data.success == true)
                        {

                            $('[data-row-product='+data.product+']').find('.st').html('');
                            ShopifyApp.flashNotice(data.message);

                        }else if(data.success == false)
                        {
                            ShopifyApp.flashError(data.errors.message)
                        }
                    }
                });
            }


            this.setFilter  = function (){

                let url = new URL(window.location.href);
                let addel = $("<a href=\"javascript: return void(0);\" class=\"ui-button ui-filter-product ui-button--primary ui-title-bar__action\"  data-allow-default=\"1\">@lang('glovo.products.filters.apply')</a>");
                let remel = $("<a href=\"javascript: return void(0);\" class=\"ui-button ui-filter-product ui-button--primary ui-title-bar__action\"  data-allow-default=\"1\">@lang('glovo.products.filters.remove')</a>");

                let parent = $('.ui-filter-product');

                var addFilter    = function (parent, el){
                    let url = new URL(window.location.href);
                    parent.html(el);
                    el.click(function (e) {
                        ShopifyApp.Modal.productPicker({
                            showHidden:true,
                            selectMultiple: true
                        }, function (success, data) {
                            let items = new Array();

                            if ( success ){

                                if (data.products.length > 0) {
                                    var selectedProducts = data.products;
                                    $.each(data.products, function (i,product) {
                                        items.push(product.id);
                                    });

                                    url.searchParams.set('ids',items.join(","));
                                    window.location.href = url.href;
                                }
                            }
                        });
                    });
                }


                var removefilter = function (parent, el, url){
                    el.click(function () {
                        url.searchParams.delete('ids');
                        window.location.href = url.href;
                    });
                    parent.html(el);

                }


                //is filtered
                if (url.searchParams.has('ids')) {
                    removefilter(parent, remel, url);
                }else

                {

                    addFilter(parent, addel);

                }



            }

            this.callback   = function () {
                console.log($(location).attr('href'))
            }
        }


        // Shorthand for $( document ).ready()
        $(function() {
            jProducts.init();
        });


    </script>

@endsection
