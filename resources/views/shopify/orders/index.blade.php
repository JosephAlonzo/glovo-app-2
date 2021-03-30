@extends('shopify-app::layouts.default')
@section('styles')
    <link rel="stylesheet"  href="{{ env('SHOPIFY_APP_URL')."/". ('css/shopify.css') }}">
@endsection


@section('content')

    @if (is_null($emstore->STORE_CARRIER_ID))
        <article>
            <div style="width: 100% !important;" class="alert warning">
                <dl>
                    <dd>@lang('glovo.validatedplan.noallow')</dd>
                </dl>
            </div>
        </article>
    @endif


    <header>
        <article>
            <h2>@lang('glovo.orders.header.title')</h2>
            <h3>@lang('glovo.orders.header.subtitle')</h3>
        </article>
    </header>


    <section class="spy-width-medium-9-10">
            <div class="card">


                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('glovo.orders.table.headers.order')</th>
                            <th>@lang('glovo.orders.table.headers.date')</th>
                            <th>@lang('glovo.orders.table.headers.customer')</th>
                            <th>@lang('glovo.orders.table.headers.deliveryaddress')</th>
                            <th>@lang('glovo.orders.table.headers.paid')</th>
                            <th>@lang('glovo.orders.table.headers.glovostatus')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $orden)
                        @php
                            $orden->customer            = json_decode($orden->customer);
                            $orden->shipping_address    = json_decode($orden->shipping_address);
                        @endphp
                        <tr>
                            <td><a href="{{ env('SHOPIFY_APP_URL')."/orders/{$orden->id}/detail?". \Illuminate\Support\Facades\Request::getQueryString() }}">{{ $orden->name }}</a> </td>
                            <td>{{ $orden->created_at }}</td>
                            <td>{{ @$orden->customer->first_name. " ". @$orden->customer->last_name }}</td>
                            <td>
                                {{ @$orden->shipping_address->address1 ." ". @$orden->shipping_address->address2 ." ".@$orden->shipping_address->city ." ".@$orden->shipping_address->province ." ".@$orden->shipping_address->zip ." ".@$orden->shipping_address->country

                                 }}
                            </td>
                            <td>{{ $orden->financial_status }}</td>
                            <td>
                                @php

                                    $state       = null;
                                    $emglovoorder = $emglovoorders->where('ORGL_ORDER_ID', $orden->id)->first();

                                    if(!is_null($emglovoorder)) {
                                       if ( is_null($state = $emglovoorder->getState())) {
                                            $state  = $emglovoorder->getStatus();
                                       }

                                    }

                                @endphp

                                @if($state== 'SCHEDULED' ||$state == 'ACTIVE' || $state == 'DELIVERED')
                                    <label class="success"> {{ $state }}</label>
                                @elseif($state == 'CANCELED' || $state == 'FAILED')
                                    <label class="error"> {{ $state }}</label>
                                @elseif(is_null($state))
                                    <label> PENDING </label>
                                @endif


                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

                <div>
                    {{--<ul class="pagination-simple" role="navigation">

                        <li class="page-item  {{ ($paginator->previous) ?  "" : "disabled"}}" aria-disabled="true" aria-label="« Previous">
                            <a class="page-link" href="{{ $paginator->previous ? $paginator->previous : "#" }}" rel="next" aria-label="Next »">
                                <img src="{{ env("SHOPIFY_APP_URL") . ('/assets/images/icons/arrow_back.png') }}" width="32">
                            </a>
                        </li>

                        <li class="page-item {{ ($paginator->next) ?  "" : "disabled"}}">
                            <a class="page-link" href="{{ $paginator->next ? $paginator->next : "#" }}" rel="next" aria-label="Next »">
                                <img src="{{ env("SHOPIFY_APP_URL") . ('/assets/images/icons/arrow_right.png') }}" width="32">
                            </a>
                        </li>
                    </ul>--}}

                    {{ $orders->links() }}
                </div>



            </div>
    </section>


@endsection




@section('scripts')
    @parent


    {{ Html::script( env('SHOPIFY_APP_URL') .'/assets/vendors/handlebars/handlebars.min.js') }}
    {{ Html::script( env('SHOPIFY_APP_URL') .'/assets/vendors/jquery-validate/jquery.validate.min.js') }}


@endsection
