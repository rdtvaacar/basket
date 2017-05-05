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
            // adress
            Route::get('/card/adress', 'AcrSepetController@adress');
            Route::post('/card/adress/county', 'AcrSepetController@county_row');
            Route::post('/card/adress/create', 'AcrSepetController@adress_create');

            Route::post('/card/adress/edit', 'AcrSepetController@adress_edit');
            Route::get('/card/adress/edit', 'AcrSepetController@card_adress_edit');

            Route::post('/card/adress/delete', 'AcrSepetController@adress_delete');
            //payment
            Route::get('/card/payment', 'AcrSepetController@payment');
            Route::post('/card/payment', 'AcrSepetController@payment');
            Route::post('/card/payment/havale_eft', 'AcrSepetController@paymet_havale_eft');


            /// admin
            Route::group(['middleware' => ['admin']], function () {
                Route::get('/product/new', 'AcrFtrController@new_product');
                Route::post('/product/search_row', 'AcrFtrController@product_search_row');
                Route::post('/product/add', 'AcrFtrController@add_product');
                Route::post('/product/delete', 'AcrFtrController@delete_product');
                Route::get('/config', 'AcrFtrController@config');

                Route::post('/bank/create', 'AcrFtrController@bank_create');
                Route::post('/bank/edit', 'AcrFtrController@bank_edit');
                Route::post('/bank/delete', 'AcrFtrController@bank_delete');
                Route::post('/bank/active', 'AcrFtrController@active_bank');
                Route::post('/bank/deactive', 'AcrFtrController@deactive_bank');

            });
        });


    });
});