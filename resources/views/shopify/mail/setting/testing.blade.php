@component('mail::message')
Testing a new configuration rappi
@if ($response['success']==true)
    <span style="color: green"> passed success </span>
@else
    <span style="color: red"> failed </span>
@endif

Recived a new testing configuration for Glovo in the shopdomain

@php
$address = @$response['requestaddress']['address']['origin']['details'];
@endphp

@component('mail::panel')
    {{ $shop->shopify_domain }}<br><br>
    <small>{{ $address }}</small><br>
    <i class="fa fa-map"></i><a href="http://maps.google.com/maps?q={{ $address }}">Mapa de la dirección</a>
    <br>
    {{ $address }}
@endcomponent

@if ($response['success']==true)
@component('mail::button', ['url' => '', 'color'=>'success'])
    {{ $response['message'] }}
@endcomponent

@else
@component('mail::button', ['url' => '', 'color'=>'error'])
    {{ $response['errors']['message'] }}
@endcomponent

@endif


Thanks,<br>
{{ config('app.name') }}
@endcomponent
