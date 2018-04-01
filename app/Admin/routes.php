<?php

use Illuminate\Routing\Router;

app('debugbar')->disable();
Admin::registerAuthRoutes();



Route::group([
    'prefix'        => config('admin.route.prefix'),
//    'prefix'        => '/',
    'namespace'     => config('admin.route.namespace'),
//    'namespace'     => 'App\\Admin\\Controllers',
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'StockController@index');
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
    $router->resource('reward',RewardsController::class);
    $router->resource('refund',RefundController::class);
    $router->resource('balance',BalanceController::class);
    $router->resource('rent',RentController::class);
    $router->resource('client',ClientController::class);
    $router->resource('agent',AgentController::class);
    $router->resource('stock2',StockAdminController::class);
    $router->resource('transfer2',TransferAdminController::class);
    $router->resource('productline',ProductLineController::class);

    $router->get('api/user', 'StockController@user');

    $router->get('api/users',       'TransferController@users');
    $router->get('api/stocks',      'TransferController@stocks');
    $router->get('api/purchases',   'TransferController@purchases');
    $router->get('api/products',    'TransferController@products');
    $router->get('api/ships',       'TransferController@ships');

    $router->get('transfer/list/{id}' ,'TransferController@list');
    $router->get('purchase/list/{id}' ,'PurchaseController@list');
    $router->get('ship/list/{id}' ,'ShipController@list');

    $router->group(['prefix'=>'product_stock'], function (Router $router) {
        $router->get('{transfer_id}/new' ,'TransferController@newline');
        $router->get('{transfer_id}/add' ,'TransferController@newline2');
        $router->get('{transfer_id}/item' ,'TransferController@item');
        $router->get('{transfer_id}/check' ,'TransferController@check');
    });


    $router->group(['prefix'=>'product_transfer'], function (Router $router) {
        $router->post('store' ,'TransferController@storeline2');
    });

    $router->group(['prefix'=>'quickline'], function (Router $router) {
        $router->post('check/{id}' ,'TransferController@checkserial');
        $router->post('query/{id}' ,'TransferController@queryserial');
        $router->post('check/{id}' ,'TransferController@queryserial');
    });


//    $router->group(['prefix'=>'quickline'], function(Router $router) {
//        $router->post('check/{$id}' ,'TransferController@checkserial');
//        $router->get('/check/{$id}' ,function(){
//            return 1;
//        });
//        $router->post('query/{$id}' ,'TransferController@queryserial');
//        $router->post('check/{$id}' ,'TransferController@queryserial');
//    });

    $router->group(['prefix'=>'product_purchase'], function (Router $router) {
        $router->any('{transfer_id}/new' ,'PurchaseController@newline');
        $router->get('{transfer_id}/item' ,'PurchaseController@item');
        $router->get('{transfer_id}/batch' ,'PurchaseController@batch');
        $router->get('{transfer_id}/check' ,'PurchaseController@check');
        $router->post('store' ,'PurchaseController@storeline');
        $router->get('{product_stock_id}/del' ,'PurchaseController@deleteline');
    });
//    $router->post('product_transfer/store' ,'TransferController@storeline2');
//    $router->get('product_purchase/{transfer_id}/new' ,'PurchaseController@newline');

    $router->group(['prefix'=>'document'],function(Router $router){
        $router->get('purchase/{purchase_id}','DocumentController@purchase');
        $router->get('stock/{stock_id}','DocumentController@stock');
        $router->get('ship/{ship_id}','DocumentController@ship');
        $router->get('stockin/{ship_id}','DocumentController@stock_in');
        $router->get('stockout/{ship_id}','DocumentController@stock_out');
    });

    $router->get('product_ship/{transfer_id}/new' ,'ShipController@newline');

    $router->group(['prefix'=>'order'], function (Router $router) {
        $router->get('check/{id}','OrderController@checkOrder');
    });

    $router->get('stock/{id}' ,'StockController@products');

    // $router->post('stock/purchase/{id}' ,'PurchaseController@quickline');
    $router->post('purchase/stock/{id}' ,'PurchaseController@quickline');

    $router->post('product_stock/store' ,'TransferController@storeline');
    $router->post('product_stock/mystore' ,'StockController@storeall');
    $router->post('product_ship/store' ,'ShipController@storeline');


    $router->get('test','StockController@test');


    $router->get('/{any}', 'SpaController@index')->where('any', '.*');

});
