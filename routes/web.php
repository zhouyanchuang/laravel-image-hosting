<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::namespace('Admin')->prefix('admin')->group(function () {
    Route::get('index', 'IndexController@index');
    Route::any('doAdd', 'IndexController@doAdd');
    // 图片管理
    Route::any('image', 'ImageController@index');
    Route::any('image/upload', 'ImageController@upload');
    Route::any('image/delete', 'ImageController@delete');

    // 图片分类管理
    Route::any('image/addCategory', 'ImageCategoryController@addCategory');
    Route::any('image/getAllImageCategory', 'ImageCategoryController@getAllImageCategory');
    Route::any('image/editCategory', 'ImageCategoryController@editCategory');
    Route::any('image/delImageCategory', 'ImageCategoryController@delImageCategory');
});
