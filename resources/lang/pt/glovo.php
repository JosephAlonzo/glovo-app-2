<?php

return [
    'storefront' => [
        'template' => [
            'products' => [
                'available' => 'Este produto está disponível para entrega por ',
                'choosewhen'=> 'Escolha no carrinho quando quiser recebê-lo'
            ],
            'cart'      => [
                'loading'=> 'Encontrar prazos de entrega disponíveis.',
                'estimated'=> [
                    'hour'  => 'Seu pedido levará aproximadamente <b> {estimate_hours}:{estimate_min} </b> horas para preparar',
                    'minute'=>'O pedido levará aproximadamente <b> {estimate_time} </b> min para ser preparado.',
                    'immediately'=>'<i>O pedido está disponível imediatamente para entrega</i>'
                ],
                'available'=> 'Um ou mais itens do seu carrinho estão disponíveis para o serviço de entrega Glovo.',
                'noallowscheduled'=> 'Escolha glovo na próxima etapa para receber seu pedido !.',
                'when' => [
                    'label' => 'Quando você gostaria de receber seu pedido?',
                    'question'=>'Escolha quando deseja receber seu pedido.',
                    'assoon'  => 'Senvie o mais rápido possível'
                ],
                'day'   => 'Dia',
                'hour'  => 'Hora'
            ]
        ],

        'shipping' => [
            'rate'      => '(Verifique o carrinho para escolher a data / hora da entrega)'
        ]
    ],

    'welcome'   => [

        'gettinstarted' => "Guia de Introdução",
        'p1'            => "Siga estas etapas para ativar a entrega do Shopify Glovo",
        'l1'            => [
            't1'        =>  "Modificar",
            't2'        =>  "Depois de instalar o aplicativo, um snippet \"liquid\" chamado",
            't3'        =>  "está instalado no seu tema de loja atual. Este snippet é responsável por exibir as configurações de exibição na página do carrinho.",
            't4'        =>  "Para ativar o snippet, abra o <a target=\"blank\" href=\"https://docs.shopify.com/manual/configuration/store-customization/#template-editor\">Theme Editor</a> na sua loja admin, em seguida, abra <code class=\"code_span\">Templates/cart.liquid</code> e adicione <code class=\"code_span\">{% include 'snippet-glovo-delivery-cart' %}</code> entre a abertura <code class=\"code_span\">&lt;form&gt;</code> e o fechamento <code class=\"code_span\">&lt;/form&gt;</code> tag.",
            't5'        =>  "O posicionamento exato entre essas tags não é crítico, mas um bom lugar é imediatamente acima das notas do carrinho ou instruções especiais (<code class=\"code_span\">{% if settings.show_cart_notes %}</code> <strong>or</strong> <code class=\"code_span\">{% if settings.special_instructions %}</code> <strong>or</strong> <code class=\"code_span\">{% if settings.additional_informaiton %}</code>).",
            't6'        =>  "Por exemplo:",
            't7'        =>  "Lembre-se de salvar as alterações quando terminar.",
        ],

        'l2'            => [
            't1'        => 'Vá para o menu de configurações',
            't2'        => [
                'title' => "Lo primero que debes configurar son las APIs para conectarse a la API de Glovo y Google Maps, por ejemplo, proporcionar las claves de la API de Glovo. Para obtenerlas puedes registrarte en el sitio. <a target=\"blank\" href=\"https://business.glovoapp.com/login\">Glovo Bussiness</a>",
                "helps"     => [
                    'c1'    => '<b>Ativar envio: </b> Ative o serviço de entrega na loja usando o serviço Glovo',
                    'c2'    => '<b>Idioma: </b> Escolha o idioma da sua loja. Por padrão (idioma do Frontstore)',
                    'c3'    => '<b>API do Google Maps: </b> Forneça a API do Google Maps, o aplicativo requer esta API para a localização geográfica do endereço. Obtenha uma API <a href="https://developers.google.com/maps/documentation/embed/get-api-key"> Google Developers </a>.',
                    'c4'    => '<b>API comercial da Glovo: </b> Forneça APIs da Glovo, o aplicativo exige que ele faça pedidos. Obtenha uma API <a href="https://business.glovoapp.com"> Glovo Business </a>.',
                    'c5'    => '<b>Título </b> É o nome que aparecerá na loja para identificar o serviço glovo',
                    'c6'    => '<b>Custo </b> Escolha como você deseja cobrar o custo do serviço. 1.- Gratuito, 2.- Calculado pela API do Glovo usando geolocalização. 3.- Preço fixo',
                    'c7'    => '<b>Agendar remessas </b> Esta opção permite ao usuário fazer a programação para receber seu pedido. Caso contrário, ele tentará enviar imediatamente',
                    'c8'    => '<b>Quando criar o pedido </b> Escolha quando deseja que o pedido do glovo seja feito: 1.- Pagamento autorizado, 2.- Pedido pago, 3.- Manual',
                ],
            ],

            't3'            => [
                'title'     => "Estabeleça a localização da loja, os dias e as horas de trabalho em que o serviço de entrega da Glovo estará disponível.",
                "helps"     => [
                    'c1'    => '<b>Local da loja: </b> Configure o local da loja para estar dentro da área de cobertura da Glovo. Ver cidades com cobertura <a href="https://glovoapp.com/pt/map"> Mapa de cobertura </a>',
                    'c2'    => '<b>Dias de assistência: </b> Configure os dias e as horas de assistência para encomendar glovo',
                    'c3'    => '<b><b> Dias de assistência: </b> Configure os dias e as horas de assistência para encomendar glovo</a>.',
                ],
            ],

            'l4' => [
                't1'        => "O serviço está ativado no modelo do produto",
                't2'        => "Na loja, as informações do produto parecerão enviadas pelo serviço Glovo",
            ],

            'l5' => [
                't1'        => "Escolha quando você deseja receber seu pedido",
                't2'        => "Se a configuração do cronograma de entrega estiver ativada, o comprador poderá escolher quando receberá seu pedido. Caso contrário, ele será enviado o mais rápido possível",
            ]
        ],

        'l3'            => [
            't1'        => "Escolha os produtos que podem ser enviados pela glovo",
            't2'        => "Configurar a disponibilidade dos produtos a serem entregues com o serviço Glovo",
            't3'        => "Defina o tempo estimado de preparação dos produtos disponíveis. Para poder oferecer a programação de entrega ao verificar o carrinho"

        ]



    ],


    'general' => [
        'section'       => 'Configurações Glovo',
        'subsection'    => 'Definir configurações básicas',
        'basic'         => 'Configurações básicas',

    ],

    'enable'=> [
        'label'=> 'Ativar o Glovo Shipping',
        'desc' => 'Você quer ativar o Glovo Delivering?'
    ],

    'language'=> [
        'label'=> 'Escolha um idioma',
        'desc' => 'Definir o idioma principal da loja'
    ],

    'server'=> [
        'label'=> 'Servidor Api',
        'desc' => 'Use o servidor produtivo ou de teste'
    ],




    'glovoapi'=> [
        'label'=> 'Chave da API do Glovo',
        'tip'   => 'Chave de API fornecida pela glovo',
        'desc' => 'Obtenha sua chave de api no site glovo <a target="_blank" href="https://business.glovoapp.com"> https://business.glovoapp.com </a>'
    ],

    'glovosecret'=> [
        'label'=> 'Segredo da API do Glovo',
        'desc' => 'Segredo da API fornecido pela glovo'
    ],

    'googleapi'=> [
        'label'=> 'Chave da API do Google Maps',
        'tip' => 'Chave de API fornecida pelo google maps',
        'desc' => 'Chave de API fornecida pelo Google Maps. Obtenha seu apikey no site <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key"> https://developers.google.com/maps </a>'
    ],


    'method' => [
        'label'=> 'Título do método',
        'desc' => 'Título para o método de entrega de frete glovo. Como o cliente verá o título ao fazer o checkout'
    ],

    'cost' => [
        'label'=> 'Custo de entrega',
        'desc' => 'Escolha o caminho para calcular a taxa de envio. <br><br> * Note que o Shopify tem um cache para evitar múltiplas requisições. Pode demorar alguns minutos para atualizar as taxas. Se você alterar qualquer produto no carrinho de compras, ele será atualizado imediatamente',
        'types' => [
            'free'  => "Livre",
            'calculate'=> "Calculado pela API Glovo",
            'fixed' => "Baseado em um preço fixo",
        ]

    ],

    'allowscheduled'    => [
        'label'     => 'Permitir agendamento de remessa',
        'desc'      => 'Ativo: permite que o comprador escolha a hora / dia para agendar a remessa. <br> Desativar: os pedidos serão feitos o mais rápido possível'
    ],

    'createorderstatus'    => [
        'label'     => 'Criar pedido quando o status for',
        'desc'      => 'Escolha o estado que o pedido do Shopify deve cumprir para executar o pedido do glovo'
    ],

    'locations' => [
        'section'       => 'Configurar endereços de entrega',
        'subsection'    => 'definir localização principal',
        'coverage'     => 'O local da loja deve estar dentro da área de serviço da glovo. Veja cidades com cobertura <a href="https://glovoapp.com/en/map"> Mapa de cobertura. </a>',
        'primary'   => 'Para editar locais, vá para Configuração -> Locais'

    ],


    'address'   => [
        'enable'=> [
            'label'=> 'Ativar entrega de Glovo',
            'desc' => 'Deseja ativar o Glovo Delivering para o local da loja?'
        ],
        'lat'           => 'Localização Lat',
        'lng'           => 'Localização Lng',
        'city'          => 'Nome da Cidade',
        'storename'     => 'Nome da loja',
        'address1'      => 'Endereço 1',
        'address2'      => 'Endereço 2',
        'postcode'      => 'Código postal',
        'phone'         => 'Telefone de contato',
        'country'       => 'Nome do país',
        'province'      => 'Province',

    ],

    'workinghours'   => [
        'section'       => 'Horas de trabalho',
        'subsection'    => 'Definir dias e horários de trabalho em que a loja fornece o serviço de entrega',
        'days'          => [
            '0' => "Domingo",
            '1' => "Segunda-feira",
            '2' => "Terça-feira",
            '3' => "Quarta-feira",
            '4' => "Quinta-feira",
            '5' => "Sexta-feira",
            '6' => "Sábado"
        ],
        'today'     => 'Hoje',
        'tomorrow'  => 'Domani'
    ],


    'holidays'   => [
        'section'       => 'Feriados dias',
        'subsection'    => 'Definir feriados. Nestes dias não haverá serviço de entrega',
        'label'         => 'Defina as férias',
        'sublabel'      => 'Adicione os dias em que o serviço glovo não estará disponível',
        'addbuttom'     => 'Adicionar feriado',
        'deletebuttom'  => 'Excluir'
    ],

    'months'    => [
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Março',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro',
    ],


    'buttons'       => [
        'save'      => 'Salve',
        'delete'    => 'Excluir',
        'cancel'    => 'Cancelar',
        'test'      => 'Conexão de teste'
    ],


    'settings'      => [
        'save'      => [
            'success'   => 'A configuração foi salva',
            'failed'    => 'Houve um problema ao salvar a configuração'
        ],
        'messages'      => [
            'required'  => 'Este campo é obrigatório.'
        ]
    ],

    'validated'         => [
        'title'         => 'Não foi possível validar o endereço da loja. Certifique-se de que as credenciais do glovo e do google estão corretas. A loja deve estar dentro da área de serviço glovo, verificar o endereço da loja',
    ],

    'validatedplan'     => [
        'noallow'       => 'Seu plano de loja impede que você calcule taxas calculadas. Para usar essa funcionalidade na finalização da compra, você deve ter um plano avançado ou um plano anual. No entanto, os pedidos criados podem ser enviados individualmente à glovo para entrega ao cliente.'
    ],

    'products'          => [

        'header'        => [
            'title'     => 'Produtos',
            'subtitle'  => 'Permite a entrega de produtos pelo serviço de entrega da Glovo'
        ],
        'table'         => [
            'headers'   => [
                'product'   => 'Produtos',
                'type'      => 'Tipo',
                'vendor'    => 'Fornecedor',
                'available' => 'Disponível para glovo',


            ]
        ],
        'filters'       => [
            'apply'     => 'Produto de filtro',
            'remove'    => 'Filtro limpo'
        ]

    ],


    'preparationform' => [
        'modal'         => [
            'title'     => 'Tempo estimado de preparação'

        ],

        'enable'        => [
            'label'     => 'Ativar este produto para entrega',
            'desc'      => 'Use esta opção para habilitar a entrega deste produto pelo serviço de entrega Glovo',

        ],

        'availability'  => [
            'label'     => 'Imediatamente',
            'no'        => 'Não disponível'

        ],


        'preparation'   => [
            'label'     => 'Tempo estimado de preparação',
            'desc'      => 'Selecione o tempo estimado de preparação do produto. Ele será usado para a disponibilidade de agendamentos. <br> <br> Por exemplo, se a loja fechar às 22:00 e o produto demorar 30 minutos. A hora máxima de serviço será às 21:30',
            'labeldelete'=> 'Excluir tempo estimado'
        ],

        'save'          => [
            'success'   => 'As informações do produto foram salvas',
            'error'     => 'Ocorreu um erro ao salvar informações do produto'
        ],
        'delete'          => [
            'success'   => 'As informações do produto foram salvas',
            'error'     => 'Ocorreu um erro ao salvar informações do produto'
        ]
    ],


    'orders'        => [

        'header'        => [
            'title'     => 'Encomendas',
            'subtitle'  => 'Encomendas entregues pela glovo'
        ],

        'table'         => [
            'headers'   => [
                'order'     => '#Ordem',
                'date'      => 'Data de entrega',
                'customer'  => 'Cliente',
                'deliveryaddress' => 'Endereço de entrega',
                'paid'      => 'Status do pagamento',
                'glovostatus' => 'Status do Glovo',


            ]
        ]
    ],



    'orderdetail'   => [

        'panel'     => [
            'title' => 'Status do pedido Glovo'
        ],

        'pickupaddress' => [
           'title'          => 'Endereço de coleta'
        ],
        'contact' => [
           'title'          => 'Endereço de contato'
        ],
        'destination' => [
            'title'         => 'Endereço de entrega'
        ],
        'viewmap' => [
            'title'         => 'Ver mapa'
        ],


        'glovostatus'       => [
            'state'         => 'Estado',
            'orderid'       => 'ID do pedido',
            'schedule'      => 'Hora agendada',
            'description'   => 'Descrição',
            'courier'       => 'Nome do correio',
            'phone'         => 'Telefone',
            'created_at'    => 'Criado em',

            'failedstatus'  => 'Status com falha',
            'failedmessage' => 'Mensagem com falha',
            'retry'         => 'Tente reenviar',
            'checklink'     => 'Visite o Glovo Business'
        ],

        'nocreate'          => [
            'label'         => 'O pedido ainda não foi criado no Glovo. Clique no botão "Criar pedido" para criá-lo agora',
            'buttom'        => 'Criar pedido glovo'
        ],

        'create'            => [
            'success'       => "Pedido criado com sucesso",
            'fail'          => "Ocorreu um erro durante o processamento do pedido. Com mensagem: "
        ],

        'resend'            => [
            'success'       => "O pedido foi processado corretamente.",
            'fail'          => "Ocorreu um erro durante o processamento do pedido. Com mensagem: "
        ],

        'delivery'          => [
            'scheduled'     => 'O pedido estava programado para ser entregue às',
            'immmediately'  => 'O pedido será enviado',
            'posibility'    => 'L\'ordine può essere spedito tramite Glovo Delivery. Per inviarlo manualmente, fai clic su Invia'
        ]

    ],

    'mailsend'          => [
        'ordertitle'        => 'Ordem',
        'hello'             => 'Olá',
        'preparing'         => 'Estamos preparando seu pedido para buscá-lo. Aqui você pode ver o rastreamento de pedidos',
        'thanks'            => 'Obrigado pela sua compra',
        'track_title'       => 'Acompanhe seu pedido',
        'visit_store'       => 'Visite nossa loja',
        'customer_info'     => 'Informação ao Cliente',
        'address_shipping'  => 'endereço de entrega',
        'address_delivery'  => 'Endereço de entrega',
        'shipping'          => 'Método de envio',
        'payment'           => 'Método de pagamento',
        'payment_end'       => 'Termina em',
        'emailcontact'      => 'Se você tiver alguma dúvida, responda a este e-mail para'
    ],

    'mailtracking'          => [
        'subject'           => 'Acompanhe seu pedido: ',
    ],


    'mailfailed'            => [
        'subject'           => 'Falha no pedido de Glovo',
        'title'             => 'Algo deu errado',
        'description'       => 'Ocorreu um erro inesperado ao tentar processar o pedido na luva. Por favor, insira o aplicativo para rever o problema',
        'action'            => 'Resolva o problema'
    ],

    'tracking'              => [
        'ordertitle'        => 'Ordem',
        'thanks'            => 'Obrigado',
        'thanks_purchase'   => 'Obrigado pela sua compra',
        'order_summary'     => 'Resumo do pedido',
        'order_confirm'     => 'Seu pedido é confirmado',
        'order_tracking'    => 'Seu pedido começou a ser entregue, monitora a localização no mapa',
        'customer_info'     => 'Informação ao Cliente',
        'contact_info'      => 'Informações de contato',
        'address_shipping'  => 'endereço de entrega',
        'address_delivery'  => 'Endereço de entrega',
        'shipping_method'   => 'Método de envio',
        'payment_method'    => 'Método de pagamento',
        'payment_end'       => 'Termina em',
        'emailcontact'      => 'Se você tiver alguma dúvida, responda a este e-mail para',
        'contactus'         => 'Entre em contato conosco',
        'backstore'         => 'Retornar para a loja',
        'needhelp'          => 'Preciso de ajuda?',
        'summary'            => [
            'summary'       => 'Resumo de custo',
            'description'   => 'Descrição',
            'price'         => 'Preço',
            'subtotal'      => 'Subtotal',
            'shipping'      => 'Remessa',
            'taxes'         => 'Impostos',
            'total'         => 'Total',

        ],

        'states'            => [
            'scheduled'     => [
                'title'     => 'Entrega agendada',
                'subtitle'  => 'Seu pedido começará a ser processado até'
            ],
            'active'     => [
                'title'     => 'Em processo',
                'subtitle'  => 'O pedido iniciou o processo de entrega'
            ],
            'delivered'     => [
                'title'     => 'Entregue',
                'subtitle'  => 'O pedido foi entregue pelo mensageiro:'
            ],
            'canceled'     => [
                'title'     => 'O pedido foi cancelado',
                'subtitle'  => 'O pedido não pode ser processado e foi cancelado'
            ]

        ]
    ],

    'mixed'     => [
        'notes'     => "Nota adicional: "

    ]

];
