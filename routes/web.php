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
    //$users = \App\Models\User::all();
    //return $users;
//    $user = new \App\Models\User;
//    $user->uno = '201319630201';
//    $user->save();
//    \App\Models\User::create([
//        'class' => '软件工程1302班',
//        'uno' => '201319630201',
//    ]);
    $user = \App\Models\User::where('uno', '201319630201')->first();
    return $user;
    return view('welcome');
});
