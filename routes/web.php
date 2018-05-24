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
    return 'Wechat Proxy';
});

// 模拟 https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
Route::get('/connect/oauth2/authorize', 'Fake\OpenController@connect_oauth2_authorize');

// 模拟 https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code
Route::get('/sns/oauth2/access_token', 'Fake\ApiController@sns_oauth2_access_token');

// 模拟 https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=APPID&grant_type=refresh_token&refresh_token=REFRESH_TOKEN
Route::get('/sns/oauth2/refresh_token', 'Fake\ApiController@sns_oauth2_refresh_token');

// 模拟 https://api.weixin.qq.com/sns/userinfo?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN
Route::get('/sns/userinfo', 'Fake\ApiController@sns_userinfo');

Route::group(['middleware' => ['web', 'wechat.oauth:snsapi_userinfo', 'wechat.user']], function () {
    Route::get('/user', function () {
        $user = session('wechat.oauth_user'); // 拿到授权用户资料

        dd($user);
    });
    Route::get('/oauth', 'OAuthController@index');
});

Route::get('MP_verify_{text}.txt', function($text){
    return $text;
});