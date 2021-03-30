<!DOCTYPE html>
<html lang="es">
<head>
    <title>@lang('glovo.mailsend.thanks')</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport"  content="width=device-width">

    <style>
        body {
            margin: 0;
        }
        h1 a:hover {
            font-size: 30px;
            color: #333;
        }
        h1 a:active {
            font-size: 30px;
            color: #333;
        }
        h1 a:visited {
            font-size: 30px;
            color: #333;
        }
        a:hover {
            text-decoration: none;
        }
        a:active {
            text-decoration: none;
        }
        a:visited {
            text-decoration: none;
        }
        .button__text:hover {
            color: #fff;
            text-decoration: none;
        }
        .button__text:active {
            color: #fff;
            text-decoration: none;
        }
        .button__text:visited {
            color: #fff;
            text-decoration: none;
        }
        a:hover {
            color: #1990C6;
        }
        a:active {
            color: #1990C6;
        }
        a:visited {
            color: #1990C6;
        }

        @media (max-width: 600px) {
            .container {
                width: 94% !important;
            }
            .main-action-cell {
                float: none !important;
                margin-right: 0 !important;
            }
            .secondary-action-cell {
                text-align: center;
                width: 100%;
            }
            .header {
                margin-top: 20px !important;
                margin-bottom: 2px !important;
            }
            .shop-name__cell {
                display: block;
            }
            .order-number__cell {
                display: block;
                text-align: left !important;
                margin-top: 20px;
            }
            .button {
                width: 100%;
            }
            .or {
                margin-right: 0 !important;
            }
            .apple-wallet-button {
                text-align: center;
            }
            .customer-info__item {
                display: block;
                width: 100% !important;
            }
            .spacer {
                display: none;
            }
            .subtotal-spacer {
                display: none;
            }
        }
    </style>
</head>

