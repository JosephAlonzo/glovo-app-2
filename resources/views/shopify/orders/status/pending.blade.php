
<div class="alert error">
    @lang('glovo.orderdetail.nocreate.label')
</div>

@php
    use Carbon\Carbon;
    $notes          = collect($shopifyorder->note_attributes)->keyBy('name');

@endphp

@if ($notes->has('glovo_when_receive'))
    <div class="divider margin-bottom-5 margin-top-5"></div>
    <p class="text-left" data-protected-personal-information>

        <br><br>
        @lang('glovo.orderdetail.delivery.posibility')
    </p>
@endif



<div class="row">
    {!! Form::model(null, ['url'=> $route .'?'.request()->getQueryString() , 'method'=>'POST', 'files' => true, 'role' => 'form', 'id'=>'form-glovo']) !!}
    {{  Form::hidden('order_id',  $shopifyorder->id) }}

    <div class="text-center" >
        <button type="submit" id="btn-send" class="tip" data-hover="@lang('glovo.orderdetail.nocreate.buttom')">@lang('glovo.orderdetail.nocreate.buttom')</button>
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

