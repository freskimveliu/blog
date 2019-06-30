<?php

    Route::post(        '/register',                  'API\Auth\AuthController@register');
    Route::post(        '/login',                     'API\Auth\AuthController@login');
    Route::apiResource( '/users',                     'API\Users\UsersController');
    Route::get(         '/users/{slug}/posts',        'API\Users\UserPostsController@index');
    Route::apiResource( '/posts',                     'API\Posts\PostsController');
    Route::apiResource( '/posts/{id}/comments',       'API\Posts\PostCommentsController');
    Route::get(         '/categories',                'API\Auth\Posts\PostsController@getCategories');


Route::group(['middleware'=>'api-auth'],function (){
    Route::post(        '/posts/{id}/favorite',       'API\Posts\PostsController@favoriteAction');
    Route::get(         '/profile',                   'API\Auth\Users\UsersController@index');
    Route::post(        '/profile',                   'API\Auth\Users\UsersController@update');
    Route::apiResource( '/my/posts/{id}/comments',    'API\Auth\Posts\PostCommentsController');
    Route::apiResource( '/my/posts',                  'API\Auth\Posts\PostsController');
    Route::apiResource( '/my/relationships',          'API\Auth\Users\UserRelationshipsController');
});


Route::group(['middleware'=>'api-admin'],function (){
    Route::apiResource( '/admin/categories',          'API\Admin\Posts\CategoriesController');
    Route::apiResource( '/admin/posts',               'API\Admin\Posts\PostsController');
    Route::apiResource( '/admin/users',               'API\Admin\Users\UsersController');

});
