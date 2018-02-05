<?php

/*
|--------------------------------------------------------------------------
| Application Routes  	路由
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('admin/index/index', 'Admin\IndexController@index');

/*	模块/控制器/方法 三级

//Admin 模块
Route::group(['namespace'=>'Admin','prefix'=>'admin','middleware'=>'admin.auth'],function(){
	//文章管理
	Route::group(['prefix'=>'articlt'],function(){
		//文章列表
		Route::get('index', 'ArticltController@index');
		//文章发布
		Route::get('create', 'ArticltController@create');
	});

	//分类管理
	Route::group(['prefix'=>'category'],function(){
		//分类列表
		Route::get('index', 'CategoryController@index');
		//添加分类
		Route::get('create', 'CategoryController@create');
	});

});

*/



// Home模块下 三级模式
Route::group(['namespace' => 'Home', 'prefix' => 'home'], function () {
    Route::group(['prefix' => 'index'], function () {

    	// www.test.com/home/index/index?id=1&name=hello
        Route::get('index', 'IndexController@index');
        // www.test.com/home/index/index_copy/1?name=hello
        Route::get('index_copy/{id}', 'IndexController@index_copy');

        Route::get('view', 'IndexController@view');
    });
    Route::group(['prefix' => 'mysql'], function () {

        Route::get('index', 'MysqlController@index');
        Route::get('add', 'MysqlController@add');
        Route::get('update', 'MysqlController@update');
        Route::get('del', 'MysqlController@del');
        Route::get('drop_table', 'MysqlController@drop_table');
        Route::get('boot', 'MysqlController@boot');
    
    });
});

