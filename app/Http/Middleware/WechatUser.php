<?php

namespace App\Http\Middleware;

use App\WechatUser as Member;
use Closure;

class WechatUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = session('wechat.oauth_user');
        $user = $user['default'];
        $member = Member::where('openid', '=', $user->original['openid'])->first();

        $wechat_user = [
            'openid' => $user->original['openid'],
            'nickname' => $user->original['nickname'],
            'sex' => $user->original['sex'],
            'province' => $user->original['province'],
            'city' => $user->original['city'],
            'country' => $user->original['country'],
            'headimgurl' => $user->original['headimgurl'],
            'privilege' => json_encode($user->original['privilege']),
        ];

        if (! $member){
            Member::create($wechat_user);
        }else{
            $member->update($wechat_user);
        }
        return $next($request);
    }
}
