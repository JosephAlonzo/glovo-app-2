@php
use Carbon\Carbon;
@endphp

<table class="bordered">
    <tr>
        <td class="text-right">@lang('glovo.orderdetail.glovostatus.state') :</td>
        <td>
            @if($glovoorder->ORGL_STATE == 'ACTIVE' || $glovoorder->ORGL_STATE == 'DELIVERED')
                <label class="success"> {{ $glovoorder->ORGL_STATE }}</label>
            @elseif($glovoorder->ORGL_STATE == 'CANCELED' )
                <label class="error"> {{ $glovoorder->ORGL_STATE }}</label>
            @else
                <label> {{ $glovoorder->ORGL_STATE }}</label>
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-right" width="30%">@lang('glovo.orderdetail.glovostatus.orderid') :</td>
        <td> {{ $glovoorder->getGlovoOrderId() }}</td>
    </tr>
    <tr>
        <td class="text-right" width="30%">@lang('glovo.orderdetail.glovostatus.schedule') :</td>
        <td>
            @if(is_null($glovoorder->ORGL_SCHEDULE_TIME))
                {{ "IMMEDIATELY" }}
            @else
                {{ Carbon::createFromTimestampMs($glovoorder->ORGL_SCHEDULE_TIME, $glovoorder->ORGL_DATETIMEZONE)->isoFormat('LLL') }}
            @endif
            </td>
    </tr>
    <tr>
        <td class="text-right">@lang('glovo.orderdetail.glovostatus.description') :</td>
        <td> {{ $glovoorder->ORGL_DESCRIPTION }} </td>
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
        <td class="text-right">@lang('glovo.orderdetail.glovostatus.created_at') :</td>
        <td> {{ \Carbon\Carbon::parse($glovoorder->created_at)->setTimezone($glovoorder->ORGL_DATETIMEZONE)->isoFormat('LLL') }} </td>
    </tr>
    <tr>
        <td class="text-right" colspan="2"> </td>
    </tr>
    <tr>
        <td class="text-center" colspan="2"> @lang('glovo.orderdetail.glovostatus.checklink') <a href="https://business.glovoapp.com/">https://business.glovoapp.com/</a></td>
    </tr>
</table>



