<?php
Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'Acr\Ftr\Controllers', 'prefix' => 'acr/ftr'], function () {
        Route::get('/', 'AcrFtrController@index');
        Route::get('/product', 'AcrFtrController@my_product');
        Route::post('/product/attribute/modal', 'AcrFtrController@attribute_modal');
        Route::post('/product/sepet/create', 'AcrSepetController@create');
        Route::post('/product/sepet/products', 'AcrSepetController@products');
        Route::post('/product/sepet/sepet_adet_guncelle', 'AcrSepetController@sepet_adet_guncelle');
        Route::post('/product/sepet/sepet_lisans_ay_guncelle', 'AcrSepetController@sepet_lisans_ay_guncelle');
        Route::post('/product/sepet/delete', 'AcrSepetController@delete');
        Route::post('/product/sepet/delete_all', 'AcrSepetController@delete_all');

        Route::get('/card/sepet', 'AcrSepetController@card');
        Route::group(['middleware' => ['auth']], function () {

            /// admin
            Route::group(['middleware' => ['admin']], function () {
                Route::get('/product/new', 'AcrFtrController@new_product');
                Route::post('/product/search_row', 'AcrFtrController@product_search_row');
                Route::post('/product/add', 'AcrFtrController@add_product');
                Route::post('/product/delete', 'AcrFtrController@delete_product');

            });
        });


    });
});