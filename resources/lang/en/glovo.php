<?php

return [
    'storefront' => [
        'template' => [
            'products' => [
                'available' => 'This product is available for delivery by ',
                'choosewhen'=> 'Choose in the cart when you want to receive it'
            ],
            'cart'      => [
                'loading'=> 'Finding available delivery times..',
                'estimated'=> [
                    'hour'  => 'The order will take approximately <b>{estimate_hours}:{estimate_min}</b> hours to prepare',
                    'minute'=>'The order will take approximately <b>{estimate_time}</b> min to prepare.',
                    'immediately'=>'<i>The order is available immediately for delivery</i>'
                ],
                'available'=> 'One or more of the items in your cart are available for Glovo delivery service.',
                'noallowscheduled'=> 'Choose glovo in the next step to receive your order!.',
                'when' => [
                    'label' => 'When would you like to receive your order?',
                    'question'=>'Choose when you want to receive your order.',
                    'assoon'  => 'Send as soon as possible'
                ],
                'day'   => 'Day',
                'hour'  => 'Hour'
            ]
        ],

        'shipping' => [
            'rate'      => '(Check the cart to Choose delivery date / time)'
        ]

    ],

    'welcome'   => [

        'gettinstarted' => "Getting Started Guide",
        'p1'            => "Follow these steps to enable Glovo Delivery",
        'l1'            => [
            't1'        =>  "Modify",
            't2'        =>  "After installing the application, a liquid snippet called",
            't3'        =>  "is installed in your current shop theme. This snippet is responsible for displaying the delivery settings on the cart page.",
            't4'        =>  "To activate the snippet, open the <a target=\"blank\" href=\"https://docs.shopify.com/manual/configuration/store-customization/#template-editor\">Theme Editor</a> in your store admin, then open <code class=\"code_span\">Templates/cart.liquid</code> and add <code class=\"code_span\"> {% include 'snippet-glovo-delivery-cart' %} </code> between the opening <code class=\"code_span\">&lt;form&gt;</code> and the closing <code class=\"code_span\">&lt;/form&gt;</code> tags.",
            't5'        =>  "Exact placement between these tags isn't critical but a good place is immediately above the cart notes or special instructions (<code class=\"code_span\">{% if settings.show_cart_notes %}</code> <strong>or</strong> <code class=\"code_span\">{% if settings.special_instructions %}</code> <strong>or</strong> <code class=\"code_span\">{% if settings.additional_informaiton %}</code>).",
            't6'        =>  "For example:",
            't7'        =>  "Remember to save changes when you are done.",
        ],

        'l2'            => [
            't1'        => 'Go to the settings menu',
            't2'        => [
                'title' => "The first thing to configure is the APIS to connect to the Glovo API and Google Maps, for example provide the Glove API keys . <a target=\"blank\" href=\"https://business.glovoapp.com/login\">Glovo Bussiness</a>",
                "helps"     => [
                    'c1'    => '<b>Enable service: </b> Enable in your store delivery service using the Glovo',
                    'c2'    => '<b>Language: </b> Choose the language of your store. By default (Frontstore language)',
                    'c3'    => '<b>Google maps API: </b> Provide the google maps API, the app requires this api for address geo-location. Get an api <a href="https://developers.google.com/maps/documentation/embed/get-api-key"> Google Developers </a>.',
                    'c4'    => '<b>Glovo Business API: </b> Provides Glovo APIs, the app requires this app to place orders. Get an api <a href="https://business.glovoapp.com"> Glovo Business </a>.',
                    'c5'    => '<b>Title </b> Is the name that will appear in the store to identify the glovo service',
                    'c6'    => '<b>Cost</b> Choose how you want to calculate the cost of the service. 1.- Free, 2.- Calculated by the api of glovo using geolocation. 3.- Fixed price',
                    'c7'    => '<b>Schedule delivery </b> This option allows the user to do the programming to receive their order. Otherwise it will try to send immediately',
                    'c8'    => '<b>When to create the order </b> Choose when you want the glovo order placed, 1.- Authorized payment, 2.- Paid order, 3.- Manual',
                ],
            ],

            't3'            => [
                'title'     => "Set locations, days and working hours in which there is delivery and rest days in which there will be no delivery",
                "helps"     => [
                    'c1'    => '<b> Store location: </b> Configure the store location to be within the Glovo coverage area. See cities with coverage <a href="https://glovoapp.com/en/map"> Coverage map </a>. If you want to change the store location follow the steps below:',
                    'c2'    => '<b> Days of service: </b> Configure the days and hours of service for ordering glovo',
                    'c3'    => '<b> Holidays: </b> Provide holidays, these days the service will not be available </a>.',
                ],
            ],
        ],

        'l3'            => [
            't1'        => "Choose the products that can be sent by glovo",
            't2'        => "Configure the availability of the products to be delivered with the Glovo service",
            't3'        => "Set the estimated time of preparation of the available products. To be able to offer you the delivery schedule when checking the cart"
        ],

        'l4'            => [
            't1'        => "The service is activated in the product template",
            't2'        => "The product information will appear in the store front to be sent through the Glovo service",
        ],

        'l5'            => [
            't1'        => "Choose when you want to receive your order",
            't2'        => "If the delivery schedule setting is activated, the buyer can choose when he will receive his order. Otherwise it will be sent as soon as possible",
        ]
    ],


    'general' => [
        'section'       => 'Glovo settings',
        'subsection'    => 'Set basic settings',
        'basic'         => 'Basic settings',

    ],

    'enable'=> [
        'label'=> 'Enable Glovo Shipping',
        'desc' => 'Do you want to activate Glovo Delivering?'
    ],

    'language'=> [
        'label'=> 'Choose an language',
        'desc' => 'Define the main language of the store'
    ],

    'server'=> [
        'label'=> 'Server',
        'desc' => 'Use the productive or test server'
    ],




    'glovoapi'=> [
        'label'=> 'Glovo API Key',
        'tip'   => 'API Key provided by glovo',
        'desc' => 'Get your api key on glovo site <a target="_blank" href="https://business.glovoapp.com"> https://business.glovoapp.com </a>'
    ],

    'glovosecret'=> [
        'label'=> 'Glovo API Secret',
        'desc' => 'API Secret provided by glovo'
    ],

    'googleapi'=> [
        'label'=> 'Google Maps API Key',
        'tip' => 'API Key provided by google maps',
        'desc' => 'API Key provided by google maps. Get your apikey on site <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key"> https://developers.google.com/maps </a>'
    ],


    'method' => [
        'label'=> 'Title of the method',
        'desc' => 'Title for the glovo shipping delivery method. How the client will see the title when doing the checkout'
    ],

    'cost' => [
        'label'=> 'Cost of delivering',
        'desc' => 'Choose the way to calculate the shipping rate. <br><br> * Note that Shopify has a cache to avoid multiple requests. It may take a few minutes to update the rates. If you change any product in the shopping cart, it will be updated immediately',
        'types' => [
            'free'  => "Free",
            'calculate'=> "Calculated by Glovo API",
            'fixed' => "Based on a fixed price",
        ]
    ],

    'allowscheduled'    => [
        'label'     => 'Allow delivery schedule',
        'desc'      => 'Active: Allows the buyer to choose day/time to schedule the delivery. <br> Deactivate: Orders will be placed as soon as possible'
    ],

    'createorderstatus'    => [
        'label'     => 'Create order when status equals',
        'desc'      => 'Choose the status that the Shopify order must fulfill to start the glovo order'
    ],

    'locations' => [
        'section'       => 'Configure delivery addresses',
        'subsection'    => 'set primary location',
        'coverage'      => 'The store location must be within the glovo service area. See cities with coverage <a href="https://glovoapp.com/en/map"> Coverage map. </a>',
        'primary'       => 'For editing locations go to Settings -> Locations'

    ],


    'address'   => [
        'enable'=> [
            'label'=> 'Enable Glovo Delivery',
            'desc' => 'Do you want to activate Glovo Delivering for store location?'
        ],
        'lat'           => 'Location Lat',
        'lng'           => 'Location Lng',
        'city'          => 'City name',
        'storename'     => 'Store name',
        'address1'      => 'Address 1',
        'address2'      => 'Address 2',
        'postcode'      => 'Postal Code',
        'phone'         => 'Contact phone',
        'country'       => 'Country name',
        'province'      => 'Province',

    ],

    'workinghours'   => [
        'section'       => 'Working hours',
        'subsection'    => 'Set days and work schedules in which the store provides the delivery service',
        'days'          => [
            '0'    => "Sunday" ,
            '1'    => "Monday" ,
            '2'    => "Tuesday" ,
            '3'    => "Wednesday" ,
            '4'    => "Thursday" ,
            '5'    => "Friday" ,
            '6'    => "Saturday"
        ],
        'today'     => 'Today',
        'tomorrow'  => 'Tomorrow'
    ],


    'holidays'   => [
        'section'       => 'Holidays days',
        'subsection'    => 'Set holidays. In these days there will be no delivery service',
        'label'         => 'Set the holidays',
        'sublabel'      => 'Add the days when the glovo service will not be available',
        'addbuttom'     => 'Add holiday',
        'deletebuttom'  => 'Delete'
    ],

    'months'    => [
        '01'    => 'January',
        '02'    => 'February',
        '03'    => 'March',
        '04'    => 'April',
        '05'    => 'May',
        '06'    => 'June',
        '07'    => 'July',
        '08'    => 'August',
        '09'    => 'September',
        '10'    => 'October',
        '11'    => 'November',
        '12'    => 'December',
    ],


    'buttons'       => [
        'save'      => 'Save',
        'delete'    => 'Delete',
        'cancel'    => 'Cancel',
        'test'      => 'Test connection'
    ],


    'settings'      => [
        'save'      => [
            'success'   => 'The configuration was saved',
            'failed'    => 'There was a problem saving the configuration'
        ],
        'messages'      => [
            'required'  => 'This field is required.'
        ]
    ],

    'validated'         => [
        'title'         => 'Could not validate store address. Make sure the credentials of glovo and google are correct. The store must be within the glovo service area (https://glovoapp.com/es/map).  Verify the address of the store is real, the city, street, cp, etc. will be used for geolocation',
    ],

    'validatedplan'         => [
        'noallow'       => 'Your store plan prevents you from calculating calculated rates, in order to use this functionality at checkout you must have an Advanced Plan or an Annual Plan. However, the orders that are created can be sent individually to glovo for delivery to the customer.'
    ],


    'products'          => [

        'header'        => [
            'title'     => 'Products',
            'subtitle'  => 'Enables the delivery of products by the Glovo delivery service'
        ],
        'table'         => [
            'headers'   => [
                'product'   => 'Product',
                'type'      => 'Type',
                'vendor'    => 'Vendor',
                'available' => 'Available for glovo',


            ]
        ],
        'filters'       => [
            'apply'     => 'Filter product',
            'remove'    => 'Clear Filter'
        ]

    ],


    'preparationform' => [
        'modal'         => [
            'title'     => 'Estimated time of preparation'

        ],

        'enable'        => [
            'label'     => 'Enable this product for delivery',
            'desc'      => 'Use this option to enable the delivery of this product by Glovo delivery service',

        ],

        'availability'  => [
            'label'     => 'Immediately',
            'no'        => 'Not available'

        ],


        'preparation'   => [
            'label'     => 'Estimated time of preparation',
            'desc'      => 'Select the estimated time of product preparation. It will be used for the availability of schedules. <br><br> For example, if the store closes at 10:00 PM, and the product takes 30 minutes. The maximum service hour will be 09:30 PM',
            'labeldelete'=> 'Delete Estimated time'
        ],

        'save'          => [
            'success'   => 'Product information was saved',
            'error'     => 'An error occurred while saving product information'
        ],
        'delete'          => [
            'success'   => 'Product information was saved',
            'error'     => 'An error occurred while saving product information'
        ]
    ],


    'orders'        => [

        'header'        => [
            'title'     => 'Orders',
            'subtitle'  => 'Orders that are delivered by glovo'
        ],

        'table'         => [
            'headers'   => [
                'order'     => '#Order',
                'date'      => 'Delivery Date',
                'customer'  => 'Customer',
                'deliveryaddress' => 'Delivery Address',
                'paid'      => 'Payment Status',
                'glovostatus' => 'Glovo Status',


            ]
        ]
    ],



    'orderdetail'   => [

        'panel'     => [
            'title' => 'Glovo Order Status'
        ],

        'pickupaddress' => [
           'title'          => 'Pickup Address'
        ],
        'contact' => [
           'title'          => 'Contact Address'
        ],
        'destination' => [
            'title'         => 'Delivery Address'
        ],
        'viewmap' => [
            'title'         => 'View map'
        ],


        'glovostatus'       => [
            'state'         => 'State',
            'orderid'       => 'Order Id',
            'schedule'      => 'ScheduleTime',
            'description'   => 'Description',
            'courier'       => 'Courier name',
            'phone'         => 'Phone',
            'created_at'    => 'Created At',

            'failedstatus'  => 'Failed status',
            'failedmessage' => 'Failed message',
            'retry'         => 'Try to resend',
            'checklink'     => 'Visit Glovo Bussines'
        ],

        'nocreate'          => [
            'label'         => 'The order has not been created in Glovo yet. Click on the button "Create order" to create it right now',
            'buttom'        => 'Create glovo order'
        ],
        'create'            => [
            'success'       => "Order created successfully",
            'fail'          => "An error occurred while processing the order. With message: "
        ],

        'resend'            => [
            'success'       => "The order was processed correctly.",
            'fail'          => "An error occurred while processing the order. With message: "
        ],

        'delivery'          => [
            'scheduled'     => 'The order was scheduled to be delivered at',
            'immmediately'  => 'The order will be sent',
            'possibility'   => 'The order can be sent using Glovo Delivery. To send it manually, click send'
        ]



    ],

    'mailsend'          => [
        'ordertitle'        => 'Order',
        'hello'             => 'Hello',
        'preparing'         => 'We are preparing your order to pick it up. Here you can see the order tracking',
        'thanks'            => 'Thanks for your purchase',
        'track_title'       => 'Track your order',
        'visit_store'       => 'Visit our store',
        'customer_info'     => 'Customer information',
        'address_shipping'  => 'Shipping Address',
        'address_delivery'  => 'Delivery Address',
        'shipping'          => 'Shipping method',
        'payment'           => 'Payment method',
        'payment_end'       => 'Ends in',
        'emailcontact'      => 'If you have any questions, reply to this email to'
    ],

    'mailtracking'          => [
        'subject'           => 'Tracking your order: ',
    ],

    'mailfailed'            => [
        'subject'           => 'Glovo Order Failed',
        'title'             => 'Something went wrong',
        'description'       => 'An unexpected error occurred when trying to process the order in glove. Please enter the application to review the problem',
        'action'            => 'Solve the problem'
    ],

    'tracking'              => [
        'ordertitle'        => 'Order',
        'thanks'            => 'Thanks',
        'thanks_purchase'   => 'Thanks for your purchase',
        'order_summary'     => 'Order summary',
        'order_confirm'     => 'Your order is confirmed',
        'order_tracking'    => 'Your order started to be delivered, monitors the location on the map',
        'customer_info'     => 'Customer information',
        'contact_info'      => 'Contact information',
        'address_shipping'  => 'Shipping Address',
        'address_delivery'  => 'Delivery Address',
        'shipping_method'   => 'Shipping method',
        'payment_method'    => 'Payment method',
        'payment_end'       => 'Ends in',
        'emailcontact'      => 'If you have any questions, reply to this email to',
        'contactus'         => 'Get in contact with us',
        'backstore'         => 'Return to the store',
        'needhelp'          => 'Need help?',
        'summary'            => [
            'summary'       => 'Cost Summary',
            'description'   => 'Description',
            'price'         => 'Price',
            'subtotal'      => 'Subtotal',
            'shipping'      => 'Shipping',
            'taxes'         => 'Taxes',
            'total'         => 'Total',

        ],

        'states'            => [
            'scheduled'     => [
                'title'     => 'Your order is scheduled',
                'subtitle'  => 'Your order will start processing until'
            ],
            'active'     => [
                'title'     => 'In process',
                'subtitle'  => 'The order has started the delivery process'
            ],
            'delivered'     => [
                'title'     => 'Delivered',
                'subtitle'  => 'The order has been delivered, by the courier:'
            ],
            'canceled'     => [
                'title'     => 'The order was canceled',
                'subtitle'  => 'The order can not be processed and has been canceled'
            ]

        ]
    ],

    'mixed'     => [
        'notes'             => "Aditional note: ",
        'contactperson'     => "Contact phone",
        'contactphone'      => "Contact person",

    ]

];
