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

Route::post('auth/login', 'Guard@login');
Route::post('auth/resetPassword', 'Guard@resetPassword');

Route::group(['middleware' => 'jwt.auth'], function () {
   
    Route::get('listUsers', 'Users@listUsers');
    Route::get('deleteUser', 'Users@deleteUser');
    Route::post('saveUser', 'Users@saveUser');

   
    Route::get('yetkiler', 'Users@yetkiler');
    Route::get('yetkiDefault', 'Users@yetkiDefault');

    Route::get('profilGetir','Users@profilGetir');
    Route::post('profilKaydet','Users@profilKaydet');
    Route::get('guard', 'Users@guard');
       
    Route::post('upload', 'Upload@uploadFile');

 
});

