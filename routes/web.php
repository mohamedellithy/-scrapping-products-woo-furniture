<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/link', function () {
    // $target = '/home/public_html/storage/app/public';
    // $shortcut = '/home/public_html/public/storage';
    // ln -s ../storage/app/products_content products_content
    // mklink /D storage ..\storage\app\public
    // $target = 'C:/wamp64/www/laravel/scrapping-woocommerce/Laravel-scrapping-products-woocommerce/storage/app/public';
    // $shortcut = 'C:/wamp64/www/laravel/scrapping-woocommerce/Laravel-scrapping-products-woocommerce/public/storage';
    // symlink($target, $shortcut);
 });

$router->get('/test','ExampleController@testDb');

// mklink /D ..\..\..\wordpress\wordpress\wp-content\uploads\2022 storage\app\public
