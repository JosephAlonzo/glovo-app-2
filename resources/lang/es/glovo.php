<?php

return [
    'storefront' => [
        'template' => [
            'products' => [
                'available' => 'Este producto está disponible para su entrega por ',
                'choosewhen'=> 'Elije en el proximo paso cuando deseas recibirlo'
            ],
            'cart'      => [
                'loading'=> 'Buscando tiempos de entrega disponibles..',
                'estimated'=> [
                    'hour'  =>'El pedido tardará  <b> {estimate_hours}: {estimate_min} </b> horas aproximadamente para prepararse',
                    'minute'=>'El pedido tardará  <b> {estimate_time} </b> min aproximadamente en prepararse.',
                    'immediately'=>'<i>El pedido está disponible inmediatamente para la entrega.</i>'
                ],
                'available'=> 'Uno o más de los artículos en su carrito están disponibles para el servicio de entrega por Glovo.',
                'noallowscheduled'=> '¡Elija envio por Glovo en el siguiente paso para recibir su pedido!',
                'when' => [
                    'label' => '¿Cuándo le gustaría recibir su pedido?',
                    'question'=>'Elija cuándo desea recibir su pedido.',
                    'assoon'  => 'Enviar lo antes posible'
                ],
                'day'   => 'Día',
                'hour'  => 'Hora'
            ],
        ],

        'shipping' => [
            'rate'      => '(Revisa el carrito para Elegir fecha/hora entrega)'
        ]
    ],

    'welcome'   => [

        'gettinstarted' => "Guía de inicio",
        'p1'            => "Siga estos pasos para habilitar entregas por el servicio de Glovo",
        'l1'            => [
            't1'        =>  "Modifica",
            't2'        =>  "Después de instalar la aplicación, se debe de crear un segmento de plantilla llamado",
            't3'        =>  "se instala en el tema default de su tienda. Este fragmento es responsable de mostrar la configuración de entrega  por Glovo en la página del carrito.",
            't4'        =>  "Para activar el fragmento, abre el <a target=\"blank\" href=\"https://docs.shopify.com/manual/configuration/store-customization/#template-editor\">Theme Editor</a> en el administrador de tu tienda, luego abre <code class=\"code_span\">Templates/cart.liquid</code> y agrega <code class=\"code_span\">{% include 'snippet-glovo-delivery-cart' %}</code> entre las etiquetas de apertura <code class=\"code_span\">&lt;form&gt;</code> y de cierre <code class=\"code_span\">&lt;/form&gt;</code>.",
            't5'        =>  "La ubicación exacta entre estas etiquetas no es crítica, pero un buen lugar está inmediatamente encima de las notas del carrito o instrucciones especiales (<code class=\"code_span\">{% if settings.show_cart_notes %}</code> <strong>or</strong> <code class=\"code_span\">{% if settings.special_instructions %}</code> <strong>o</strong> <code class=\"code_span\">{% if settings.additional_informaiton %}</code>).",
            't6'        =>  "Por ejemplo:",
            't7'        =>  "Recuera que debes de guardar los cambios despues de modificar la plantilla.",
        ],

        'l2'            => [
            't1'        => 'Ir al  menu settings',
            't2'        => [
                'title' => "Lo primero que debes configurar son las APIs para conectarse a la API de Glovo y Google Maps, por ejemplo, proporcionar las claves de la API de Glovo. Para obtenerlas puedes registrarte en el sitio. <a target=\"blank\" href=\"https://business.glovoapp.com/login\">Glovo Bussiness</a>",
                "helps"     => [
                    'c1'    => '<b>Habilitar el envio:</b> Habilita en la tienda el servicio de entrega utilizando el servicio de Glovo',
                    'c2'    => '<b>Idioma:</b> Elije el idioma de tu tienda. Por default (Frontstore idioma)',
                    'c3'    => '<b>API google maps:</b> Proporciona la API de google maps, la app requiere esta api para la geocalización de direcciones. Obten aquí una api <a href="https://developers.google.com/maps/documentation/embed/get-api-key">Google Developers</a>.',
                    'c4'    => '<b>API Glovo Business:</b> Proporciona las APIs de Glovo, la app requiere esta api para realizar pedidos. Obten aquí una api <a href="https://business.glovoapp.com">Glovo Business</a>.',
                    'c5'    => '<b>Título</b> Es el nombre que aparecera en la tienda para identificar el servicio de glovo',
                    'c6'    => '<b>Costo</b> Elige como deseas cobrar el costo del servicio. 1.- Gratis, 2.- Calculado por la api de glovo usando la geolocalización. 3.- Precio fijo',
                    'c7'    => '<b>Programar envios</b> Esta opcion permite al usuario hacer la programación para recibir su orden. De lo contrario se tratara de enviar de inmediato',
                    'c8'    => '<b>Cuando crear la orden</b> Elije cuando quieres que se realice el pedido de glovo, 1.- Pago autorizado, 2.- Orden pagada, 3.- Manual',
                ],
            ],

            't3'            => [
                'title'     => "Establezca ubicación de la tienda, los días y horas de trabajo en los que el servicio de entrega por Glovo estara disponible.",
                "helps"     => [
                    'c1'    => '<b>Ubicación de la tienda:</b> Configura la ubicación de la tienda para que este dentro del área de covertura de Glovo. Consulta las ciudades con covertura <a href="https://glovoapp.com/en/map">Mapa de covertura</a>. Si deseas cambiar la ubicación de la tienda sigue los siguientes pasos:',
                    'c2'    => '<b>Días de servicio:</b> Configura los días y horarios de servicio para realizar pedidos de glovo',
                    'c3'    => '<b>Días festivos:</b> Proporciona los días feriados, en estos días el servicio no estara disponible</a>.',
                ],
            ]

        ],

        'l3'            => [
            't1'        => "Elige los productos que pueden ser enviados a traves del servicio de Glovo. Recuerda que la capacidad maxima del glover es 9KG",
            't2'        => "Configure la disponibilidad de los productos a entregar con el servicio Glovo.",
            't3'        => "Establecer el tiempo estimado de preparación de los productos disponibles. Para poder ofrecerle el horario de entrega al momento de revisar el carrito."
        ],

        'l4'            => [
            't1'        => "El servicio se activa en el template del producto",
            't2'        => "En la tienda aparecerá la información del producto para ser enviado a travez del servicio de Glovo",
        ],

        'l5'            => [
            't1'        => "Elige cuando deseas recibir tu pedido",
            't2'        => "Si esta activada la configuración de programación de envios, el comprador puede elegir cuando recibira su orden. De lo contrario se enviará lo mas pronto posible",
        ]





    ],


    'general' => [
        'section'       => 'Configuraciones para glovo',
        'subsection'    => 'Configuraciones basicas',
        'basic'         => 'Ajuste las configuraciones basicas',

    ],

    'enable'=> [
        'label'=> 'Habilitar el envío por Glovo',
        'desc' => '¿Quieres activar el servicio de entrega por Glovo Delivering?'
    ],

    'language'=> [
        'label'=> 'Elige tu idioma',
        'desc' => 'Elige el idioma principal de la tienda.'
    ],

    'server'=> [
        'label'=> 'Servidor',
        'desc' => 'Utilizar el servidor productivo o de prueba.'
    ],




    'glovoapi'=> [
        'label'=> 'Clave API de Glovo',
        'tip'   => 'Clave API proporcionada por glovo',
        'desc' => 'Obtenga su clave API en el sitio glovo <a target="_blank" href="https://business.glovoapp.com"> https://business.glovoapp.com </a>'
    ],

    'glovosecret'=> [
        'label'=> 'Clave API Secreta',
        'desc' => 'API Secret proporcionado por glovo'
    ],

    'googleapi'=> [
        'label'=> 'Clave API de Google Maps',
        'tip' => 'Clave API proporcionada por google maps',
        'desc' => 'Clave API proporcionada por google maps. Obtenga su apikey en el sitio <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key"> https://developers.google.com/maps </a>'
    ],


    'method' => [
        'label'=> 'Título del método',
        'desc' => 'Título para el método de entrega de envío glovo. Cómo verá el cliente el título al realizar la compra'
    ],

    'cost' => [
        'label'=> 'Costo de envio',
        'desc' => 'Elige la forma de calcular la tarifa de envío.. <br><br> * Tenga en cuenta que Shopify tiene un caché para evitar múltiples solicitudes. Puede tomar unos minutos actualizar las tarifas. Si cambia algún producto en el carrito de compras, se actualizará inmediatamente.',
        'types' => [
            'free'  => "Entrega gratis",
            'calculate'=> "Calculado por Glovo API",
            'fixed' => "Basado en un precio fijo",
        ]

    ],

    'allowscheduled'    => [
        'label'     => 'Permitir programación de envios',
        'desc'      => 'Activa: Permite al comprador elegir hora/día para realizar la programación del envio.<br> Desactiva: Los pedidos se realizaran tan pronto sea posible'
    ],

    'createorderstatus'    => [
        'label'     => 'Crear orden cuando estado sea',
        'desc'      => 'Elige el estado que debe de cumplir  la orden de Shopify para realizar la orden de glovo'
    ],


    'locations' => [
        'section'       => 'Configurar direcciones de entrega',
        'subsection'    => 'establecer ubicación primaria',
        'coverage'     => 'La ubicación de la tienda debe estar dentro del área de servicio de glovo. Vea ciudades con cobertura <a href="https://glovoapp.com/en/map"> Mapa de cobertura. </a> ',
        'primary'       => 'Para ubicaciones de edición vaya a Configuración -> Ubicaciones'

    ],


    'address'   => [
        'enable'=> [
            'label'=> 'Habilitar el envío de Glovo',
            'desc' => '¿Desea activar la entrega de Glovo para la ubicación de la tienda?'
        ],
        'lat'           => 'Ubicación Lat',
        'lng'           => 'Ubicación Lng',
        'city'          => 'Nombre de la ciudad',
        'storename'     => 'Nombre de la tienda',
        'address1'      => 'Dirección 1',
        'address2'      => 'Dirección 2',
        'postcode'      => 'Código postal',
        'phone'         => 'Teléfono de contacto',
        'country'       => 'Nombre del país',
        'province'      => 'Provincia/Estado',

    ],

    'workinghours'   => [
        'section'       => 'Horas de servicio',
        'subsection'    => 'Establecer días y horarios de trabajo en los que la tienda presta el servicio de entrega.',
        'days'          => [
            '0'    => "Domingo" ,
            '1'    => "Lunes" ,
            '2'    => "Martes" ,
            '3'    => "Miércoles" ,
            '4'    => "Jueves" ,
            '5'    => "Viernes" ,
            '6'    => "Sábado" ,
        ],
        'today'     => 'Hoy',
        'tomorrow'  => 'Mañana'
    ],


    'holidays'   => [
        'section'       => 'Dias feriados',
        'subsection'    => 'Establecer días feriados. En estos días no habrá servicio de entrega.',
        'label'         => 'Establecer días feriados',
        'sublabel'      => 'Agregue los días en que el servicio de glovo no estará disponible.',
        'addbuttom'     => 'Agregar día feriado',
        'deletebuttom'  => 'Borrar'
    ],

    'months'    => [
        '01' => 'Enero',
        '02' => 'Febrero',
        '03' => 'Marzo',
        '04' => 'Abril',
        '05' => 'Mayo',
        '06' => 'Junio',
        '07' => 'Julio',
        '08' => 'Agosto',
        '09' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre'
    ],


    'buttons'       => [
        'save'      => 'Guardar',
        'delete'    => 'Borrar',
        'cancel'    => 'Cancelar',
        'test'      => 'Probar conexión'
    ],


    'settings'      => [
        'save'      => [
            'success'   => 'Configuración guardada',
            'failed'    => 'Ocurrio un problema al guardar la configuracion'
        ],
        'messages'      => [
            'required'  => 'Este campo es requerido.'
        ]

    ],


    'validated'         => [
        'title'         => 'No se pudo validar la dirección de la tienda. Asegurate que las credenciales de Glovo y Google sean correctas. La tienda debe estar dentro el area de servicio de Glovo, verifica la dirección de la tienda',
    ],

    'validatedplan'         => [
        'noallow'       => 'El plan de tu tienda impide poder realizar el calculo de tarifas calculadas, para poder utilizar esta funcionalidad en el checkout debes de tener un Plan Avanzado o un Plan Anual. Sin embargo las ordenes que se creen las puedes enviar de forma individual a glovo para su entrega al cliente.'
    ],



    'products'          => [

        'header'        => [
            'title'     => 'Productos',
            'subtitle'  => 'Permite la entrega de productos por parte del servicio de entrega de Glovo.'
        ],
        'table'         => [
            'headers'   => [
                'product'   => 'Producto',
                'type'      => 'Tipo',
                'vendor'    => 'Vendedor',
                'available' => 'Disponible para glovo',


            ]
        ],
        'filters'       => [
            'apply'     => 'Filtrar producto',
            'remove'    => 'Limpiar filtro'
        ]


    ],


    'preparationform' => [
        'modal'         => [
            'title'     => 'Tiempo estimado de preparación.'

        ],

        'enable'        => [
            'label'     => 'Habilitar este producto para la entrega',
            'desc'      => 'Utilice esta opción para habilitar la entrega de este producto por el servicio de entrega de Glovo',

        ],

        'availability'  => [
            'label'     => 'Inmediatamente',
            'no'        => 'No disponible'

        ],


        'preparation'   => [
            'label'     => 'Tiempo estimado de preparación.',
            'desc'      => 'Seleccione el tiempo estimado de preparación del producto. Se utilizará para la disponibilidad de horarios. <br> <br> Por ejemplo, si la tienda cierra a las 10:00 pm y el producto tarda 30 minutos. La hora máxima de servicio será 09:30 PM.',
            'labeldelete'=> 'Eliminar el tiempo estimado'
        ],

        'save'          => [
            'success'   => 'La información del producto fue guardada.',
            'error'     => 'Se produjo un error al guardar la información del producto'
        ],
        'delete'          => [
            'success'   => 'La información del producto fue guardada.',
            'error'     => 'Se produjo un error al guardar la información del producto'
        ]
    ],


    'orders'        => [

        'header'        => [
            'title'     => 'Ordenes',
            'subtitle'  => 'Los pedidos que se entregan por Glovo'
        ],

        'table'         => [
            'headers'   => [
                'order'         => '#Orden',
                'date'          => 'Fecha de entrega',
                'customer'      => 'Cliente',
                'deliveryaddress' => 'Dirección de entrega',
                'paid'          => 'Estado de pago',
                'glovostatus'   => 'Estado de Glovo',


            ]
        ]
    ],



    'orderdetail'   => [

        'panel'     => [
            'title' => 'Estado de pedido de Glovo'
        ],

        'pickupaddress' => [
           'title'          => 'Dirección de origen'
        ],
        'contact' => [
           'title'          => 'Dirección de contacto'
        ],
        'destination' => [
            'title'         => 'Dirección de entrega'
        ],
        'viewmap' => [
            'title'         => 'Ver el mapa'
        ],


        'glovostatus'       => [
            'state'         => 'Estado',
            'orderid'       => '#Orden Glovo',
            'schedule'      => 'Hora programada',
            'description'   => 'Descripción',
            'courier'       => 'Nombre del mensajero',
            'phone'         => 'Teléfono',
            'created_at'    => 'Creado en',

            'failedstatus'  => 'Estado del fallo',
            'failedmessage' => 'Mensaje del fallo',
            'retry'         => 'Tratar de reenviar',
            'checklink'     => 'Visita Glovo Bussines'
        ],

        'nocreate'          => [
            'label'         => 'La orden no ha sido creado en Glovo. Clic en el boton "Solicitar orden de Glovo" para crear la orden ahora mismo',
            'buttom'        => 'Enviar orden a Glovo'
        ],

        'create'            => [
            'success'       => "Orden creada exitosamente",
            'fail'          => "Se ha producido un error al procesar el pedido. Con mensaje: "
        ],

        'resend'            => [
            'success'       => "El pedido fue procesado correctamente..",
            'fail'          => "Se ha producido un error al procesar el pedido. Con mensaje: "
        ],

        'delivery'          => [
            'scheduled'     => 'La orden fue programada para ser enviada',
            'immmediately'  => 'La orden se enviara',
            'posibility'    => 'La orden puede ser enviada utilizando Glovo Delivery. Para enviarla manualmente has clic en enviar'
        ]

    ],

    'mailsend'          => [
        'ordertitle'        => 'Orden',
        'hello'             => 'Hola',
        'preparing'         => 'Estamos preparando su pedido para recogerlo. Aquí puedes ver el seguimiento del pedido.',
        'thanks'            => 'Gracias por su compra',
        'track_title'       => 'Rastrea tu orden',
        'visit_store'       => 'Visita nuestra tienda',
        'customer_info'     => 'Información al cliente',
        'address_shipping'  => 'Dirección de Envío',
        'address_delivery'  => 'Dirección de Envío...',
        'shipping'          => 'Método de envío',
        'payment'           => 'Método de pago',
        'payment_end'       => 'Termina en',
        'emailcontact'      => 'Si tiene alguna pregunta, responda a este correo electrónico a'
    ],

    'mailtracking'          => [
        'subject'           => 'Rastrea tu pedido: ',
    ],

    'mailfailed'            => [
        'subject'           => 'Error al procesar la orden de Glovo',
        'title'             => 'Algo salió mal',
        'description'       => 'Se ha producido un error inesperado al intentar procesar el pedido en Glovo. Por favor ingrese la aplicación para revisar el problema',
        'action'            => 'Resolver el problema'
    ],

    'tracking'              => [
        'ordertitle'        => 'Orden',
        'thanks'            => 'Gracias',
        'thanks_purchase'   => 'Gracias por su compra',
        'order_summary'     => 'Resumen de la orden',
        'order_confirm'     => 'Tu pedido esta confirmado',
        'order_tracking'    => 'Su pedido comenzó a ser entregado, monitorea la ubicación en el mapa',
        'customer_info'     => 'Información al cliente',
        'contact_info'      => 'Información del contacto',
        'address_shipping'  => 'Dirección de Envío',
        'address_delivery'  => 'Dirección de entrega',
        'shipping_method'   => 'Método de envío',
        'payment_method'    => 'Método de pago',
        'payment_end'       => 'Termina en',
        'emailcontact'      => 'Si tiene alguna pregunta, responda a este correo electrónico a',
        'contactus'         => 'Ponte en contacto con nosotros',
        'backstore'         => 'Volver a la Tienda',
        'needhelp'          => '¿Necesitas ayuda?',
        'summary'            => [
            'summary'       => 'Costo total',
            'description'   => 'Descripción',
            'price'         => 'Precio',
            'subtotal'      => 'Subtotal',
            'shipping'      => 'Envío',
            'taxes'         => 'Impuestos',
            'total'         => 'Total',

        ],

        'states'            => [
            'scheduled'     => [
                'title'     => 'Programada',
                'subtitle'  => 'Tu orden se encuentra programada y empezara a procesarse hasta '
            ],
            'active'     => [
                'title'     => 'En progreso',
                'subtitle'  => 'Tu orden se encuentra en proceso de entrega'
            ],
            'delivered'     => [
                'title'     => 'Entregada',
                'subtitle'  => 'La orden ha sido entregada, por el glover: '
            ],
            'canceled'     => [
                'title'     => 'Cancelada',
                'subtitle'  => 'La orden fue cancelada'
            ]

        ],

        'mixed'     => [
            'notes'     => "Nota adicional: "

        ]






    ]

];
