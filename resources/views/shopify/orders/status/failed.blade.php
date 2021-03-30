@php
    use Carbon\Carbon;
@endphp

<table class="bordered">
    <tr>
        <td class="text-right">@lang('glovo.orderdetail.glovostatus.description') :</td>
        <td> {{ $glovoorder->ORGL_DESCRIPTION }} </td>
    </tr>

    <tr>
        <td class="text-right" width="30%">@lang('glovo.orderdetail.glovostatus.schedule') :</td>
        <td>
            @if(is_null($glovoorder->ORGL_SCHEDULE_TIME))
                {{ "IMMEDIATELY" }}
            @else
                @php

                    try
                    {
                        $t = Carbon::createFromTimestampMs($glovoorder->ORGL_SCHEDULE_TIME, $glovoorder->ORGL_DATETIMEZONE)->isoFormat('LLL');

                    }catch (Exception $e)
                    {
                        $t = Carbon::parse($glovoorder->ORGL_SCHEDULE_TIME, $glovoorder->ORGL_DATETIMEZONE)->isoFormat('LLL');
                    }

                @endphp
                {{ $t }}
            @endif
        </td>
    </tr>


    <tr>
        <td class="text-right">@lang('glovo.orderdetail.glovostatus.state') :</td>
        <td>
            @if($glovoorder->ORGL_STATE == 'ACTIVE' || $glovoorder->ORGL_STATE == 'DELIVERED')
                <label class="success"> {{ $glovoorder->ORGL_STATE }}</label>
            @elseif($glovoorder->ORGL_STATE == 'CANCELED' )
                <label class="error"> {{ $glovoorder->ORGL_STATE }}</label>
            @else
                <label> {{ $glovoorder->ORGL_STATE ? $glovoorder->ORGL_STATE : "--" }}</label>
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-right" width="30%">@lang('glovo.orderdetail.glovostatus.orderid') :</td>
        <td> {{ ($glovoorder->getGlovoOrderId() ? $glovoorder->getGlovoOrderId() : "--") }} </td>
    </tr>


    <tr>
        <td class="text-right">@lang('glovo.orderdetail.glovostatus.courier') :</td>
        <td>{{ ($glovocurier ? $glovocurier['courierName'] : '--') }} </td>
    </tr>
    <tr>
        <td class="text-right">@lang('glovo.orderdetail.glovostatus.phone') :</td>
        <td> {{ ($glovocurier ? $glovocurier['phone'] : '--') }} </td>
    </tr>


    <tr>
        <td class="text-right"></td>
        <td> </td>
    </tr>
    <tr>
        <td class="text-right">@lang('glovo.orderdetail.glovostatus.failedstatus') :</td>
        <td> <label class="error">{{ $glovoorder->ORGL_STATUS }}</label></td>
    </tr>
    <tr>
        <td class="text-right">@lang('glovo.orderdetail.glovostatus.failedmessage') :</td>
        <td> <small><em class="invalid">{{ $glovoorder->ORGL_MESSAGE }}</em></small></td>
    </tr>
</table>


<div class="row margin-top-20">
    {!! Form::model(null, ['url'=> $routeresend, 'method'=>'POST', 'files' => true, 'role' => 'form', 'id'=>'form-glovo']) !!}
    {{  Form::hidden('order_id',  $shopifyorder->id) }}

    <div class="text-center" >
        <button type="submit" id="btn-send" class="tip" data-hover="@lang('glovo.orderdetail.glovostatus.retry')">@lang('glovo.orderdetail.glovostatus.retry')</button>
    </div>

    {!! Form::close() !!}


    @push('customscripts')
        <script>
            $('#form-glovo').submit(function(){
                $(this).find('button[type=submit]').prop('disabled', true).addClass('disabled');
            });
        </script>
    @endpush




</div>
