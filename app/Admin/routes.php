<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('users', UserController::class);
    $router->resource('stocks', StockController::class);
    $router->resource('products',ProductController::class);
    $router->resource('scm/ship',ShipController::class);
    $router->resource('transfer',TransferController::class);
    $router->resource('purchase',PurchaseController::class);
    $router->resource('order',OrderController::class);
    $router->resource('ship',ShipController::class);
    $router->resource('scm/recycle',RecycleController::class);
    $router->resource('scm/lend',LendController::class);
    $router->resource('scm/repair',RepairController::class);
    $router->resource('vendor',VendorController::class);
    $router->resource('serials',SerialController::class);

    $router->get('api/user', 'StockController@user');

    $router->get('api/users',       'TransferController@users');
    $router->get('api/stocks',      'TransferController@stocks');
    $router->get('api/purchases',   'TransferController@purchases');
    $router->get('api/products',    'TransferController@products');
    $router->get('api/ships',       'TransferController@ships');

    $router->get('transfer/list/{id}' ,'TransferController@list');
    $router->get('purchase/list/{id}' ,'PurchaseController@list');
    $router->get('ship/list/{id}' ,'ShipController@list');

    $router->get('product_stock/{transfer_id}/new' ,'TransferController@newline');
    $router->get('product_purchase/{transfer_id}/new' ,'PurchaseController@newline');
    $router->get('product_ship/{transfer_id}/new' ,'ShipController@newline');


    $router->get('stock/{id}' ,'StockController@products');

    // $router->post('stock/purchase/{id}' ,'PurchaseController@quickline');
    $router->post('purchase/stock/{id}' ,'PurchaseController@quickline');

    $router->post('product_stock/store' ,'TransferController@storeline');
    $router->post('product_purchase/store' ,'PurchaseController@storeline');
    $router->post('product_ship/store' ,'ShipController@storeline');

});