<body style="margin: 0;">
<table class="body" style="height: 100% !important; width: 100% !important; border-spacing: 0; border-collapse: collapse;">
    <tr>
        <td height="900" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
            <table class="header row" style="width: 100%; border-spacing: 0; border-collapse: collapse; margin: 40px 0 20px;">
                <tr>
                    <td class="header__cell" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
                        <center>
                            <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                                <tr>
                                    <td style="font-family: -apple-system, BlinkMacSystemFont,'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
                                        <table class="row" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
                                            <tr>
                                                <td class="shop-name__cell" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu','Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
                                                    <h1 class="shop-name__text" style="font-weight:normal; font-size: 30px; color: #333; margin: 0;">
                                                        <a href="https://store-vexsolutions.myshopify.com" style="font-size: 30px; color: #333; text-decoration: none;">
                                                            {{ $shopifyshop->name }}
                                                        </a>
                                                    </h1>
                                                </td>
                                                <td class="order-number__cell" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; text-transform: uppercase; font-size: 14px; color: #999;" align="right">
                                                    <span class="order-number__text" style="font-size: 16px;"> @lang('glovo.mailsend.ordertitle') {{ $shopifyorder->name }} </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>

            <table class="row content" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
                <tr>
                    <td class="content__cell" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; padding-bottom: 40px;">
                        <center>
                            <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                                <tr>
                                    <td style="font-family: -apple-system, BlinkMacSystemFont,'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
                                        <h2 style="font-weight: normal; font-size: 24px; margin: 0 0 10px; color: #C60C10 ">@lang('glovo.mailfailed.title')!</h2>
                                        <p style="color: #777; line-height: 150%; font-size: 16px; margin: 0;">
                                            @lang('glovo.mailsend.hello') <b>{{ $shopifyshop->shop_owner }}</b>, @lang('glovo.mailfailed.description'). <br><br>

                                            @if($emglovoorder)
                                                <span style="color: #C60C10">{!! $emglovoorder->getErrorMessage() !!}</span>
                                            @endif
                                        </p>
                                        <table class="row actions" style="width: 100%; border-spacing: 0; border-collapse: collapse; margin-top: 20px;">
                                            <tr>
                                                <td class="actions__cell" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
                                                    <table class="button main-action-cell" style="border-spacing: 0; border-collapse: collapse; float: left; margin-right: 15px;">
                                                        <tr>
                                                            <td class="button__cell" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; border-radius: 4px; color: #ffffff" align="center" bgcolor="#C60C10">
                                                                <a href="https://{{$shopifyshop->domain}}/admin/apps" class="button__text" style="font-size: 16px; text-decoration: none; display: block; color: #fff; padding: 20px 25px;">@lang('glovo.mailfailed.action')</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table width="311" class="link secondary-action-cell" style="border-spacing: 0; border-collapse: collapse; margin-top: 19px;">
                                                        <tr>
                                                            <td class="link__cell" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
                                                                <a href="https://{{$shopifyshop->domain}}" class="link__text" style="font-size: 16px; text-decoration: none; color: #1990C6;">
                                                                    <span class="or" style="font-size: 16px; color: #999; display: inline-block; margin-right: 10px;">o</span> @lang('glovo.mailsend.visit_store')
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>


                                                </td>
                                            </tr>

                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>

            <table class="row section" style="width: 100%; border-spacing: 0; border-collapse: collapse; border-top-width: 1px; border-top-color: #e5e5e5; border-top-style: solid;">
                <tr>
                    <td class="section__cell" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; padding: 40px 0;">
                        <center>
                            <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                                <tr>
                                    <td style='font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;'>
                                        <h3 style="font-weight: normal; font-size: 20px; margin: 0 0 25px;">@lang('glovo.mailsend.customer_info')</h3>
                                    </td>
                                </tr>
                            </table>
                            <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                                <tr>
                                    <td style='font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;'>
                                        @php

                                            $meta   = json_decode($emglovoorder->ORGL_METAS);

                                        @endphp

                                        @if ($meta)
                                            <table class="row" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
                                                <tr>
                                                    <td class="customer-info__item" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; padding-bottom: 40px; width: 50%; vertical-align: top">
                                                        <h4 style="font-weight: 500; font-size: 16px; color: #555; margin: 0 0 5px;">@lang('glovo.mailsend.address_shipping')</h4>
                                                        <p style="color: #777; line-height: 150%; font-size: 16px; margin: 0;">
                                                            {{ $meta->shipping_address->name }}<br>
                                                            {{ $meta->shipping_address->address1 }}<br>
                                                            @if($meta->shipping_address->address2)
                                                                {{  $meta->shipping_address->address2 }}<br>
                                                            @endif
                                                            {{ $meta->shipping_address->city }} {{ $meta->shipping_address->zip }}<br>
                                                            {{ $meta->shipping_address->province }}<br>
                                                            {{ $meta->shipping_address->country_name }}<br>
                                                            {{ $meta->shipping_address->phone }}
                                                        </p>
                                                    </td>
                                                    <td class="customer-info__item" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; padding-bottom: 40px; width: 50%; vertical-align: top">
                                                        <h4 style="font-weight: 500; font-size: 16px; color: #555; margin: 0 0 5px;">@lang('glovo.mailsend.address_delivery')</h4>
                                                        <p style="color: #777; line-height: 150%; font-size: 16px; margin: 0;">
                                                            {{ $meta->delivery_address->first_name }} {{ $meta->delivery_address->last_name }}<br>
                                                            {{ $meta->delivery_address->company }}<br>
                                                            {{ $meta->delivery_address->address1 }}<br>
                                                            @if($meta->delivery_address->address2)
                                                                {{  $meta->delivery_address->address2 }}<br>
                                                            @endif
                                                            {{ $meta->delivery_address->city  }} {{ $meta->delivery_address->zip }}<br>
                                                            {{ $meta->delivery_address->province_code  }}<br>
                                                            {{ $meta->delivery_address->country }}<br>
                                                            {{ $meta->delivery_address->phone}}<br>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endif

                                        <table class="row" style="width: 100%; border-spacing: 0;border-collapse: collapse;">
                                            <tr>
                                                <td class="customer-info__item" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif; padding-bottom: 40px; width: 50%;"><h4 style="font-weight: 500; font-size: 16px; color: #555; margin: 0 0 5px;">@lang('glovo.mailsend.shipping')</h4>
                                                    <p style="color: #777; line-height: 150%; font-size: 16px; margin: 0;"> {{ $shopifyorder->shipping_lines[0]->title }}</p>
                                                </td>
                                                <td class="customer-info__item" style='font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif; padding-bottom: 40px; width: 50%;'><h4 style="font-weight: 500; font-size: 16px; color: #555; margin: 0 0 5px;">@lang('glovo.mailsend.payment')</h4>
                                                    <p class="customer-info__item-content" style="color: #777; line-height: 150%; font-size: 16px; margin: 0;">
                                                        @if ( isset($shopifyorder->payment_details->credit_card_number))
                                                            <span style="font-size: 16px;">@lang('glovo.mailsend.payment_end') {{ $shopifyorder->payment_details->credit_card_number }} <strong style="font-size: 16px; color: #555;">{{ \Utils\Money::Format(number_format($shopifyorder->total_price,2), $shopifyshop->money_format) }} </strong></span>
                                                        @endif
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
            <table class="row footer" style="width: 100%; border-spacing: 0; border-collapse: collapse; border-top-width: 1px; border-top-color: #e5e5e5; border-top-style: solid;">
                <tr>
                    <td class="footer__cell" style='font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif; padding: 35px 0;'>
                        <center>
                            <table class="container" style="width: 560px; text-align: left; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
                                <tr>
                                    <td style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;">
                                        <p class="disclaimer__subtext" style="color: #999; line-height: 150%; font-size: 14px; margin: 0;">
                                            @lang('glovo.mailsend.emailcontact')
                                            <a href="mailto:{{ $shopifyshop->email }}" style="font-size: 14px; text-decoration: none; color: #1990C6;">{{ $shopifyshop->email }}</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
