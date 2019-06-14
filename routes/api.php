<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::post(     '/register',                  'API\Auth\AuthController@register');
    Route::post(     '/login',                     'API\Auth\AuthController@login');
    Route::resource( '/posts',                     'API\Posts\PostsController');
    Route::resource( '/posts/{id}/comments',       'API\Posts\PostCommentsController');

Route::group(['middleware'=>'api-auth'],function (){
    Route::post(     '/posts/{id}/favorite',       'API\Posts\PostsController@favoriteAction');
    Route::get(      '/profile',                   'API\Auth\Users\UsersController@index');
    Route::post(     '/profile',                   'API\Auth\Users\UsersController@update');
    Route::resource( '/my/posts/{id}/comments',    'API\Auth\Posts\PostCommentsController');
    Route::resource( '/my/posts',                  'API\Auth\Posts\PostsController');
    Route::get(      '/categories',                'API\Auth\Posts\PostsController@getCategories');
});
Route::group(['middleware'=>'api-admin'],function (){
    Route::resource( '/admin/categories',          'API\Admin\Posts\CategoriesController');
    Route::resource( '/admin/posts',               'API\Admin\Posts\PostsController');
    Route::resource( '/admin/users',               'API\Admin\Users\UsersController');

});
