<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // The SBK app's notifications (App\Services\App\AppPush): Firebase project and
    // the path of its service-account key, a file outside the web root.
    'fcm' => [
        'project' => env('FCM_PROJECT', 'sbk-chat'),
        'service_account' => env('FCM_SERVICE_ACCOUNT'),
    ],

    'paypal' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'client_id' => env('PAYPAL_CLIENT_ID', env('PAYPAL_SANDBOX_CLIENT_ID')),
        'secret' => env('PAYPAL_SECRET', env('PAYPAL_SANDBOX_CLIENT_SECRET')),
    ],

    // The SBK CRM. This website signs the "CRM Global" sign-in links and its
    // reads of the CRM's customer records with an RSA private key; the CRM
    // only holds the public half. See docs/CRM-INTEGRATION.md.
    'crm' => [
        'url' => rtrim(env('CRM_URL', 'https://crmglobal.sbkautotrading.com'), '/'),
        'private_key' => env('CRM_PRIVATE_KEY'),            // base64 of the PEM (one line), or the PEM
        'private_key_path' => env('CRM_PRIVATE_KEY_PATH'),  // or a path to the PEM file
        'timeout' => (int) env('CRM_TIMEOUT', 10),
        'cache_seconds' => (int) env('CRM_CACHE_SECONDS', 60),
    ],

    // The auction portal: its "Auction" sign-in is signed with the CRM key above
    // (the auction holds the public half, in its sso.php).
    'auction' => [
        'url' => rtrim(env('AUCTION_URL', 'https://auction.sbkautotrading.com'), '/'),
    ],

];
