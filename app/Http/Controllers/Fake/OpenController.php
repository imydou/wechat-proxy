<?php

namespace App\Http\Controllers\Fake;

use App\Client;
use App\OAuthLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OpenController extends Controller
{
    protected $error_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxf0e81c3bee622d60&redirect_uri=http%3A%2F%2Fnba.bluewebgame.com%2Foauth_response.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';

    public function connect_oauth2_authorize(Request $request)
    {
        $query = $request->all();

        // 提取 redirect_domain
        $redirect_url = parse_url(urldecode($query['redirect_uri']));
        $redirect_domain = $redirect_url['host'];

        $client = Client::where('appid', $query['appid'])->first();

        // appid 不存在
        if (! $client)
            return redirect($this->error_url);

        // appid redirect_domain 不匹配
        if ($client->redirect_domain != $redirect_domain)
            return  redirect($this->error_url);

        $o_auth_log = OAuthLog::create([
            'client_appid' => $query['appid'],
            'client_redirect_uri' => urldecode($query['redirect_uri']),
            'client_scope' => $query['scope'],
            'client_state' => $query['state'],
        ]);

        $request->session()->put('o_auth_log_id', $o_auth_log->id);

        return redirect('/oauth');
    }
}
