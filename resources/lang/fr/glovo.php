<?php

return [
    'storefront' => [
        'template' => [
            'products' => [
                'available' => 'Ce produit est disponible à la livraison par ',
                'choosewhen'=> 'Choisissez dans le panier quand vous voulez le recevoir'
            ],
            'cart'      => [
                'loading'=> 'Trouver les délais de livraison disponibles ..',
                'estimated'=> [
                    'hour'  => 'La commande prendra environ <b> {estimate_hours}: {estimate_min} </b> heures pour être préparée.',
                    'minute'=>'La préparation prendra environ <b> {estimate_time} </b> min.',
                    'immediately'=>'<i> La commande est disponible immédiatement pour la livraison </i>'
                ],
                'available'=> 'Un ou plusieurs articles de votre panier sont disponibles pour le service de livraison de Glovo.',
                'noallowscheduled'=> 'Choisissez glovo dans l’étape suivante pour recevoir votre commande!.',
                'when' => [
                    'label' => 'Quand souhaitez-vous recevoir votre commande?',
                    'question'=>'Choisissez quand vous voulez recevoir votre commande.',
                    'assoon'  => 'Envoyer dès que possible'
                ],
                'day'   => 'Journée',
                'hour'  => 'Heure'
            ]
        ],

        'shipping' => [
            'rate'      => '(Cochez le panier pour choisir la date / heure de livraison)'
        ]
    ],

    'welcome'   => [

        'gettinstarted' => "Guide de Démarrage",
        'p1'            => "Suivez ces étapes pour activer la livraison Shopify Glovo",
        'l1'            => [
            't1'        =>  "Modifier",
            't2'        =>  "Après l'installation de l'application, un extrait de liquide appelé",
            't3'        =>  "est installé dans votre thème de boutique actuel. Ce fragment de code est chargé d’afficher les paramètres de livraison sur la page du panier.",
            't4'        =>  "Pour activer l'extrait, ouvrez le <a target=\"blank\" href=\"https://docs.shopify.com/manual/configuration/store-customization/#template-editor\">Theme Editor</a> dans votre magasin, puis ouvrez <code class=\"code_span\">Templates/cart.liquid</code> et ajouter <code class=\"code_span\">{% include 'snippet-glovo-delivery-cart' %}</code> entre l'ouverture <code class=\"code_span\">&lt;form&gt;</code> et la fermeture <code class=\"code_span\">&lt;/form&gt;</code> Mots clés.",
            't5'        =>  "Le placement exact entre ces balises n’est pas critique, mais une bonne place est immédiatement au-dessus des notes du panier ou des instructions spéciales. (<code class=\"code_span\">{% if settings.show_cart_notes %}</code> <strong>ou</strong> <code class=\"code_span\">{% if settings.special_instructions %}</code> <strong>ou</strong> <code class=\"code_span\">{% if settings.additional_informaiton %}</code>).",
            't6'        =>  "Par exemple:",
            't7'        =>  "N'oubliez pas de sauvegarder les modifications lorsque vous avez terminé.",
        ],

        'l2'            => [
            't1'        => 'Aller au menu des paramètres',
            't2'        => [
                'title' => "La première chose à configurer est l’APIS pour se connecter à l’API Glovo et à Google Maps. Par exemple, fournissez les clés de l’API Glove. <a target=\"blank\" href=\"https://business.glovoapp.com/login\">Glovo Bussiness</a>",
                "helps"     => [
                    'c1'    => '<b>Activer l\'expédition: </b> Activer le service de livraison en magasin à l\'aide du service Glovo',
                    'c2'    => '<b>Langue: </b> choisissez la langue de votre magasin. Par défaut (langue Frontstore)',
                    'c3'    => '<b>API Google Maps: </b> Fournissez l’API Google Maps, l’application nécessite cette API pour la géolocalisation des adresses. Procurez-vous une <a href="https://developers.google.com/maps/documentation/embed/get-api-key"> développeurs Google </a> api.',
                    'c4'    => '<b>Glovo Business API: </b> Fournir des API Glovo, l\'application nécessite cette application pour passer des commandes. Obtenez une api <a href="https://business.glovoapp.com"> Glovo Business </a>.',
                    'c5'    => '<b>Titre </b> est le nom qui apparaîtra dans la boutique pour identifier le service glovo.',
                    'c6'    => '<b>Coût </b> Choisissez le mode de facturation du coût du service. 1.- Gratuit, 2.- Calculé par l\'api de glovo en utilisant la géolocalisation. 3.- Prix fixe',
                    'c7'    => '<b>Planifier les envois </b> Cette option permet à l\'utilisateur de faire la programmation pour recevoir sa commande. Sinon, il essaiera d\'envoyer immédiatement',
                    'c8'    => '<b>Quand créer la commande </b> Choisissez le moment où vous souhaitez passer la commande glovo, 1.- Paiement autorisé, 2.- Commande payée, 3.- Manuel',
                ],
            ],

            't3'            => [
                'title'     => "Déterminez le lieu, les jours et les heures de travail du magasin dans lesquels le service de livraison de Glovo sera disponible.",
                "helps"     => [
                    'c1'    => '<b>Emplacement du magasin: </b> Configurez l’emplacement du magasin dans la zone de couverture de Glovo. Voir les villes avec une couverture <a href="https://glovoapp.com/fr/map"> Carte de couverture </a>',
                    'c2'    => '<b>Jours de service: </b> Configurez les jours et les heures de service pour la commande de glovo.',
                    'c3'    => '<b>Vacances: </b> Fournissez des vacances, le service ne sera pas disponible.',
                ],
            ],

            'l4' => [
                't1'        => "Le service est activé dans le modèle de produit",
                't2'        => "Dans le magasin, les informations sur le produit semblent avoir été envoyées par le service Glovo",
            ],

            'l5' => [
                't1'        => "Choisissez quand vous voulez recevoir votre commande",
                't2'        => "Si le paramètre de calendrier de livraison est activé, l’acheteur peut choisir quand il recevra sa commande. Sinon, il sera envoyé dès que possible",
            ]
        ],

        'l3'            => [
            't1'        => "Choisissez les produits qui peuvent être envoyés par glovo",
            't2'        => "Configurer la disponibilité des produits à livrer avec le service Glovo",
            't3'        => "Définissez le temps estimé de préparation des produits disponibles. Pour pouvoir vous proposer le calendrier de livraison lors de la vérification du panier"

        ]
    ],

    'general' => [
        'section'       => 'Paramètres Glovo',
        'subsection'    => 'Définir les paramètres de base',
        'basic'         => 'Paramètres de base',

    ],

    'enable'=> [
        'label'=> 'Activer l\'expédition Glovo',
        'desc' => 'Voulez-vous activer Glovo Delivering?'
    ],

    'language'=> [
        'label'=> 'Choisissez une langue',
        'desc' => 'Définir la langue principale du magasin'
    ],

    'server'=> [
        'label'=> 'Serveur',
        'desc' => 'Utiliser le serveur de production ou de test'
    ],




    'glovoapi'=> [
        'label'=> 'Clé API Glovo',
        'tip'   => 'Clé API fournie par glovo',
        'desc' => 'Obtenez votre clé api sur le site glovo <a target="_blank" href="https://business.glovoapp.com"> https://business.glovoapp.com </a>'
    ],

    'glovosecret'=> [
        'label'=> 'Glovo API Secret',
        'desc' => 'API Secret fourni par glovo'
    ],

    'googleapi'=> [
        'label'=> 'Clé API Google Maps',
        'tip' => 'Clé API fournie par google maps',
        'desc' => 'Clé PI fournie par google maps. Obtenez votre apikey sur place <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key"> https://developers.google.com/maps </a>'
    ],


    'method' => [
        'label'=> 'Titre de la méthode',
        'desc' => 'Titre du mode de livraison d’expédition de glovo. Comment le client verra le titre lors du paiement'
    ],

    'cost' => [
        'label'=> 'Coût de livraison',
        'desc' => 'Choisissez le moyen de calculer le taux d\'expédition. <br> <br> * Notez que Shopify a un cache pour éviter les demandes multiples. La mise à jour des taux peut prendre quelques minutes. Si vous modifiez un produit dans le panier, il sera mis à jour immédiatement',
        'types' => [
            'free'  => "Libre",
            'calculate'=> "Calculé par l'API Glovo",
            'fixed' => "Basé sur un prix fixe",
        ]

    ],

    'allowscheduled'    => [
        'label'     => 'Autoriser le calendrier des envois',
        'desc'      => 'Actif: Permet à l’acheteur de choisir l’heure / le jour pour planifier l’envoi. <br> Désactiver: les commandes seront passées dès que possible.'
    ],

    'createorderstatus'    => [
        'label'     => 'Créer une commande lorsque le statut est',
        'desc'      => 'Choisissez l\'état que la commande Shopify doit remplir pour exécuter la commande glovo'
    ],

    'locations' => [
        'section'       => 'Configurer les adresses de livraison',
        'subsection'    => 'définir l\'emplacement principal',
        'coverage'      => 'Le magasin doit être situé dans la zone de service de glovo. Voir les villes avec une couverture <a href="https://glovoapp.com/fr/map"> Carte de couverture. </a>',
        'primary'       => 'Pour modifier les emplacements, allez à Configuration -> Emplacements'

    ],


    'address'   => [
        'enable'=> [
            'label'=> 'Activer la livraison de Glovo',
            'desc' => 'Voulez-vous activer Glovo Delivering pour le magasin?'
        ],
        'lat'           => 'Lieu Lat',
        'lng'           => 'Lieu Lng',
        'city'          => 'Nom de Ville',
        'storename'     => 'Nom du magasin',
        'address1'      => 'Adresse 1',
        'address2'      => 'Adresse 2',
        'postcode'      => 'Code postal',
        'phone'         => 'Numéro du contact',
        'country'       => 'Nom du pays',
        'province'      => 'Province',

    ],

    'workinghours'   => [
        'section'       => 'Heures de service',
        'subsection'    => 'Définir les jours et les horaires de travail dans lesquels le magasin fournit le service de livraison',
        'days'          => [
            '0'    => "Dimanche" ,
            '1'    => "Lundi" ,
            '2'    => "Mardi" ,
            '3'    => "Mercredi" ,
            '4'    => "Jeudi" ,
            '5'    => "Vendredi" ,
            '6'    => "Samedi"
        ],
        'today'     => 'Aujourd\'hui',
        'tomorrow'  => 'Demain'
    ],


    'holidays'   => [
        'section'       => 'Jours fériés',
        'subsection'    => 'Définir des fériés. En ces jours il n\'y aura pas de service de livraison',
        'label'         => 'Définir les fériés',
        'sublabel'      => 'Ajoutez les jours où le service glovo ne sera pas disponible',
        'addbuttom'     => 'Ajouter les fériés',
        'deletebuttom'  => 'Effacer'
    ],

    'months'    => [
        '01'    => 'Janvier',
        '02'    => 'Février',
        '03'    => 'Mars',
        '04'    => 'Avril',
        '05'    => 'Peut',
        '06'    => 'Juin',
        '07'    => 'Juillet',
        '08'    => 'D\'aout',
        '09'    => 'Septembre',
        '10'    => 'Octobre',
        '11'    => 'Novembre',
        '12'    => 'Décembre',
    ],


    'buttons'       => [
        'save'      => 'Sauvegarder',
        'delete'    => 'Effacer',
        'cancel'    => 'Annuler',
        'test'      => 'Tester la connexion'
    ],

    'settings' => [
        'save' => [
            'success'   => 'La configuration a été sauvegardée',
            'failed'    => 'Un problème est survenu lors de l\'enregistrement de la configuration'
        ],
        'messages'      => [
            'required'  => 'Ce champ est requis.'
        ]
    ],

    'validated'         => [
        'title'         => 'Impossible de valider l\'adresse du magasin. Assurez-vous que les informations d\'identification de glovo et de google sont correctes. Le magasin doit être dans la zone de service de glovo. Vérifiez l\'adresse du magasin.',
    ],

    'validatedplan'         => [
        'noallow'           => 'Votre plan de magasin vous empêche de calculer les tarifs calculés, afin d\'utiliser cette fonctionnalité lors du paiement, vous devez avoir un plan avancé ou un plan annuel. Cependant, les commandes créées peuvent être envoyées individuellement à glovo pour livraison au client.'
    ],


    'products'          => [

        'header'        => [
            'title'     => 'Des produits',
            'subtitle'  => 'Permet la livraison des produits par le service de livraison de Glovo'
        ],
        'table'         => [
            'headers'   => [
                'product'   => 'Produit',
                'type'      => 'Type',
                'vendor'    => 'Vendeur',
                'available' => 'Disponible pour glovo',


            ]
        ],
        'filters'       => [
            'apply'     => 'Filtrer le produit',
            'remove'    => 'Filtre filtrant'
        ]

    ],


    'preparationform' => [
        'modal'         => [
            'title'     => 'Temps de préparation estimé'

        ],

        'enable'        => [
            'label'     => 'Activer ce produit pour la livraison',
            'desc'      => 'Utilisez cette option pour activer la livraison de ce produit par le service de livraison Glovo.',

        ],

        'availability'  => [
            'label'     => 'Immédiatement',
            'no'        => 'Indisponible'

        ],


        'preparation'   => [
            'label'     => 'Temps de préparation estimé',
            'desc'      => 'Sélectionnez le temps estimé de préparation du produit. Il sera utilisé pour la disponibilité des horaires. <br> <br> Par exemple, si le magasin ferme à 22h00 et que le produit prend 30 minutes. L’heure de service maximum sera 21h30.',
            'labeldelete'=> 'Supprimer le temps estimé'
        ],

        'save'          => [
            'success'   => 'Les informations sur le produit ont été sauvegardées',
            'error'     => 'Une erreur s\'est produite lors de l\'enregistrement des informations sur le produit.'
        ],
        'delete'          => [
            'success'   => 'Les informations sur le produit ont été sauvegardées',
            'error'     => 'Une erreur s\'est produite lors de l\'enregistrement des informations sur le produit.'
        ]
    ],


    'orders'        => [

        'header'        => [
            'title'     => 'Ordres',
            'subtitle'  => 'Les commandes livrées par glovo'
        ],

        'table'         => [
            'headers'   => [
                'order'     => '#Ordre',
                'date'      => 'Date de livraison',
                'customer'  => 'Client',
                'deliveryaddress' => 'Adresse de livraison',
                'paid'      => 'Statut de paiement',
                'glovostatus' => 'Statut Glovo',


            ]
        ]
    ],



    'orderdetail'   => [

        'panel'     => [
            'title' => 'Statut de la commande Glovo'
        ],

        'pickupaddress' => [
           'title'          => 'Adresse de ramassage'
        ],
        'contact' => [
           'title'          => 'Adresse de contact'
        ],
        'destination' => [
            'title'         => 'Adresse de livraison'
        ],
        'viewmap' => [
            'title'         => 'Voir la carte'
        ],


        'glovostatus'       => [
            'state'         => 'Etat',
            'orderid'       => 'Numéro de commande',
            'schedule'      => 'Horaire',
            'description'   => 'La description',
            'courier'       => 'Nom du courrier',
            'phone'         => 'Téléphone',
            'created_at'    => 'Créé à',

            'failedstatus'  => 'Statut échoué',
            'failedmessage' => 'Message échoué',
            'retry'         => 'Essayer de renvoyer',
            'checklink'     => 'Visiter Glovo Business'
        ],

        'nocreate'          => [
            'label'         => 'La commande n\'a pas encore été créée à Glovo. Cliquez sur le bouton "Créer une commande" pour le créer tout de suite',
            'buttom'        => 'Créer une commande glovo'
        ],
        'create'            => [
            'success'       => "Commande créée avec succès",
            'fail'          => "Une erreur s'est produite lors du traitement de la commande. Avec message: "
        ],

        'resend'            => [
            'success'       => "La commande a été traitée correctement.",
            'fail'          => "Une erreur s'est produite lors du traitement de la commande. Avec message: "
        ],

        'delivery'          => [
            'scheduled'     => 'La commande devait être livrée à',
            'immmediately'  => 'La commande sera envoyée',
            'posibility'    => 'La commande peut être envoyée via Glovo Delivery. Pour l\'envoyer manuellement, cliquez sur envoyer'
        ]

    ],

    'mailsend'          => [
        'ordertitle'        => 'Ordre',
        'hello'             => 'Bonjour',
        'preparing'         => 'Nous préparons votre commande pour la récupérer. Ici vous pouvez voir le suivi de commande',
        'thanks'            => 'Merci pour votre achat',
        'track_title'       => 'Suivre votre commande',
        'visit_store'       => 'Visitez notre magasin',
        'customer_info'     => 'Informations client',
        'address_shipping'  => 'Adresse de livraison',
        'address_delivery'  => 'Adresse de livraison',
        'shipping'          => 'Méthode d\'envoi',
        'payment'           => 'Mode de paiement',
        'payment_end'       => 'Fini dans',
        'emailcontact'      => 'Si vous avez des questions, répondez à cet email à'
    ],

    'mailtracking'          => [
        'subject'           => 'Suivre votre commande: ',
    ],

    'mailfailed'            => [
        'subject'           => 'Impossible de traiter la commande Glovo',
        'title'             => 'Quelque chose a mal tourné',
        'description'       => 'Une erreur inattendue s\'est produite lors de la tentative de traitement de la commande dans le gant. S\'il vous plaît entrer l\'application pour examiner le problème',
        'action'            => 'Résoudre le problème'
    ],

    'tracking'              => [
        'ordertitle'        => 'Ordre',
        'thanks'            => 'Merci',
        'thanks_purchase'   => 'Merci pour votre achat',
        'order_summary'     => 'Récapitulatif de la commande',
        'order_confirm'     => 'Votre commande est confirmée',
        'order_tracking'    => 'Votre commande a commencé à être livrée, surveille l\'emplacement sur la carte',
        'customer_info'     => 'Informations client',
        'contact_info'      => 'Informations de contact',
        'address_shipping'  => 'Adresse de livraison',
        'address_delivery'  => 'Adresse de livraison',
        'shipping_method'   => 'Méthode d\'envoi',
        'payment_method'    => 'Mode de paiement',
        'payment_end'       => 'Fini dans',
        'emailcontact'      => 'Si vous avez des questions, répondez à cet email à',
        'contactus'         => 'Prenez contact avec nous',
        'backstore'         => 'Retour au magasin',
        'needhelp'          => 'Besoin d\'aide pour?',
        'summary'            => [
            'summary'       => 'Résumé des coûts',
            'description'   => 'La description',
            'price'         => 'Prix',
            'subtotal'      => 'Subtotal',
            'shipping'      => 'Livraison',
            'taxes'         => 'Les taxes',
            'total'         => 'Total',

        ],

        'states'            => [
            'scheduled'     => [
                'title'     => 'Livraison prévue',
                'subtitle'  => 'Votre commande commencera à traiter jusqu\'au'
            ],
            'active'     => [
                'title'     => 'En cours',
                'subtitle'  => 'La commande a commencé le processus de livraison'
            ],
            'delivered'     => [
                'title'     => 'Livré',
                'subtitle'  => 'La commande a été livrée, par le messager:'
            ],
            'canceled'     => [
                'title'     => 'Annulé',
                'subtitle'  => 'La commande ne peut pas être traitée et a été annulée'
            ]

        ]

    ],

    'mixed'     => [
        'notes'     => "Remarque additionnelle: "

    ]

];
