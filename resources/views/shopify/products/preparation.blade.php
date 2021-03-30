@extends('shopify-app::layouts.default')
@section('styles')
    <link rel="stylesheet"  href="{{ env('SHOPIFY_APP_URL')."/". ('css/shopify.css') }}">
@endsection


@section('content')
    <section class="spy-width-medium-9-10">
        <div class="card">

            {!! Form::model(null, ['url'=> $route, 'method'=>'POST', 'files' => true, 'role' => 'form', 'id'=>'form-preparation-time']) !!}
            {{  Form::hidden('product',  $shopiproduct->id) }}

            <div class="row paddin-top-md">
                    <div>
                        <div style="width: 100px; float: left">
                            <a class="image-ratio image-ratio--square image-ratio--square--100 image-ratio--interactive" href="#">
                                <img title="{{ $shopiproduct->title }}" class="image-ratio__content" src="{{ $shopiproduct->images[0]->src }}">
                            </a></div>
                        <div class="text-left"  style="padding-left:20px; width: 150px; float: left">
                            <div> <a href="#"><b>{{ $shopiproduct->title }}</b></a><br></div>
                            <div>{!!  $shopiproduct->body_html !!}</div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row paddin-top-md">
                    <div class="tip full-width" data-hover="@lang('glovo.preparationform.enable.desc')">
                        <label>{{ Form::checkbox('available_for_glovo', 1, ( ($emavailable) ? $emavailable->PROD_METADATA_VALUE : null), ['id'=>'available_for_glovo']) }}  @lang('glovo.preparationform.enable.label') </label>
                    </div>
                    <em class="small help ">@lang('glovo.preparationform.enable.desc')</em>
                </div>

                <div class="row paddin-top-md">
                    <label>@lang('glovo.preparationform.preparation.label') : </label>
                    <div class="tip full-width" data-hover="@lang('glovo.preparationform.preparation.label')">
                        {!! Form::select('preparationtime', $preparationtimes , ( ($emprodmeta) ? $emprodmeta->PROD_METADATA_VALUE : null) , ['id'=>'preparationtime', 'required'=>'required', 'class' => 'form-control']) !!}
                    </div>

                    @if ($emprodmeta)
                        <div class="text-center"> <a href="javascript:void(0);" id="delete-metadata" data-product="{{$emprodmeta->PROD_PRODUCT}}" data-metafield="{{$emprodmeta->PROD_METADATA_ID}}" >@lang('glovo.preparationform.preparation.labeldelete') </a> </div>
                    @endif

                    <em class="small  help ">@lang('glovo.preparationform.preparation.desc')</em>
                </div>

                <br>
                <div class="row">
                    <div class="text-center" >
                        <button type="button" id="btn-send" class="tip" data-hover="Save">Save</button>
                    </div>
                </div>

            {!! Form::close() !!}


        </div>
    </section>
@endsection




@section('scripts')
    @parent


    {{ Html::script( env('SHOPIFY_APP_URL') .'/assets/vendors/jquery-validate/jquery.validate.min.js') }}
    {{ Html::script( env('SHOPIFY_APP_URL') .'/assets/vendors/forms/jquery.form.js') }}

    <script type="text/javascript">

        var jForm = new function() {
            var $this = this;
            // Variables
            $this.preparationtime       = "#preparationtime";
            $this.form                  = $("#form-preparation-time");
            $this.btn_ok                = $('#btn-send');
            $this.$j

            this.init = function () {


                $this.formSubmitInitialice();
                $this.btnInitialice();
                $this.deleteInitialice();

            }



            this.btnInitialice  = function () {

                $('#btn-send').click(function () {
                    $this.submit();
                });
            }


            this.deleteInitialice = function () {

                $('#delete-metadata').click(function (e) {
                    $this.deleteMetadata($(this).data('product'), $(this).data('metafield'))
                });
            }



            /**
             * Initialice the form event
             */
            this.formSubmitInitialice    = function () {

                var $registerForm = $($this.form).validate({
                    rules 		: {},     // Rules for form validation
                    messages 	: {},  // Messages for form validation

                    // Do not change code below
                    errorPlacement : function(error, element) {
                        error.insertAfter(element);
                    },

                    // Ajax form submition
                    submitHandler : function(form) {



                        if (!$(form).valid()) {
                            return false;
                        }

                        $(form).ajaxSubmit({
                            type : 'POST', // 'get' or 'post', override for form's 'method' attribute
                            url  : $(form).attr('action'),
                            data : { MODULE : 'CONTRATO' },
                            beforeSubmit: function () {
                                $this.btn_ok.prop('disabled', true);
                            },
                            success: function (response, statusText, xhr, form) {
                                ShopifyApp.Modal.close('aftersave', response);
                            },
                            complete : function(){
                                $this.btn_ok.removeAttr('disabled');
                            }
                        });
                    },

                });

            }


            /**
             *
             **/
            this.deleteMetadata = function (product, metaid) {
                $.ajax({
                    type: 'post',
                    url: "{{ env('SHOPIFY_APP_URL'). "/products/preparation/del" }}",
                    data: {'product':product,'metaid' : metaid, '_token': $("input[name='_token']").val()},
                    dataType : 'json',
                    beforeSend: function( xhr ) {
                        $('#delete-metadata').addClass('disabled')
                    }
                }).done(function( response ) {

                    ShopifyApp.Modal.close('afterdelete', response);

                });
            }

            /**
             *  Send de form
             */
            this.submit     = function () {
                $this.form.submit();
            }
        }

        jForm.init();

    </script>

@endsection



