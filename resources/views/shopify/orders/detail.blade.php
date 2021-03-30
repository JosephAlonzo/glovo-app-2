@extends('shopify-app::layouts.default')

@section('styles')
    <link rel="stylesheet"  href="{{ env('SHOPIFY_APP_URL')."/". ('css/shopify.css') }}">
@endsection


@section('content')

    <div class="uk-grid">
        <div class="order-header">
            <div class="heading-group">
                <h1 class="order-header-bar-title"> {{ $shopifyorder->name }} </h1>
                <span class="meta-data">
                {{  \Carbon\Carbon::parse($shopifyorder->created_at)->isoFormat('MMMM D, YYYY \at h:mm A')}}.
                </span>
                <span class="meta-data">
                    {{ ucfirst($shopifyorder->financial_status) }}
                </span>
            </div>
        </div>
    </div>


    @include('shopify.orders.errorglovo')


    <div class="uk-grid margin-top-5">

        <div class="uk-width-small-1-1 uk-width-medium-4-10 uk-grid-margin">
            <div class="md-card">
                <div class="order-details__summary">
                    <div class="payment-header">
                        <div class="payment-wrapper">
                            <div class="payment-paid-icon">
                                @if($shopifyorder->financial_status=='paid')
                                    <img src="{{ env('SHOPIFY_APP_URL') . ("/svg/pay_success.svg") }}" width="32">
                                @elseif($shopifyorder->financial_status=='authorized')
                                    <img src="{{ env('SHOPIFY_APP_URL') . ("/assets/images/icons/autorized.png") }}" width="32">
                                @endif

                            </div>
                            <div class="payment-paid-info">
                                <h2>{{ $shopifyorder->financial_status }}</h2>
                            </div>
                        </div>

                    </div>

                    <table class="table condensed-4  order-details-summary-table" role="table">
                        <tbody>
                            <tr>
                                <td>Subtotal</td>
                                <td> {{ $shopifyorder->quantity }} items </td>
                                <td class="text-right">$ {{ $shopifyorder->subtotal_price }}</td>
                            </tr>

                            <tr>
                                <td width="1%"> Shipping </td>
                                <td> {{ $shopifyorder->shipping_lines[0]->title }}</td>
                                <td class="text-right"> $ {{ $shopifyorder->shipping_lines[0]->price_set->shop_money->amount }} </td>
                            </tr>
                        </tbody>
                        <tbody class="">
                            <tr class="">
                                <td> Tax </td>
                                <td>  {{ @$shopifyorder->tax_lines[0]->title }} {{@$shopifyorder->tax_lines[0]->rate*100 }}%</td>
                                <td class="text-right">$ {{ $shopifyorder->total_tax }}</td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td class="text-bold" colspan="2">Total</td>
                                <td class="text-bold text-right">$ {{ $shopifyorder->total_price }} </td>
                            </tr>
                        </tbody>
                        <tbody class="order-details__summary__paid_by_customer">
                            <tr>
                                <td colspan="3" class="order-details-summary-table__separator"><hr></td>
                            </tr>
                            <tr>
                                <td colspan="2">Paid by customer</td>
                                <td class="text-right">$ {{ $shopifyorder->total_price }}</td>
                            </tr>
                        </tbody>

                    </table>

                </div>
            </div>
            <div class="md-card">
                <div class="md-card-content">
                    <table class="table bordered condensed">

                        @foreach($shopifyorder->line_items as $item)
                            @php
                               $product = $shopifyproducts->where('id', $item->product_id )->first();
                               $image   = ($product ? $product->image : null);

                            $metas  = $item->metas;


                            @endphp

                            <tr>
                                <td width="50">
                                    @if ($image)
                                        <a class="image-ratio image-ratio--square image-ratio--square--50 image-ratio--interactive" href="javascript:void(0)">
                                            <img title="{{ $product->title }}" class="image-ratio__content" src="{{ $image->src }}">
                                        </a>
                                    @endif
                                </td>
                                <td>{{ $item->name }}</td>
                                <td class="text-right"> {{ $item->price_set->shop_money->amount}} x {{ $item->quantity }}</td>
                                <td class="text-right"> {{ number_format(($item->price_set->shop_money->amount *  $item->quantity), 2) }} </td>
                            </tr>
                        @endforeach
                    </table>

                </div>


            </div>
        </div>

        <div class="uk-width-small-1-1 uk-width-medium-3-10 uk-grid-margin">

            <div class="md-card">
                <div class="md-card-content">
                    <div class="md-card-section">
                        <h3 class="ui-subheading no-padding no-margin">@lang('glovo.orderdetail.pickupaddress.title')</h3>
                        <div class="divider margin-bottom-5 margin-top-5"></div>
                        <p class="text-left" data-protected-personal-information>

                            {{ $shopifylocation->name }}  <br>
                            {{ $shopifylocation->address1 }}<br>
                            @if(!empty($shopifylocation->address2))
                                {{ $shopifylocation->address2 }} <br>
                            @endif

                            {{ $shopifylocation->zip }} <br>
                            {{ $shopifylocation->city }}, {{ $shopifylocation->province }}, {{ $shopifylocation->country }}
                            @if(!empty($shopifylocation->phone))
                                <br><small> {{ $shopifylocation->phone }} </small>
                            @endif


                        </p>

                        @php
                            use Carbon\Carbon;
                            $notes          = collect($shopifyorder->note_attributes)->keyBy('name');

                        @endphp

                        @if ($notes->has('glovo_when_receive'))
                            <div class="divider margin-bottom-5 margin-top-5"></div>
                            <p class="text-left" data-protected-personal-information>

                                @if($notes->get('glovo_when_receive')->value == 'scheduled')
                                    @lang('glovo.orderdetail.delivery.scheduled') : <br>{{ Carbon::createFromFormat('YmdHi', $notes->get('glovo_schedule_time')->value, $emstore->getTimeZone() )->setTimezone( $emstore->getTimeZone() )->isoFormat('LLL') }}
                                @else
                                    @lang('glovo.orderdetail.delivery.immmediately') : IMMEDIATELY
                                @endif
                            </p>
                        @endif


                    </div>
                </div>


            </div>


            <div class="md-card">
                <div class="md-card-content">
                    <div class="md-card-section">
                        <h3 class="ui-subheading no-padding no-margin margin-bottom-20">@lang('glovo.orderdetail.contact.title')</h3>
                        <div class="divider margin-bottom-5 margin-top-5"></div>
                        <div class="text-left customer-contact">
                            <p>{{ $shopifyorder->customer->first_name }} {{ $shopifyorder->customer->last_name }}</p>
                            <p><a href="mailto:{{  $shopifyorder->customer->email}}"> {{ $shopifyorder->customer->email }}</a></p>
                            <p>{{ $shopifyorder->customer->phone }}</p>

                        </div>
                    </div>


                    <div class="md-card-section">
                        <h3 class="ui-subheading no-padding no-margin">@lang('glovo.orderdetail.destination.title')</h3>
                        <div class="divider margin-bottom-5 margin-top-5"></div>
                        <p class="text-left" data-protected-personal-information>

                            {{ $shopifyorder->shipping_address->first_name }} {{  $shopifyorder->shipping_address->last_name }} <br>
                            {{ $shopifyorder->shipping_address->address1 }}<br>
                            {{ $shopifyorder->shipping_address->address2 }} <br>
                            {{ $shopifyorder->shipping_address->zip }} <br>
                            {{ $shopifyorder->shipping_address->city }}, {{ $shopifyorder->shipping_address->province }}, {{ $shopifyorder->shipping_address->country }}
                            <small> {{ $shopifyorder->shipping_address->phone }} </small>


                        </p>

                        @php
                            $address     = $shopifyorder->shipping_address->address1. " ".$shopifyorder->shipping_address->address2. ", ".$shopifyorder->shipping_address->city. ", ".$shopifyorder->shipping_address->province. ", ".$shopifyorder->shipping_address->zip.", ".$shopifyorder->shipping_address->country;
                            $destination = $glovocontroller->geocode( $address);

                        @endphp

                        @if($destination)
                        <p class="hide-when-printing">
                            <a target="_blank" data-bind-event-click="Shopify.OrderSidebarFunnel.clickCustomerAddressViewMap()" data-allow-default="1" href="https://maps.google.com/?q={{ $destination->latitude }},{{ $destination->longitude }}&amp;t=h&amp;z=17"> @lang('glovo.orderdetail.viewmap.title') </a>
                        </p>
                        @endif
                    </div>


                </div>


            </div>

        </div>

        <div class="uk-width-small-1-1 uk-width-medium-3-10 uk-grid-margin">

            <div class="md-card">
                <div class="md-card-content">

                    <div class="md-card-section">
                        <h3 class="ui-subheading no-padding no-margin">@lang('glovo.orderdetail.panel.title')</h3>
                        <div class="divider margin-bottom-5 margin-top-5"></div>


                        @if(is_null($glovoorder))
                            {{--No se ha realizado la orden a glovo--}}
                            @include('shopify.orders.status.pending',['emstore'=> $emstore, 'shopifyorder' => $shopifyorder, 'glovoorder'=> $glovoorder, 'shopifyordermetas'=>$shopifyordermetas])
                        @else

                            @if($glovoorder->getStatus() == 'COMPLETED') {{--La orden se realizo correctamente --}}
                                @include('shopify.orders.status.completed',['glovoorder'=> $glovoorder, 'glovocurier'=>$glovocurier])
                            @endif

                            @if($glovoorder->getStatus() == 'FAILED') {{--La orden se realizo pero con errores--}}
                                @include('shopify.orders.status.failed',['glovoorder'=> $glovoorder, 'routeresend'=> $routeresend])
                            @endif

                        @endif



                    </div>



                </div>
            </div>


        </div>

    </div>

@endsection
