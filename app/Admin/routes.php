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

//    $router->get('/', 'StockController@index');
    $router->get('/', 'HomeController@index');
    $router->resource('users', UserController::class);
    $router->resource('stocks', StockController::class);
    $router->resource('products',ProductController::class);
    $router->resource('scm/ship',ShipController::class);
    $router->resource('transfer',TransferController::class);
    $router->resource('purchase',PurchaseController::class);
    $router->resource('order',OrderController::class);
    $router->resource('order2',OrderSimpleController::class);
    $router->resource('ship',ShipController::class);
    $router->resource('scm/recycle',RecycleController::class);
    $router->resource('scm/lend',LendController::class);
    $router->resource('scm/repair',RepairController::class);
    $router->resource('vendors',VendorController::class);
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
    $router->resource('staff',UserController::class);



    $router->get('transfer/list/{id}' ,'TransferController@list');
    $router->get('purchase/list/{id}' ,'PurchaseController@list');
    $router->get('ship/list/{id}' ,'ShipController@list');

    $router->group(['prefix'=>'product_stock'], function (Router $router) {
        $router->get('{transfer_id}/new' ,'TransferController@newline');
        $router->get('{transfer_id}/add' ,'TransferController@newline2');
        $router->get('{transfer_id}/item' ,'TransferController@item');
        $router->get('{transfer_id}/check' ,'TransferController@check');
    });

    $router->group(['prefix'=>'transfer'], function (Router $router) {
        $router->get('{stock_id}/{any}' ,function($stock){
            return view('transfer.create',['title'=>$stock]);
        })->where('any', '.*');
        $router->post('{stock_id}/purchase' ,'TransferController@postPurchase');
        $router->post('{stock_id}/trans' ,'TransferController@postTrans');
        $router->post('{stock_id}/ship' ,'TransferController@postShip');
        $router->post('{stock_id}/lend' ,'TransferController@postLend');
//        $router->get('{transfer_id}/add' ,'TransferController@newline2');
//        $router->get('{transfer_id}/item' ,'TransferController@item');
//        $router->get('{transfer_id}/check' ,'TransferController@check');
    });

    $router->group(['prefix'=>'api'], function (Router $router) {
//        仓库详情
        $router->get('stock/{stock_id}' ,'StockController@api_show');
        $router->get('stocklist' ,'StockController@list');
//        商品列表
        $router->get('productlist','ProductController@list');
        $router->get('productlist/{stock_id}','ProductController@stocklist');
//        产品详情
        $router->get('product/{id}','ProductController@api_show');
        $router->get('product/{id}/detail','ProductController@api_stock');
        $router->get('product/{id}/count','ProductController@api_count');
//        判断序列号已存在
        $router->get('serialExist','SerialController@serial_dup');

        $router->get('userlist','UserController@list');

        $router->get('orderlist','OrderController@list');
        $router->get('contractlist','ContractController@list');

        $router->post('contract','ContractController@store');


        $router->get('clientlist','ClientController@list');
        $router->get('agentlist','AgentController@list');

        $router->get('lendlist/{stock_id}','TransferController@lendlist');


        $router->get('user', 'StockController@user');

        $router->get('users',       'TransferController@users');
        $router->get('stocks',      'TransferController@stocks');
        $router->get('purchases',   'TransferController@purchases');
        $router->get('products',    'TransferController@products');
        $router->get('ships',       'TransferController@ships');
    });

    $router->group(['prefix'=>'product_transfer'], function (Router $router) {
        $router->post('store' ,'TransferController@storeline2');
    });

    $router->group(['prefix'=>'quickline'], function (Router $router) {
        $router->post('check/{id}' ,'TransferController@checkserial');
        $router->post('query/{id}' ,'TransferController@queryserial');
        $router->get('{id}','TransferController@queryserial');
        $router->get('/','TransferController@updateSerialTransfer');
//        $router->post('check/{id}' ,'TransferController@queryserial');
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

    $router->get('report','SupplyReportController@import');
    $router->get('report/table', 'SupplyReportController@table');


    $router->get('/{any}', 'SpaController@index')->where('any', '.*');


});
