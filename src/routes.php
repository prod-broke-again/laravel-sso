<?php

use Packages\LaravelSSO\Http\Controllers\SSOController;
use Illuminate\Support\Facades\Route;

// SSO Routes
Route::prefix(config('sso.routes.prefix', 'sso'))
    ->middleware(config('sso.routes.middleware', ['web']))
    ->group(function () {

        // SSO Login page
        Route::get('/login', [SSOController::class, 'login'])
            ->name('sso.login');

        // Redirect to partner
        Route::get('/redirect/{partnerIdentifier}', [SSOController::class, 'redirectToPartner'])
            ->name('sso.redirect')
            ->middleware('auth');

        // SSO Callback
        Route::get('/callback', [SSOController::class, 'callback'])
            ->name('sso.callback');

        // Handle incoming SSO requests (optional)
        Route::get('/incoming', [SSOController::class, 'handleIncoming'])
            ->name('sso.incoming');
    });
