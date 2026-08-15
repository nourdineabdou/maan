<?php

return [

    /**
     * Interrupteur général des notifications push (mobile + navigateur).
     * Volontairement piloté par .env et non par une option visible dans le
     * dashboard admin : c'est une fonctionnalité activable uniquement par
     * l'exploitant de la plateforme (accès serveur), pas par le client. Les
     * notifications internes (cloche dans l'app) ne sont jamais affectées.
     */
    'enabled' => env('PUSH_NOTIFICATIONS_ENABLED', false),

];
