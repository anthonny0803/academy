<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Central Domains
    |--------------------------------------------------------------------------
    |
    | Hosts that serve the central application (panel, login, sign-up) and do
    | NOT belong to any tenant. The tenant resolver treats any host outside
    | this list as "{slug}.{central_domain}" and extracts the leftmost label
    | as the tenant subdomain. Requests on a central domain resolve no tenant,
    | leaving the global scope inert.
    |
    */

    'central_domains' => array_values(array_filter([
        env('TENANCY_CENTRAL_DOMAIN', parse_url((string) env('APP_URL'), PHP_URL_HOST)),
    ])),

];
