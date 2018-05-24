<?php

namespace App\Http\Controllers;

use App\OAuthLog;
use App\WechatUser;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    protected $wechat_user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $oauth_user = session('wechat.oauth_user');

            $this->wechat_user = WechatUser::where('openid', '=', $oauth_user['default']->original['openid'])->first();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $o_auth_log_id = $request->session()->get('o_auth_log_id');
        $o_auth_log = OAuthLog::find($o_auth_log_id);

        $virtual_code = (new ToolController())->generate_unique_string('o_auth_logs', 'virtual_code', 32);
        $virtual_access_token = (new ToolController())->generate_unique_string('o_auth_logs', 'virtual_access_token', 128);
        $virtual_refresh_token = (new ToolController())->generate_unique_string('o_auth_logs', 'virtual_refresh_token', 128);

        $o_auth_log->update([
            'virtual_code' => $virtual_code,
            'virtual_access_token' => $virtual_access_token,
            'virtual_refresh_token' => $virtual_refresh_token,
            'openid' => $this->wechat_user->openid,
        ]);

        $redirect_url_parse = parse_url($o_auth_log->client_redirect_uri);
        if (isset($redirect_url_parse['query'])) {
            $redirect_uri = $o_auth_log->client_redirect_uri . '&code=' . $virtual_code . '&state=' . $o_auth_log->client_state;
        }else{
            $redirect_uri = $o_auth_log->client_redirect_uri . '?code=' . $virtual_code . '&state=' . $o_auth_log->client_state;
        }

        return redirect($redirect_uri);
    }
}
