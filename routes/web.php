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
use Illuminate\Http\Request;

Route::get('/',  function () {
    return response('微精弘');
});
Route::get('/oauth/wechat', [
    'uses' => 'Auth\OauthController@wechat']);
Route::get('/oauth/wechat/login', [
    'uses' => 'Auth\OauthController@wechatLogin']);
Route::get('/walk', [
    'uses' => 'Walk\MainController@main']);
Route::get('/wechat/view', [
    'uses' => 'Wechat\ViewController@view']);
Route::get('/wechat/image', [
    'uses' => 'Wechat\ViewController@image']);

Route::get('/zjut/view', [
    'uses' => 'Ycjw\MainController@view']);
Route::get('/zjut/image/{path}/{file}', [
    'uses' => 'Ycjw\MainController@image']);
Route::get('/zjut/js/{file}', [
    'uses' => 'Ycjw\MainController@js']);
Route::get('/zjut/css/{file}', [
    'uses' => 'Ycjw\MainController@css']);
Route::get('/zjutsso/{path}',   function ($path, Request $request) {
    $params = $request->all();
    return redirect('http://www.zjut.edu.cn/zjutsso/' . $path . '?' . http_build_query($params));
});

// 微信服务号/订阅号名片跳转
Route::get('/weixincard/{id}',  function ($id) {
    return redirect('https://mp.weixin.qq.com/mp/profile_ext?action=home&scene=110&__biz='.$id.'==#wechat_redirect');
});

Route::get('/decode/{id}',  function ($id) {
    if (env('APP_ENV') === 'local') {
        return decrypt($id);
    }
    return redirect('/');
});
