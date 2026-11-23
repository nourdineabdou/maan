<?php

return [

    'title' => 'Contacter l\'administration',
    'subtitle' => 'Posez une question ou signalez un problème, l\'administration vous répondra ici.',
    'admin_title' => 'Messages des membres',

    'new_message' => 'Nouveau message',
    'label_subject' => 'Sujet',
    'label_body' => 'Message',
    'send' => 'Envoyer',
    'reply' => 'Répondre',
    'reply_placeholder' => 'Écrivez votre réponse...',
    'close_thread' => 'Clôturer',
    'close_confirm' => 'Clôturer ce fil de discussion ?',

    'column_subject' => 'Sujet',
    'column_member' => 'Membre',
    'column_status' => 'Statut',
    'column_date' => 'Date',

    'status_open' => 'En attente',
    'status_answered' => 'Répondu',
    'status_closed' => 'Clôturé',

    'filter_all_statuses' => 'Tous les statuts',

    'back_to_list' => 'Retour aux messages',
    'no_messages' => 'Vous n\'avez envoyé aucun message pour le moment.',
    'no_results' => 'Aucun message pour le moment.',
    'no_replies' => 'Aucune réponse pour le moment.',

    'from_member' => 'Membre',
    'from_admin' => 'Administration',

    'flash_sent' => 'Votre message a été envoyé à l\'administration.',
    'flash_reply_sent' => 'Réponse envoyée.',
    'flash_closed' => 'Fil de discussion clôturé.',

    'notification_title' => 'Nouveau message d\'un membre',
    'notification_body' => ':name vous a envoyé un message : ":subject"',
    'reply_notification_title' => 'Réponse de l\'administration',
    'reply_notification_body' => 'L\'administration a répondu à votre message ":subject".',

    'contact_member_title' => 'Contacter ce membre',
    'contact_member_button' => 'Nouveau message',
    'contact_member_hint' => 'Envoyez un message directement à ce membre, il le recevra dans son espace.',
    'contact_about_button' => 'Discuter à ce sujet',
    'related_to_label' => 'Concernant :',
    'new_thread_notification_title' => 'Nouveau message de l\'administration',
    'new_thread_notification_body' => 'L\'administration vous a envoyé un message : ":subject"',

    // Origine du compte et demande manuelle des pièces d'identité (comptes
    // créés depuis l'app iOS, qui ne collecte ni NNI ni carte d'identité).
    'platform_ios' => 'Inscrit via iPhone',
    'platform_android' => 'Inscrit via Android',
    'platform_web' => 'Inscrit via le site web',
    'request_identity_button' => 'Demander NNI et carte d\'identité',
    'request_identity_hint' => 'Informations manquantes : :items. Le bouton pré-remplit le message ci-dessous ; relisez-le puis cliquez sur « Envoyer ».',
    'request_identity_subject' => 'Compléter votre dossier : NNI et carte d\'identité',
    'request_identity_body' => "Bonjour :name,\n\nPour que nous puissions examiner votre demande d'adhésion, merci de compléter les éléments suivants sur notre site web :url (connectez-vous avec les mêmes identifiants que dans l'application) :\n\n:items\n\nMerci,\nL'équipe Maan",
    'request_identity_item_nni' => '• Votre NNI (numéro national d\'identification) : rubrique « Mon profil > Mes informations personnelles »',
    'request_identity_item_front' => '• La photo du RECTO de votre carte d\'identité : rubrique « Mon profil > Mes documents > CNI recto »',
    'request_identity_item_back' => '• La photo du VERSO de votre carte d\'identité : rubrique « Mon profil > Mes documents > CNI verso »',
    'request_identity_short_nni' => 'NNI',
    'request_identity_short_front' => 'CNI recto',
    'request_identity_short_back' => 'CNI verso',
];
