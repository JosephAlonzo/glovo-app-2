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
            <h1>Glovo delivery for Shopify</h1>
            <h2><a target="_blank" href="">by Vexsoluciones</a></h2>
        </article>
    </header>



    @if ($message = Session::get('success') )
        <section>
            <article>
                <div style="width: 100% !important;" class="alert success">
                    <dl>
                        <dt> Success!</dt>
                        <dd>{{ $message }}</dd>
                    </dl>
                </div>
            </article>
            @push('customscripts')
               <script>
                   ShopifyApp.flashNotice("{{ $message }}");
                   /*var actions = window['app-bridge']['actions'];
                   var Flash = actions.Flash;

                   var flashOptions = {
                       message:"{{ $message }}",
                       duration: 5000,
                       isDismissible: true,
                   };
                   var flash = Flash.create(app, flashOptions);
                   flash.dispatch(Flash.Action.SHOW);*/
               </script>
            @endpush
        </section>
    @endif

    @if ($message = Session::get('myerror') )
        <section>
            <article>
                <div style="width: 100% !important;" class="alert warning">
                    <dl>
                        <dt> Warning!</dt>
                        <dd>{{ $message}}</dd>
                    </dl>
                </div>
            </article>
            @push('customscripts')
                <script>
                    ShopifyApp.flashError("{!! str_replace(array("\n","\t"), " ", $message) !!}");
                    /*var actions = window['app-bridge']['actions'];
                    var Flash = actions.Flash;

                    var flashOptions = {
                        message:"{!! str_replace(array("\n","\t"), " ", $message) !!}",
                        duration: 5000,
                        isDismissible: true,
                        isError : true
                    };
                    var flash = Flash.create(app, flashOptions);
                    flash.dispatch(Flash.Action.SHOW);*/
                </script>
            @endpush
        </section>
    @endif


    @if ($settings and  $settings->getValidated() == false  and $settings->getGlovoApi() and $settings->getGlovoSecret())
        <section>
            <article>
                <div style="width: 100% !important;" class="alert error">
                    <dl>
                        <dd>@lang('glovo.validated.title')</dd>
                    </dl>
                </div>
            </article>
        </section>
    @endif








    {!! Form::model($settings, ['url'=> $route, 'method'=>'POST', 'files' => true, 'role' => 'form', 'id'=>'form-settings', 'class'=>'smart-form ']) !!}
    {{  Form::hidden('storeid',  $store->id) }}


    <section >
        <aside>
            <h2>@lang('glovo.general.section')</h2>
            <p>@lang('glovo.general.subsection')</p>
        </aside>
        <article>
            <div class="card">

                @csrf

                <h5>@lang('glovo.general.basic')</h5>
                <div class="row paddin-top-md">
                    <div class="tip full-width" data-hover="@lang('glovo.enable.desc')">
                        <label>@lang('glovo.enable.label'): {{ Form::checkbox('SETT_ENABLE', 1, null, ['id'=>'SETT_ENABLE', 'checked'=>'checked']) }} </label>
                    </div>
                    <em class="small help ">@lang('glovo.enable.desc')</em>
                </div>


                <div class="row paddin-top-md">
                    <label>@lang('glovo.language.label') : </label>
                    <div class="tip full-width" data-hover="@lang('glovo.language.label')">
                        {!! Form::select('SETT_LANGUAGE', $languages , null, ['class' => 'select-language form-control']) !!}
                    </div>
                    <em class="small  help ">@lang('glovo.language.desc')</em>
                </div>


                <div class="row paddin-top-md">
                    <label>@lang('glovo.server.label') : </label>
                    <div class="tip  full-width" data-hover="@lang('glovo.server.desc')">
                        <div class="row">
                            <div class="col-3">
                                {{ Form::radio('SETT_SERVER', 'Production', null, [ 'required'=>'required', 'checked'=>'checked']) }}
                                <label class="display-inline">Production</label>

                            </div>

                            <div class="col-3">
                                {{ Form::radio('SETT_SERVER', 'Test', null, ['required'=>'required']) }}
                                <label class="display-inline">Testing</label>
                            </div>
                        </div>
                    </div>
                    <em class="small  help ">@lang('glovo.server.desc')</em>
                </div>


                <div class="row paddin-top-md">
                    <label> @lang('glovo.googleapi.label') : </label>
                    <div class="tip full-width" data-hover="{{ addslashes(__('glovo.googleapi.tip')) }} ">
                        {{  Form::text('SETT_GOOGLE_API', null, ['required'=>'required', 'maxlength'=>100]) }}
                    </div>
                    <em class="small  help ">@lang('glovo.googleapi.desc').</em> &nbsp; &nbsp;<em class="small help"> <a href="https://www.youtube.com/watch?v=U6TCtMZpWak" target="_blank"> Show me how</a> </em>
                </div>

                <div class="row paddin-top-md">
                    <label>@lang('glovo.glovoapi.label') : </label>
                    <div class="inputcontainer">
                        <div class="tip full-width" data-hover="@lang('glovo.glovoapi.tip')">
                            {{  Form::text('SETT_GLOVO_API', null, ['required'=>'required', 'maxlength'=>30]) }}
                        </div>
                        <div class="icon-container" style="display: none">
                            <i class="loader"></i>
                        </div>
                    </div>

                    <em class="small  help ">@lang('glovo.glovoapi.desc')</em> &nbsp; &nbsp;<em class="small help"> <a href="https://www.youtube.com/watch?v=aVRlHUB7kO0" target="_blank"> Show me how</a> </em>
                </div>

                <div class="row paddin-top-md">
                    <label>@lang('glovo.glovosecret.label') : </label>
                    <div class="inputcontainer">
                        <div class="tip full-width" data-hover="@lang('glovo.glovosecret.desc')">
                            {{  Form::text('SETT_GLOVO_SECRET', null, ['required'=>'required', 'maxlength'=>50]) }}
                        </div>
                        <div class="icon-container" style="display: none">
                            <i class="loader"></i>
                        </div>
                    </div>
                    <em class="small  help ">@lang('glovo.glovosecret.desc')</em>
                </div>

                <div class="row paddin-top-md">
                    <div class="row">
                        <div class="text-right" >
                            <button type="button" class="" id="btn-test-conexion" disabled>@lang('glovo.buttons.test')</button>
                        </div>
                    </div>
                </div>


                <div class="row paddin-top-md">
                    <label>@lang('glovo.method.label')</label>
                    <div class="tip full-width" data-hover="@lang('glovo.method.desc')">
                        {{  Form::text('SETT_METHOD_TITLE', null, ['required'=>'required']) }}
                    </div>
                    <em class="small  help ">@lang('glovo.method.desc')</em>
                </div>



                <div class="row paddin-top-md">
                    <label>@lang('glovo.cost.label')</label>
                    <div class="tip  full-width" data-hover="@lang('glovo.cost.label')">
                        <div class="row">
                            <label> {{ Form::radio('SETT_COST_TYPE', 'Free', null, ['required'=>'required']) }} @lang('glovo.cost.types.free') </label>
                            <label> {{ Form::radio('SETT_COST_TYPE', 'Calculate', null, ['required'=>'required']) }} @lang('glovo.cost.types.calculate') </label>
                            <label> {{ Form::radio('SETT_COST_TYPE', 'Fixed', null, ['required'=>'required']) }} @lang('glovo.cost.types.fixed') </label>

                            <div class="custom-cost" style="height: 32px; {{    ($settings ? ( $settings->SETT_COST_DEFAULT ==  'Fixed' ? "display: block" : "display: none" ) : "display: none") }}">
                                <div class="col-3 margin-left-25" >
                                    {{  Form::text('SETT_COST_DEFAULT', null, ['id'=>'SETT_COST_DEFAULT', 'required'=>'required']) }}
                                </div>
                                <span style="padding: 8px;display: flex;"> {{ $store->currency }}</span>
                            </div>

                        </div>
                    </div>
                    <em class="small  help ">@lang('glovo.cost.desc')</em>
                </div>

                <div class="row paddin-top-md">
                    <label>@lang('glovo.allowscheduled.label')</label>
                    <div class="tip  full-width" data-hover="@lang('glovo.allowscheduled.label')">
                        <div class="row">
                            <label>{{ Form::checkbox('SETT_ALLOWSCHEDULED', 1, null, ['data-checkbox-allow-scheduled'=>'for']) }} @lang('glovo.allowscheduled.label')</label>
                        </div>
                    </div>
                    <em class="small help">@lang('glovo.allowscheduled.desc')</em><br>
                </div>


                <div class="row paddin-top-md">
                    <label>@lang('glovo.createorderstatus.label') : </label>
                    <div class="tip full-width" data-hover="@lang('glovo.createorderstatus.label')">
                        {!! Form::select('SETT_CREATE_STATUS', $orderstatus , ( $settings ? $settings->getCreateStatus(): 'paid' ), ['class' => 'select-hollyday form-control']) !!}
                    </div>
                    <em class="small  help ">@lang('glovo.createorderstatus.desc')</em>
                </div>
            </div>
        </article>
    </section>


    <section>
        <aside>
            <h2>@lang('glovo.locations.section')</h2>
            <p>@lang('glovo.locations.subsection')</p>
        </aside>

        <article>
            <div class="card full-width clearfix">

                @foreach($locations as $location)

                    <div class="row" id="store-location-{{ $location->getId() }}" data-location-section="{{ $location->getId() }}">

                        {{  Form::hidden('locations['.$location->getId().'][id]', $location->getId()) }}

                        {{--<div class="row paddin-top-md">
                            <div class="tip full-width" data-hover="@lang('glovo.address.enable.desc')">
                                <label>@lang('glovo.address.enable.label'): {{ Form::checkbox('locations['.$location->getId().'][enable]', 1, /*$location->getEnable()*/ true , ['data-check-enable'=>'',  'class'=>'disabled', 'readonly'=>'readonly']) }} </label>
                            </div>
                            <em class="small help ">@lang('glovo.address.enable.desc')  {{ $location->getName()  }} {{ $location->getEnable() }}</em>
                        </div>--}}

                        <div class="row columns paddin-top-sm">
                            <div class="columns two"> <label >@lang('glovo.address.storename') : </label></div>
                            <div class="columns eight" data-hover="">
                                {{  Form::text('locations['.$location->getId().'][name]', $location->getName() , ['id'=>'name' , 'class'=>'disabled', 'readonly'=>'readonly', 'required'=>'required']) }}
                            </div>
                        </div>

                        {{--<div class="row columns paddin-top-sm">
                            <div class="columns two"> <label >@lang('glovo.address.lat') : </label></div>
                            <div class="columns eight" data-hover="">
                                {{  Form::text('locations['.$location->getId().'][lat]', $location->getLat() , $location->LAT_ATTRIBUTES) }}
                            </div>
                        </div>--}}

                        {{--<div class="row columns paddin-top-sm">
                            <div class="columns two"> <label >@lang('glovo.address.lng') : </label></div>
                            <div class="columns eight" data-hover="">
                                {{  Form::text('locations['.$location->getId().'][lng]', $location->getLng() , $location->LNG_ATTRIBUTES) }}
                            </div>
                        </div>--}}


                        <div class="row columns paddin-top-sm">
                            <div class="columns two"> <label >@lang('glovo.address.city') : </label></div>
                            <div class="columns eight" data-hover="">
                                {{  Form::text('locations['.$location->getId().'][city]', $location->getCity() , ['id'=>'city', 'class'=>'disabled', 'readonly'=>'readonly']) }}
                            </div>
                        </div>

                        <div class="row columns paddin-top-sm">
                            <div class="columns two"> <label >@lang('glovo.address.address1') : </label></div>
                            <div class="columns eight" data-hover="">
                                {{  Form::text('locations['.$location->getId().'][address1]', $location->getAddress1() , ['id'=>'address1', 'class'=>'disabled', 'readonly'=>'readonly']) }}
                            </div>
                        </div>

                        <div class="row columns paddin-top-sm">
                            <div class="columns two"> <label >@lang('glovo.address.address2') : </label></div>
                            <div class="columns eight" data-hover="">
                                {{  Form::text('locations['.$location->getId().'][address2]', $location->getAddress2() , ['id'=>'address2', 'class'=>'disabled', 'readonly'=>'readonly']) }}
                            </div>
                        </div>


                        <div class="row columns paddin-top-sm">
                            <div class="columns two"> <label data-hover="@lang('glovo.address.country')" >@lang('glovo.address.country') : </label></div>
                            <div class="columns three" data-hover="@lang('glovo.address.country')">
                                <div class="display-inline">
                                    {{  Form::text('locations['.$location->getId().'][country]', $location->getCountryName() , ['id'=>'country', 'class'=>'disabled', 'required'=>'required']) }}
                                </div>
                            </div>
                            <div class="columns three" data-hover="@lang('glovo.address.province')">
                                <div class="display-inline">
                                    {{  Form::text('locations['.$location->getId().'][province]', $location->getProvince() , ['id'=>'province', 'class'=>'disabled']) }}
                                </div>
                            </div>
                            <div class="columns one" data-hover="@lang('glovo.address.postcode')">
                                <div class="display-inline">
                                    {{  Form::text('locations['.$location->getId().'][postcode]', $location->getPostCode() , ['id'=>'zip', 'class'=>'disabled']) }}
                                </div>
                            </div>
                        </div>



                        <div class="row columns paddin-top-sm">
                            <div class="columns two"> <label >@lang('glovo.address.phone') : </label></div>
                            <div class="columns eight" data-hover="">
                                {{  Form::text('locations['.$location->getId().'][phone]', $location->getPhone() , ['id'=>'phone', 'class'=>'disabled', 'readonly'=>'readonly']) }}
                            </div>
                        </div>

                    </div>

                    <hr>

                @endforeach
                <div class="paddin-top-md text-center"> @lang('glovo.locations.coverage') <br><a onclick="javascript: ShopifyApp.remoteRedirect('https://{{  ShopifyApp::shop()->shopify_domain . "/admin/settings/locations" }}')" href="javascript:void(0);">@lang('glovo.locations.primary')</a></div>
            </div>
        </article>


    </section>



    <section>
        <aside>
            <h2>@lang("glovo.workinghours.section") </h2>
            <p>@lang("glovo.workinghours.subsection")</p>
        </aside>
        <article>
            <div class="card">

                @foreach($workinghours as $workingdays)

                    <div class="row columns paddin-top-sm workin-day">
                        <div class="columns three display-inline"> {{ Form::checkbox("days[{$workingdays['day']}][enabled]", 1, (($workingdays['enabled'] == true) ? 1 : 0 ) , ['data-check-day'=>'']) }} <label class="display-inline">@lang("glovo.workinghours.days.{$workingdays['day']}")  </label></div>
                        <div class="columns one" data-hover="">
                            <div class="display-inline">{{  Form::text("days[{$workingdays['day']}][open]", $workingdays['hours'][0]['open'] , ['class'=>'input-hour', 'data-date'=>$workingdays['default'][0]['open'],  'placeholder'=>'HH:MM']) }}</div>
                        </div>
                        <div class="columns one text-center"> - </div>
                        <div class="columns one" data-hover="">
                            <div class="display-inline">{!!  Form::text("days[{$workingdays['day']}][close]", $workingdays['hours'][0]['close'] , ['class'=>'input-hour', 'data-date'=>$workingdays['default'][0]['close'], 'placeholder'=>'HH:MM']) !!}  </div>
                        </div>
                    </div>

                @endforeach

            </div>
        </article>
    </section>


    <section>
        <aside>
            <h2>@lang("glovo.holidays.section")</h2>
            <p>@lang("glovo.holidays.subsection")</p>
        </aside>
        <article>
            <div class="card hollyday-section">

                <script id="clone_section_hollydays" type="text/x-handlebars-template">

                    <div class="row columns paddin-top-sm" style="display:none">
                        <div class="columns one" data-hover="">
                            <div class="display-inline">{!! Form::select('hollydays[day][]',  $days , null , ['class' => 'select-hollyday form-control']) !!}</div>
                        </div>
                        <div class="columns three" data-hover="">
                            <div class="display-inline">{!! Form::select('hollydays[month][]', $months , null , ['class' => 'select-hollyday form-control']) !!}</div>
                        </div>
                        <div class="columns one" data-hover="">
                            <div class="display-inline"> <a href="javascript:void(0)" id="delete_hollyday"> @lang('glovo.holidays.deletebuttom') </a></div>
                        </div>
                    </div>

                </script>

                <div class="row">
                    <div class="tip full-width" data-hover="@lang('glovo.holidays.sublabel')">
                        <label>@lang('glovo.holidays.label') </label>
                    </div>
                    <em class="small help ">@lang('glovo.holidays.sublabel') <a class="add_holliday" href="javascript:void(0);">@lang('glovo.holidays.addbuttom') </a></em>
                </div>

                @if( $settings->hollydays()->count() )

                    @foreach($settings->hollydays as $hollyday)
                        <div class="row columns paddin-top-sm">
                            <div class="columns one" data-hover="">
                                <div class="display-inline">{!! Form::select('hollydays[day][]',  $days , $hollyday->HODAY_DAY , ['class' => 'select-hollyday form-control']) !!}</div>
                            </div>
                            <div class="columns three" data-hover="">
                                <div class="display-inline">{!! Form::select('hollydays[month][]', $months , $hollyday->HODAY_MONTH , ['class' => 'select-hollyday form-control']) !!}</div>
                            </div>
                            <div class="columns one" data-hover="">
                                <div class="display-inline"> <a href="javascript:void(0)" class="delete_hollyday"> @lang('glovo.holidays.deletebuttom') </a></div>
                            </div>
                        </div>
                    @endforeach

                @endif
            </div>
        </article>
    </section>





    <section>
        <article>
            <div class="card">
                <div class="row">
                    <div class="text-center" >
                        <button type="submit" class="tip" data-hover="@lang('glovo.buttons.save')">@lang('glovo.buttons.save')</button>
                    </div>
                </div>
            </div>
        </article>
    </section>




{!! Form::close() !!}










@endsection


