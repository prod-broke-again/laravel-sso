<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SSO Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the Laravel SSO package.
    | Here you can configure your partner applications and SSO settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Partner Applications
    |--------------------------------------------------------------------------
    |
    | Define your partner applications here. Each partner should have:
    | - name: Display name for the partner
    | - url: Base URL of the partner application
    | - key: Shared secret key for token encryption/decryption
    | - enabled: Whether SSO with this partner is enabled
    |
    */
    'partners' => [
        // Example partner configuration:
        // 'partner1' => [
        //     'name' => 'Partner Application 1',
        //     'url' => 'https://partner1.example.com',
        //     'key' => env('SSO_PARTNER1_KEY'),
        //     'enabled' => env('SSO_PARTNER1_ENABLED', true),
        // ],
        // 'partner2' => [
        //     'name' => 'Partner Application 2',
        //     'url' => 'https://partner2.example.com',
        //     'key' => env('SSO_PARTNER2_KEY'),
        //     'enabled' => env('SSO_PARTNER2_ENABLED', true),
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Current Application Identifier
    |--------------------------------------------------------------------------
    |
    | Unique identifier for this application. This should be unique across
    | all partner applications.
    |
    */
    'app_identifier' => env('SSO_APP_IDENTIFIER', 'app1'),

    /*
    |--------------------------------------------------------------------------
    | Token Lifetime
    |--------------------------------------------------------------------------
    |
    | How long SSO tokens should be valid (in minutes).
    |
    */
    'token_lifetime' => env('SSO_TOKEN_LIFETIME', 5),

    /*
    |--------------------------------------------------------------------------
    | Redirect After Login
    |--------------------------------------------------------------------------
    |
    | Where to redirect users after successful SSO login.
    |
    */
    'redirect_after_login' => env('SSO_REDIRECT_AFTER_LOGIN', '/dashboard'),

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The user model class to use for authentication.
    |
    */
    'user_model' => env('SSO_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Database Tables
    |--------------------------------------------------------------------------
    |
    | Table names for SSO functionality.
    |
    */
    'tables' => [
        'partners' => 'sso_partners',
        'tokens' => 'sso_tokens',
    ],

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes for SSO functionality.
    |
    */
    'routes' => [
        'prefix' => 'sso',
        'middleware' => ['web'],
    ],
];
