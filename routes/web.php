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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/home/download', 'HomeController@download');

Route::post('/home/upload', 'HomeController@upload');

Route::get('/home/delete', 'HomeController@delete');

Route::get('/users', 'AdminController@index')->middleware('admin');

Route::get('/users/delete', 'AdminController@delete')->middleware('admin');

Route::get('/forbidden', 'AdminController@filter');
