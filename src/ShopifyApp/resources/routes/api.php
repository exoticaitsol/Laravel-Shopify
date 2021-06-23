<?php

use Illuminate\Support\Facades\Route;
use Osiset\ShopifyApp\Helpers;

// Check if manual routes override is to be use
$manualRoutes = Helpers::getShopifyConfig('manual_routes');

if ($manualRoutes) {
    // Get a list of route names to exclude
    $manualRoutes = explode(',', $manualRoutes);
}

Route::group(['middleware' => ['api']], function () use ($manualRoutes) {
    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Exposes endpoints for the current user data, and all plans.
    |
    */

    if (Helpers::registerPackageRoute('api', $manualRoutes)) {
        Route::group(['prefix' => 'api', 'middleware' => ['auth.token']], function () {
            Route::get(
                '/',
                'Osiset\ShopifyApp\Http\Controllers\ApiController@index'
            );

            Route::get(
                '/me',
                'Osiset\ShopifyApp\Http\Controllers\ApiController@getSelf'
            );

            Route::get(
                '/plans',
                'Osiset\ShopifyApp\Http\Controllers\ApiController@getPlans'
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Webhook Handler
    |--------------------------------------------------------------------------
    |
    | Handles incoming webhooks.
    |
    */

    if (Helpers::registerPackageRoute('webhook', $manualRoutes)) {
        Route::post(
            '/webhook/{type}',
            'Osiset\ShopifyApp\Http\Controllers\WebhookController@handle'
        )
        ->middleware('auth.webhook')
        ->name(Helpers::getShopifyConfig('route_names.webhook'));
    }
});
