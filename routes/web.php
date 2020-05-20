<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/annotate/{selected_video_id}', 'VideoController@annotate')->name('videos.annotate');
Route::post('/upload_json', 'VideoController@upload_json');
Route::post('/save_json', 'VideoController@save_json');
Route::post('/delete_after', 'VideoController@delete_after');
Route::post('/delete_before', 'VideoController@delete_before');
Route::get('/download/{video_id}', 'VideoController@download')->name("videos.download");
Route::get('/videos/delete/{video_id}', 'VideoController@delete')->name("videos.delete");
Route::get('/videos/upload_more_file/{video_id}', 'VideoController@upload_more_file')->name("videos.upload_more_file");
Route::post('/upload_more_file_store', 'VideoController@upload_more_file_store')->name("videos.upload_more_file_store");

Route::get('/users/delete/{user_id}', 'UserController@delete')->name("users.delete");

Route::resource('users','UserController');
Route::resource('videos','VideoController');