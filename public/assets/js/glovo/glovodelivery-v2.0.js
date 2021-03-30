

(function(){

    /* Load Script function we may need to load jQuery from the Google's CDN */
    /* That code is world-reknown. */
    /* One source: http://snipplr.com/view/18756/loadscript/ */

    var loadScript = function(url, callback, type){

        if (type=="css")
        {
            var css = document.createElement("link");
            css.rel = "stylesheet";
            css.type = "text/css";
            css.href = url;
            document.getElementsByTagName("head")[0].appendChild(css);

            return true;
        }

        var script = document.createElement("script");
        script.type = "text/javascript";

        // If the browser is Internet Explorer.
        if (script.readyState){
            script.onreadystatechange = function(){
                if (script.readyState == "loaded" || script.readyState == "complete"){
                    script.onreadystatechange = null;
                    callback();
                }
            };
            // For any other browser.
        } else {
            script.onload = function(){
                callback();
            };
        }

        script.src = url;
        document.getElementsByTagName("head")[0].appendChild(script);

    };

    /* This is my app's JavaScript */
    var GlovoDelivery = function($){


        var $this = this;
        // Variables
        $this.version        = 2.33;
        $this.urlBase        = "https://glovodelivery.vexecommerce.com";
        //$this.urlBase        = "https://90ef3c23.ngrok.io";
        $this.workingDaysUrl = $this.urlBase + "/workingdays.json";
        $this.workingTimesUrl= $this.urlBase + "/workingtime.json";
        $this.settingid      = undefined;
        $this.workingdays    = undefined;
        $this.cbScheduleday  = null;
        $this.cbscheduletime = null;
        $this.ctrlWhen       = null;

        $this.queryProducts  = ["#productSelect-product-template", "#ProductSelect-product-template", "#ProductSelect", "select.product-form__master-select", 'input[name="id"]', 'select[name="id"]'];
        $this.queryCart      = ["form[action='/cart'] table", "form[action='/cart'] table.cart__table", '#CartSpecialInstructions', 'textarea[name="note"]', 'footer.cart__footer','div.cart__footer','form.cart'];
        $this.maxWeight     = 9000; //9.0KG
        $this.map;
        $this.zoom          = 16;
        $this.submit        = null;
        $this.currency      = {};
        $this.workingtext   = '';

        $this.disabled      = null;
        $this.cart          = null;

        this.init   = function ($) {
            var body    = $('body');
            var product = selector($this.queryProducts);

            console.log( body.hasClass('product') );
            if ( (body.hasClass('template-product') || c && product.length){
                this.initProduct(product);
            }else if( body.hasClass('template-cart'))
            {
                setTimeout(this.initCart,1000);
            }

        }


        this.initCart = function () {
            $this = this;

            $.getJSON('/cart.js', function(cart) {
                $this.cart = cart;
                $this.getServiceAvailability();
            });

        }


        /**
         *
         */
        this.calculateWeight    = function () {

            var total_weight_grams = 0;
            $.each($this.cart.items,function (i, product) {
                total_weight_grams+= (product.grams * product.quantity);
            });

            if ( (total_weight_grams-100) > $this.maxWeight)
            {
                $('#glovo-shpping-delivery').remove();
            }else{
                $('#glovo-shpping-delivery').slideDown('slow');
            }
        }


        /**
         *
         */
        this.getWorkinDays  = function(){

            $.ajax({
                type: 'post',
                url: $this.workingDaysUrl,
                data: {'setting':$this.settingid},
                dataType : 'json',
                beforeSend: function( xhr ) {
                    $this.cbScheduleday.empty();
                }
            }).done(function( response ) {

                $this.setWorkinDays(response);

            });

        }


        /**
         *
         */
        this.setWorkinDays = function( workingdays ){

            $this.cbScheduleday.find('option').remove();
            $this.cbScheduleday.prop('required','required');

            $.each(workingdays,function (i, v) {
                var option = $('<option value="' + v.id + '">' + v.text + '</option>');

                if (v.enable==false) option.prop('disabled','disabled');
                if (v.immediately!=undefined)   option.prop('immediately',v.immediately);

                $this.cbScheduleday.append(option);
            });

            $this.cbScheduleday.unbind().bind('change',function () {
                $this.getWorkingTimes($(this).val());
            });


            if( $this.cbScheduleday.find('option:eq(0)').prop('disabled')  == 'disabled' ||
                $this.cbScheduleday.find('option:eq(0)').prop('immediately') == false
            ){
                $this.ctrlWhenDisable();
            }


            $.each($this.cbScheduleday.find('option') , function (i,option) {
                if ( $(this).prop('disabled') === false){
                    $this.cbScheduleday.val( $this.cbScheduleday.find('option').eq(i).val()).trigger('change');
                    return false;
                }
            });


        }


        /**
         *
         * @param day
         */
        this.getWorkingTimes = function (day) {

            if ( $('option:selected', $this.cbScheduleday).attr('disabled') ) {
                return false;
            }


            var product_list = new Array();
            $.each($this.cart.items, function (i,product) {
                product_list.push({product_id:product.product_id, variant_id:product.variant_id })
            });


            $.ajax({
                type: 'post',
                url: $this.workingTimesUrl +"?_d"+(new Date().getTime()),
                data: {'shop': window["Shopify"].shop, 'day':day, 'products':JSON.stringify(product_list)},
                dataType : 'json',
                beforeSend: function( xhr ) {
                    $this.cbscheduletime.empty();
                    $('#glovo-loading').show();
                }
            }).done(function( response ) {
                $('#glovo-loading').hide();
                $.each(response.hours, function (i,hour) {
                    $this.cbscheduletime.append('<option value="' + hour.id + '">' + hour.text + '</option>');
                });

            });
        }



        this.getServiceAvailability = function ( ) {

            if ($this.cart.items.length == 0){
                return false;
            }

            var product_list = new Array();
            $.each($this.cart.items, function (i,product) {
                product_list.push({product_id:product.product_id, variant_id:product.variant_id })
            });

            $.ajax({
                type: 'post',
                url: $this.urlBase + "/serviceavailability.json?_d"+ (new Date().getTime()),
                data: {'shop': window["Shopify"].shop, 'products': JSON.stringify(product_list)},
                dataType : 'json',
                beforeSend: function( xhr ) { }
            }).done(function( response ) {
                $this.proccessServiceAvailability(response);
            });
        }


        this.proccessServiceAvailability = function(response){

            if (response.status.code == 200)
            {
                if($this.renderWrapperCart(response.snippet)){
                    $this.cbScheduleday     = $('#glovo_schedule_day');
                    $this.cbscheduletime    = $('#glovo_schedule_time');
                    $this.ctrlWhen          = $('.jtoggler'); //switch


                    $this.calculateWeight();
                    $this.toogle();
                    $this.setWorkinDays(response.workingdays);

                }else
                {
                    $this.removeWrapper();
                }
                //
            }else
            {
                $this.removeWrapper();
            }
        }



        /**
         * @param html
         * @returns {boolean}
         */
        this.renderWrapperCart  = function(html){


            let notes = selector($this.queryCart);
            $this.debug(notes.selector);
            $this.debug('notes-----', notes);


            if( notes)
            {

                $this.debug("------------Posicionar---------------");

                if (notes.selector=="form[action='/cart'] table" || notes.selector=="form[action='/cart'] table.cart__table") {
                    $(html).insertAfter(notes);

                }else if (notes.selector=='#CartSpecialInstructions' || notes.selector=='textarea[name="note"]') {
                    $this.debug("parent", notes.parent());
                    notes.parent().append(html);

                }else {
                    alert('sss');
                    notes.parent().append(html);

                }

                return true;
            }
            return false;

        }


        this.toogle = function () {
            $(document).on('jt:toggled', function(event, target) {
                $this.enableWrapper(event, target);
            });


            $this.ctrlWhen.jtoggler().trigger('change');
        }

        this.ctrlWhenDisable  = function (){
            return $this.ctrlWhen.prop('checked',false).trigger('change').prop('disabled','disabled');
        }



        this.enableWrapper  = function(event, target){

            if ($(target).prop('checked') ){
                $('.select-schedule').slideUp();
                $('#glovo_when_receive').val("immediately");
            }else{
                $('.select-schedule').slideDown();
                $('#glovo_when_receive').val("scheduled");
            }

        }


        this.removeWrapper = function(){
            $('#glovo-shpping-delivery').remove();
        }


        /**
         *
         * @param product
         */
        this.initProduct = function (product) {

            if ( product ) {
                $this.requestProductAvailability(product);
            }

        }


        /**
         * Get product avalilability for glovo
         * @param product $product
         * @returns {boolean}
         */
        this.requestProductAvailability = function (product ) {

            $.ajax({
                type: 'post',
                url: $this.urlBase + "/productavailability.json",
                data: {'shop': window["Shopify"].shop, 'product':product.val()},
                dataType : 'json',
                beforeSend: function( xhr ) { }
            }).done(function( response ) {
                if (response.status.code == 200)
                {

                    product.closest('form').prepend(response.snippet);
                }

            });
        }


        /**
         *
         * @param data
         * @constructor
         */
        this.ProductAvailability = function( data ){

            var tpl = "";
            tpl+= "<fieldset class=\"shipping-glovo-delivery\">\n" +
                "        <legend><img src=\"https://db7150b2.ngrok.io/assets/images/icons/shipping_glovo.png\" width=\"80\"> Glovo delivery  </legend>\n" +
                "        <div class=\"shipping-glovo-delivery-body\">\n" +
                "            <div class=\"shipping-glovo-delivery-estimated\">\n" +
                "                <div style=\"float: right\">\n" +
                "                    <span class=\"estimated-time\">30 min aprox </span>\n" +
                "                </div>\n" +
                "                <div class=\"shipping-glovo-delivery-icon-estimated\">\n" +
                "                    <img src=\"https://db7150b2.ngrok.io/assets/images/icons/clock.png\" width=\"22\">\n" +
                "                </div>\n" +
                "            </div>\n" +
                "            <div class=\"shipping-glovo-delivery-info\">\n" +
                "               " + data.message
            "            </div>\n" +
            "        </div>\n" +
            "    </fieldset>";
        }




        this.debug = function(message)
        {
            // It is always good to declare things at the top of a function,
            // to quicken the lookup!
            var i = 0, len = arguments.length;

            // Notice that the for statement is missing the initialization.
            // The initialization is already defined,
            // so no need to keep defining for each iteration.
            for( ; i < len; i += 1 ){

                // now you can perform operations on each argument,
                // including checking for the arguments type,
                // and even loop through arguments[i] if it's an array!
                // In this particular case, each argument is logged in the console.
                console.log( arguments[i] );
            }
        }


        var selector = function (selectors){

            for (let t=0; t < selectors.length; t++){
                let el = $(selectors[t]);
                if (el.length) {
                    $el= $(el[0]);
                    $el.selector = selectors[t];
                    return $el;
                }
            }

            return false;
        }

        loadScript($this.urlBase + "/assets/vendors/jquery-toogle/jtoggler.styles.css", function(){}, 'css');
        loadScript($this.urlBase + "/assets/css/glovodelivery-v2.0.min.css", function(){}, 'css');


        init($);






    };


    var jToogle = function($){
        ;( function( $, window, document, undefined ) {

            "use strict";

            var pluginName = "jtoggler",
                defaults = {
                    className: "",
                };

            function Toggler ( element, options ) {
                this.element = element;

                this.settings = $.extend( {}, defaults, options );
                this._defaults = defaults;
                this._name = pluginName;

                this.init();
                this.events();
            }

            $.extend( Toggler.prototype, {
                init: function() {
                    var $element = $(this.element);

                    if ($element.data('jtmulti-state') != null) {
                        this.generateThreeStateHTML();
                    } else {
                        this.generateTwoStateHTML();
                    }
                },
                events: function() {
                    var $element = $(this.element);
                    var instance = this;

                    $element.on('change', this, function (event) {
                        if ($element.data('jtlabel')) {
                            if ($element.data('jtlabel-success')) {
                                if ($element.prop('checked')) {
                                    $element.next().next().text($element.data('jtlabel-success'));
                                } else {
                                    $element.next().next().text($element.data('jtlabel'));
                                }
                            } else {
                                instance.setWarningLabelMessage();
                            }
                        }

                        $(document).trigger('jt:toggled', [event.target]);
                    });

                    if (!$element.prop('disabled')) {
                        var $control = $element.next('.jtoggler-control');
                        $control
                            .find('.jtoggler-radio')
                            .on('click', this, function (event) {
                                $(this)
                                    .parents('.jtoggler-control')
                                    .find('.jtoggler-btn-wrapper')
                                    .removeClass('is-active');

                                $(this)
                                    .parent()
                                    .addClass('is-active');

                                if ($(event.currentTarget).parent().index() === 2) {
                                    $control.addClass('is-fully-active');
                                } else {
                                    $control.removeClass('is-fully-active');
                                }

                                $(document).trigger('jt:toggled:multi', [event.target]);
                            });
                    }
                },
                generateTwoStateHTML: function() {
                    var $element = $(this.element);

                    if (!$element.hasClass('jqtoggler-inited')) {
                        $element.addClass('jqtoggler-inited');
                        var $wrapper = $('<label />', {
                            class: $.trim("jtoggler-wrapper " + this._defaults.className),
                        });
                        var $control = $('<div />', {
                            class: 'jtoggler-control',
                        });
                        var $handle = $('<div />', {
                            class: 'jtoggler-handle',
                        });

                        $control.prepend($handle);
                        $element.wrap($wrapper).after($control);

                        if ($element.data('jtlabel')) {

                            var $label = $('<div />', {
                                class: 'jtoggler-label',
                            });

                            if ($element.prop('checked')) {
                                if ($element.data('jtlabel-success')) {
                                    $label.text($element.data('jtlabel-success'));
                                } else {
                                    this.setWarningLabelMessage();
                                    $label.text($element.data('jtlabel'));
                                }
                            } else {
                                $label.text($element.data('jtlabel'));
                            }

                            $control.after($label);
                        }
                    }
                },
                generateThreeStateHTML: function() {
                    var $element = $(this.element);

                    if (!$element.hasClass('jqtoggler-inited')) {
                        $element.addClass('jqtoggler-inited');
                        var $wrapper = $('<div />', {
                            class: $.trim("jtoggler-wrapper jtoggler-wrapper-multistate " + this._defaults.className),
                        });
                        var $control = $('<div />', {
                            class: 'jtoggler-control',
                        });
                        var $handle = $('<div />', {
                            class: 'jtoggler-handle',
                        });
                        for (var i = 0; i < 3; i++) {
                            var $label = $('<label />', {
                                class: 'jtoggler-btn-wrapper',
                            });
                            var $btn = $('<input />', {
                                type: 'radio',
                                name: 'options',
                                class: 'jtoggler-radio',
                            });

                            $label.append($btn);
                            $control.prepend($label);
                        }
                        $control.append($handle);
                        $element.wrap($wrapper).after($control);
                        $control.find('.jtoggler-btn-wrapper:first').addClass('is-active');
                    }

                },
                setWarningLabelMessage: function() {
                    console.warn('Data attribute "jtlabel-success" is not set');
                },

            } );

            $.fn[ pluginName ] = function( options ) {
                return this.each( function() {
                    if ( !$.data( this, "plugin_" + pluginName ) ) {
                        $.data( this, "plugin_" +
                            pluginName, new Toggler( this, options ) );
                    }
                } );
            };

        } )( $, window, document );
        return $;
    }

    /* If jQuery has not yet been loaded or if it has but it's too old for our needs,
    we will load jQuery from the Google CDN, and when it's fully loaded, we will run
    our app's JavaScript. Set your own limits here, the sample's code below uses 1.7
    as the minimum version we are ready to use, and if the jQuery is older, we load 1.9. */
    if ((typeof jQuery === 'undefined') || (parseFloat(jQuery.fn.jquery) < 1.9)) {
        loadScript('//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js', function(){
            jQuery191 = jQuery.noConflict(true);
            jQuery191 = jToogle(jQuery191);
            GlovoDelivery(jQuery191);

        });
    } else {
        jQuery = jToogle(jQuery);
        GlovoDelivery(jQuery);

    }

})();
