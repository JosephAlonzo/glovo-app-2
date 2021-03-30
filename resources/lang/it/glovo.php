<?php

return [
    'storefront' => [
        'template' => [
            'products' => [
                'available' => 'Questo prodotto è disponibile per la consegna entro ',
                'choosewhen'=> 'Scegli nel carrello quando vuoi riceverlo'
            ],
            'cart'      => [
                'loading'=> 'Ricerca dei tempi di consegna disponibili ..',
                'estimated'=> [
                    'hour'  => 'L\'ordine impiegherà circa <b>{estimate_hours}:{estimate_min} </b> ore per prepararsi.',
                    'minute'=>'L\'ordine richiederà circa <b>{estimate_time}</b> min per prepararsi.',
                    'immediately'=>'<i>L\'ordine è immediatamente disponibile per la consegna</i>'
                ],
                'available'=> 'Uno o più articoli nel carrello sono disponibili per il servizio di consegna Glovo.',
                'noallowscheduled'=> 'Scegli glovo nel passaggio successivo per ricevere il tuo ordine !.',
                'when' => [
                    'label' => 'Quando vorresti ricevere il tuo ordine?',
                    'question'=>'Scegli quando vuoi ricevere il tuo ordine.',
                    'assoon'  => 'Invia il prima possibile'
                ],
                'day'   => 'Giorno',
                'hour'  => 'Ora'
            ]
        ],
        'shipping' => [
            'rate'      => '(Controlla il carrello per scegliere la data / ora di consegna)'
        ]
    ],

    'welcome'   => [

        'gettinstarted' => "Guida introduttiva",
        'p1'            => "Segui questi passaggi per abilitare Shopify Glovo Delivery",
        'l1'            => [
            't1'        =>  "Modificare",
            't2'        =>  "Dopo aver installato l'applicazione, viene chiamato un snippet liquido",
            't3'        =>  "è installato nel tuo attuale tema del negozio. Questo snippet è responsabile della visualizzazione delle impostazioni di consegna nella pagina del carrello.",
            't4'        =>  "Per attivare lo snippet, apri il <a target=\"blank\" href=\"https://docs.shopify.com/manual/configuration/store-customization/#template-editor\">Theme Editor</a> nel tuo amministratore del negozio, quindi apri <code class=\"code_span\">Templates/cart.liquid</code> e aggiungi<code class=\"code_span\">{% include 'snippet-glovo-delivery-cart' %}</code> tra l'apertura <code class=\"code_span\">&lt;form&gt;</code> e la chiusura <code class=\"code_span\">&lt;/form&gt;</code> tag.",
            't5'        =>  "Il posizionamento esatto tra questi tag non è critico, ma un buon posto si trova immediatamente sopra le note del carrello o le istruzioni speciali (<code class=\"code_span\">{% if settings.show_cart_notes %}</code> <strong>o</strong> <code class=\"code_span\">{% if settings.special_instructions %}</code> <strong>o</strong> <code class=\"code_span\">{% if settings.additional_informaiton %}</code>).",
            't6'        =>  "Per esempio:",
            't7'        =>  "Ricorda di salvare le modifiche quando hai finito.",
        ],

        'l2'            => [
            't1'        => 'Vai al menu delle impostazioni',
            't2'        => [
                'title' => "La prima cosa che devi configurare sono le API per connettersi all'API Glovo e Google Maps, ad esempio, per fornire le chiavi dell'API Glovo. Per ottenerli è possibile registrarsi sul sito. <a target=\"blank\" href=\"https://business.glovoapp.com/login\"> Glovo Bussiness </a>",
                "helps"     => [
                    'c1'    => '<b>Abilita spedizione: </b> abilita il servizio di consegna in negozio utilizzando il servizio Glovo',
                    'c2'    => '<b>Lingua: </b> scegli la lingua del tuo negozio. Per impostazione predefinita (lingua Frontstore)',
                    'c3'    => '<b>API di Google Maps: </b> fornisci l\'API di Google Maps, l\'app richiede questa API per la geolocalizzazione dell\'indirizzo. Ottieni un api <a href="https://developers.google.com/maps/documentation/embed/get-api-key"> Google Developers </a>.',
                    'c4'    => '<b>API Glovo Business: </b> fornire API Glovo, l\'app richiede questa app per effettuare ordini. Ottieni un api <a href="https://business.glovoapp.com"> Glovo Business </a>.',
                    'c5'    => '<b>Titolo </b> Nome che apparirà nel negozio per identificare il servizio glovo',
                    'c6'    => '<b>Costo </b> Scegli come addebitare il costo del servizio. 1.- Gratuito, 2.- Calcolato dall\'apice di glovo tramite geolocalizzazione. 3.- Prezzo fisso',
                    'c7'    => '<b>Pianifica spedizioni </b> Questa opzione consente all\'utente di eseguire la programmazione per ricevere il proprio ordine. Altrimenti proverà a inviare immediatamente',
                    'c8'    => '<b>Quando creare l\'ordine </b> Scegli quando desideri effettuare l\'ordine glovo, 1.- Pagamento autorizzato, 2.- Ordine pagato, 3.- Manuale',
                ],
            ],

            't3'            => [
                'title'     => "Stabilire l'ubicazione del negozio, i giorni e le ore di lavoro in cui sarà disponibile il servizio di consegna da parte di Glovo.",
                "helps"     => [
                    'c1'    => '<b>Posizione del negozio: </b> configura la posizione del negozio all\'interno dell\'area di copertura di Glovo. Vedi le città con copertura <a href="https://glovoapp.com/en/map"> Mappa di copertura </a>',
                    'c2'    => '<b>Giorni di servizio: </b> configura i giorni e gli orari di servizio per l\'ordinazione di glovo',
                    'c3'    => '<b> Feriados: </b> Nos feriados, atualmente o serviço não estará disponível.',
                ],
            ],

            'l4' => [
                't1'        => "Il servizio è attivato nel modello di prodotto",
                't2'        => "Nel negozio le informazioni sul prodotto appariranno inviate tramite il servizio Glovo",
            ],

            'l5' => [
                't1'        => "Scegli quando vuoi ricevere il tuo ordine",
                't2'        => "Se l'impostazione del programma di consegna è attivata, l'acquirente può scegliere quando riceverà il suo ordine. In caso contrario, verrà inviato il prima possibile",
            ]
        ],

        'l3'            => [
            't1'        => "Scegli i prodotti che possono essere inviati da glovo",
            't2'        => "Configura la disponibilità dei prodotti da consegnare con il servizio Glovo",
            't3'        => "Imposta il tempo stimato di preparazione dei prodotti disponibili. Per essere in grado di offrire il programma di consegna quando si controlla il carrello"

        ]
    ],

    'general' => [
        'section'       => 'Impostazioni Glovo',
        'subsection'    => 'Imposta le impostazioni di base',
        'basic'         => 'Impostazioni di base',

    ],

    'enable'=> [
        'label'=> 'Abilita la spedizione Glovo',
        'desc' => 'Vuoi attivare Glovo Delivering?'
    ],

    'language'=> [
        'label'=> 'Scegli una lingua',
        'desc' => 'Definire la lingua principale del negozio'
    ],

    'server'=> [
        'label'=> 'Server',
        'desc' => 'Utilizza il server produttivo o di prova'
    ],




    'glovoapi'=> [
        'label'=> 'Glovo API Key',
        'tip'   => 'Chiave API fornita da glovo',
        'desc' => 'Ottieni la tua chiave API sul sito glovo <a target="_blank" href="https://business.glovoapp.com"> https://business.glovoapp.com </a>'
    ],

    'glovosecret'=> [
        'label'=> 'Glovo API Secret',
        'desc' => 'Segreto API fornito da glovo'
    ],

    'googleapi'=> [
        'label'=> 'Chiave API di Google Maps',
        'tip' => 'Chiave API fornita da google maps',
        'desc' => 'Chiave API fornita da google maps. Ottieni il tuo apikey sul sito <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key"> https://developers.google.com/maps </a>'
    ],


    'method' => [
        'label'=> 'Titolo del metodo',
        'desc' => 'Titolo per il metodo di consegna della spedizione glovo. Come il cliente vedrà il titolo durante il checkout'
    ],

    'cost' => [
        'label'=> 'Costo della consegna',
        'desc' => 'Scegli il modo per calcolare la velocità di spedizione. <br> <br> * Nota che Shopify ha una cache per evitare richieste multiple. Potrebbero essere necessari alcuni minuti per aggiornare le tariffe. Se si modifica qualsiasi prodotto nel carrello, verrà aggiornato immediatamente',
        'types' => [
            'free'  => "Gratuito",
            'calculate'=> "Calcolato dall'API di Glovo",
            'fixed' => "Basato su un prezzo fisso",
        ]
    ],

    'allowscheduled'    => [
        'label'     => 'AConsenti programma spedizioni',
        'desc'      => 'Attivo: consente all\'acquirente di scegliere l\'ora / il giorno per programmare la spedizione. <br> Disattiva: gli ordini verranno effettuati il prima possibile'
    ],

    'createorderstatus'    => [
        'label'     => 'Crea ordine quando lo stato è',
        'desc'      => 'Scegli lo stato che l\'ordine di Shopify deve soddisfare per eseguire l\'ordine glovo'
    ],

    'locations' => [
        'section'       => 'Configura gli indirizzi di consegna',
        'subsection'    => 'imposta la posizione principale',
        'coverage'     => 'La posizione del negozio deve trovarsi all\'interno dell\'area di assistenza glovo. Vedi le città con copertura <a href="https://glovoapp.com/en/map"> Mappa di copertura. </a>',
        'primary'       => 'Per le posizioni di modifica, vai su Configurazione -> Posizioni'

    ],


    'address'   => [
        'enable'=> [
            'label'=> 'Abilita consegna Glovo',
            'desc' => 'Vuoi attivare Glovo Delivering per la posizione del negozio?'
        ],
        'lat'           => 'Posizione Lat',
        'lng'           => 'Location Lng',
        'city'          => 'Nome della città',
        'storename'     => 'Nome del negozio',
        'address1'      => 'Indirizzo 1',
        'address2'      => 'Indirizzo 2',
        'postcode'      => 'Codice postale',
        'phone'         => 'Contatto telefonico',
        'country'       => 'Nome paese',
        'province'      => 'Province',

    ],

    'workinghours'   => [
        'section'       => 'Ore di servizio',
        'subsection'    => 'Impostare giorni e orari di lavoro in cui il negozio fornisce il servizio di consegna',
        'days'          => [
            '0'    => "Domenica" ,
            '1'    => "Lunedi" ,
            '2'    => "Martedì" ,
            '3'    => "Mercoledì" ,
            '4'    => "Giovedi" ,
            '5'    => "Venerdì" ,
            '6'    => "Sabato"
        ],
        'today'     => 'Oggi',
        'tomorrow'  => 'Domani'
    ],


    'holidays'   => [
        'section'       => 'Giorni festivi',
        'subsection'    => 'Imposta le vacanze. In questi giorni non ci sarà alcun servizio di consegna',
        'label'         => 'Imposta le vacanze',
        'sublabel'      => 'Aggiungi i giorni in cui il servizio glovo non sarà disponibile',
        'addbuttom'     => 'Aggiungi vacanza',
        'deletebuttom'  => 'Elimina'
    ],

    'months'    => [
        '01'    => 'Gennaio',
        '02'    => 'Febbraio',
        '03'    => 'Marzo',
        '04'    => 'Aprile',
        '05'    => 'Maggio',
        '06'    => 'Giugno',
        '07'    => 'Julio',
        '08'    => 'Agosto',
        '09'    => 'Settembre',
        '10'    => 'Ottobre',
        '11'    => 'Novembre',
        '12'    => 'Dicembre',
    ],


    'buttons'       => [
        'save'      => 'Salvare',
        'delete'    => 'Elimina',
        'cancel'    => 'Annulla',
        'test'      => 'Test connessione'
    ],


    'settings'      => [
        'save'      => [
            'success'   => 'La configurazione è stata salvata',
            'failed'    => 'Si è verificato un problema durante il salvataggio della configurazione'
        ],
        'messages'      => [
            'required'  => 'Questo campo è obbligatorio.'
        ]
    ],

    'validated'         => [
        'title'         => 'Impossibile convalidare l\'indirizzo del negozio. Assicurati che le credenziali di glovo e google siano corrette. Il negozio deve trovarsi nell\'area di servizio glovo, verificare l\'indirizzo del negozio',
    ],

    'validatedplan'         => [
        'noallow'       => 'Il piano del negozio ti impedisce di calcolare le tariffe calcolate, al fine di utilizzare questa funzionalità alla cassa è necessario disporre di un piano avanzato o di un piano annuale. Tuttavia, gli ordini creati possono essere inviati singolarmente a glovo per la consegna al cliente.'
    ],


    'products'          => [

        'header'        => [
            'title'     => 'Prodotti',
                'subtitle'  => 'Abilita la consegna dei prodotti tramite il servizio di consegna Glovo'
        ],
        'table'         => [
            'headers'   => [
                'product'   => 'Prodotto',
                'type'      => 'Genere',
                'vendor'    => 'Venditore',
                'available' => 'Disponibile per glovo',


            ]
        ],
        'filters'       => [
            'apply'     => 'Filtra prodotto',
            'remove'    => 'Filtro pulito'
        ]

    ],


    'preparationform' => [
        'modal'         => [
            'title'     => 'Tempo stimato di preparazione'

        ],

        'enable'        => [
            'label'     => 'Abilita questo prodotto per la consegna',
            'desc'      => 'Utilizzare questa opzione per abilitare la consegna di questo prodotto tramite il servizio di consegna Glovo',

        ],

        'availability'  => [
            'label'     => 'Subito',
            'no'        => 'Non disponibile'

        ],


        'preparation'   => [
            'label'     => 'Tempo stimato di preparazione',
            'desc'      => 'Seleziona il tempo stimato di preparazione del prodotto. Sarà utilizzato per la disponibilità di orari. <br> <br> Ad esempio, se il negozio chiude alle 22:00 e il prodotto impiega 30 minuti. L\'orario di servizio massimo sarà 09:30 PM',
            'labeldelete'=> 'Elimina tempo stimato'
        ],

        'save'          => [
            'success'   => 'Le informazioni sul prodotto sono state salvate',
            'error'     => 'Si è verificato un errore durante il salvataggio delle informazioni sul prodotto'
        ],
        'delete'          => [
            'success'   => 'Le informazioni sul prodotto sono state salvate',
            'error'     => 'Si è verificato un errore durante il salvataggio delle informazioni sul prodotto'
        ]
    ],


    'orders'        => [

        'header'        => [
            'title'     => 'Ordini',
            'subtitle'  => 'Ordini consegnati da glovo'
        ],

        'table'         => [
            'headers'   => [
                'order'     => '#Ordine',
                'date'      => 'Data di consegna',
                'customer'  => 'Cliente',
                'deliveryaddress' => 'Indirizzo di consegna',
                'paid'      => 'Stato del pagamento',
                'glovostatus' => 'Stato Glovo',


            ]
        ]
    ],



    'orderdetail'   => [

        'panel'     => [
            'title' => 'Stato dell\'ordine Glovo'
        ],

        'pickupaddress' => [
           'title'          => 'Indirizzo di ritiro'
        ],
        'contact' => [
           'title'          => 'Indirizzo di contatto'
        ],
        'destination' => [
            'title'         => 'Indirizzo di consegna'
        ],
        'viewmap' => [
            'title'         => 'Guarda la mappa'
        ],


        'glovostatus'       => [
            'state'         => 'Stato',
            'orderid'       => 'ID ordine',
            'schedule'      => 'Orario',
            'description'   => 'Descrizione',
            'courier'       => 'Nome del corriere',
            'phone'         => 'Telefono',
            'created_at'    => 'Creato a',

            'failedstatus'  => 'Stato fallito',
            'failedmessage' => 'Messaggio fallito',
            'retry'         => 'Prova a inviare nuovamente',
            'checklink'     => 'Visita Glovo Business'
        ],

        'nocreate'          => [
            'label'         => 'L\'ordine non è stato ancora creato in Glovo. Fai clic sul pulsante "Crea ordine" per crearlo subito',
            'buttom'        => 'Crea ordine glovo'
        ],
        'create'            => [
            'success'       => "Ordine creato con successo",
            'fail'          => "Si è verificato un errore durante l'elaborazione dell'ordine. Con messaggio: "
        ],

        'resend'            => [
            'success'       => "L'ordine è stato elaborato correttamente.",
            'fail'          => "Si è verificato un errore durante l'elaborazione dell'ordine. Con messaggio: "
        ],

        'delivery'          => [
            'scheduled'     => 'L\'ordine doveva essere consegnato alle',
            'immmediately'  => 'L\'ordine verrà inviato',
            'posibility'    => 'L\'ordine può essere inviato tramite Glovo Delivery. Per inviarlo manualmente, fai clic su Invia'
        ]
    ],

    'mailsend'          => [
        'ordertitle'        => 'Ordine',
        'hello'             => 'Ciao',
        'preparing'         => 'Stiamo preparando il tuo ordine per ritirarlo. Qui puoi vedere il tracciamento dell\'ordine',
        'thanks'            => 'Grazie per il tuo acquisto',
        'track_title'       => 'Rintraccia il tuo ordine',
        'visit_store'       => 'Visita il nostro negozio',
        'customer_info'     => 'Informazioni per il cliente',
        'address_shipping'  => 'indirizzo di spedizione',
        'address_delivery'  => 'Indirizzo di consegna',
        'shipping'          => 'Metodo di spedizione',
        'payment'           => 'Metodo di pagamento',
        'payment_end'       => 'Finisce dentro',
        'emailcontact'      => 'In caso di domande, rispondi a questa email a'
    ],

    'mailtracking'          => [
        'subject'           => 'Traccia il tuo ordine: ',
    ],


    'mailfailed'            => [
        'subject'           => 'Ordine Glovo non riuscito',
        'title'             => 'Qualcosa è andato storto',
        'description'       => 'Si è verificato un errore imprevisto durante il tentativo di elaborare l\'ordine con il guanto. Si prega di inserire l\'applicazione per rivedere il problema',
        'action'            => 'Risolvere il problema'
    ],

    'tracking'              => [
        'ordertitle'        => 'Ordine',
        'thanks'            => 'Grazie',
        'thanks_purchase'   => 'Grazie per il tuo acquisto',
        'order_summary'     => 'Riepilogo dell\'ordine',
        'order_confirm'     => 'Il tuo ordine è confermato',
        'order_tracking'    => 'Il tuo ordine ha iniziato a essere consegnato, monitora la posizione sulla mappa',
        'customer_info'     => 'Informazioni per il cliente',
        'contact_info'      => 'Informazioni sui contatti',
        'address_shipping'  => 'indirizzo di spedizione',
        'address_delivery'  => 'Indirizzo di consegna',
        'shipping_method'   => 'Metodo di spedizione',
        'payment_method'    => 'Metodo di pagamento',
        'payment_end'       => 'Finisce dentro',
        'emailcontact'      => 'In caso di domande, rispondi a questa email a',
        'contactus'         => 'Rimani in contatto con noi',
        'backstore'         => 'Ritorna allo Store',
        'needhelp'          => 'Ho bisogno di aiuto?',
        'summary'            => [
            'summary'       => 'Riepilogo costi',
            'description'   => 'Descrizione',
            'price'         => 'Prezzo',
            'subtotal'      => 'Totale parziale',
            'shipping'      => 'Spedizione',
            'taxes'         => 'Le tasse',
            'total'         => 'Totale',

        ],

        'states'            => [
            'scheduled'     => [
                'title'     => 'In programma',
                'subtitle'  => 'Il tuo ordine inizierà l\'elaborazione fino al'
            ],
            'active'     => [
                'title'     => 'In corso',
                'subtitle'  => 'L\'ordine ha avviato il processo di consegna'
            ],
            'delivered'     => [
                'title'     => 'consegnato',
                'subtitle'  => 'L\'ordine è stato consegnato dal corriere:'
            ],
            'canceled'     => [
                'title'     => 'Annullato',
                'subtitle'  => 'L\'ordine non può essere elaborato ed è stato annullato'
            ]

        ]
    ],

    'mixed'     => [
        'notes'     => "Nota aggiuntiva: "

    ]

];
