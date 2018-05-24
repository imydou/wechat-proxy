<?php

namespace App\Http\Controllers\Fake;

use App\Client;
use App\Http\Controllers\ToolController;
use App\OAuthLog;
use App\WechatUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected $error = [
        'errcode' => 40029,
        'errmsg' => 'invalid code',
    ];

    public function sns_oauth2_access_token(Request $request)
    {
        $query = $request->all();

        $client = Client::where('appid', $query['appid'])->first();

        // appid 不存在
        if(! $client)
            return $this->error;

        // secret 不匹配
        if ($client->secret != $query['secret'])
            return $this->error;

        $o_auth_log = OAuthLog::where('virtual_code', $query['code'])->first();

        // code 无效
        if (! $o_auth_log)
            return $this->error;

        // code 和 appid 不匹配
        if ($o_auth_log->client_appid != $client->appid)
            return $this->error;

        return [
            'access_token' => $o_auth_log->virtual_access_token,
            'expires_in' => 7200,
            'refresh_token' => $o_auth_log->virtual_refresh_token,
            'openid' => $o_auth_log->openid,
            'scope' => $o_auth_log->client_scope,
        ];
    }

    public function sns_oauth2_refresh_token(Request $request)
    {
        $query = $request->all();

        $o_auth_log = OAuthLog::where('virtual_refresh_token', $query['refresh_token'])->first();

        // refresh_token 无效
        if (! $o_auth_log)
            return $this->error;

        // refresh_token 和 appid 不匹配
        if ($o_auth_log->client_appid != $query['appid'])
            return $this->error;

        $virtual_access_token = (new ToolController())->generate_unique_string('o_auth_logs', 'virtual_access_token', 128);
        $virtual_refresh_token = (new ToolController())->generate_unique_string('o_auth_logs', 'virtual_refresh_token', 128);
        $o_auth_log->update([
            'virtual_access_token' => $virtual_access_token,
            'virtual_refresh_token' => $virtual_refresh_token,
        ]);

        return [
            'access_token' => $o_auth_log->virtual_access_token,
            'expires_in' => 7200,
            'refresh_token' => $o_auth_log->virtual_refresh_token,
            'openid' => $o_auth_log->openid,
            'scope' => $o_auth_log->client_scope,
        ];
    }

    public function sns_userinfo(Request $request)
    {
        $query = $request->all();

        $o_auth_log = OAuthLog::where('virtual_access_token', $query['access_token'])->first();

        // access_token 无效
        if (! $o_auth_log)
            return $this->error;

        // access_token 和 openid 不匹配
        if ($o_auth_log->openid != $query['openid'])
            return $this->error;

        $wechat_user = WechatUser::where('openid', $o_auth_log->openid)->first();

        return [
            'openid' => $wechat_user->openid,
            'nickname' => $wechat_user->nickname,
            'sex' => $wechat_user->sex,
            'province' => $wechat_user->province,
            'city' => $wechat_user->city,
            'country' => $wechat_user->country,
            'headimgurl' => $wechat_user->headimgurl,
            'privilege' => json_decode($wechat_user->privilege, true),
        ];
    }
}
