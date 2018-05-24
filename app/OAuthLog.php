<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OAuthLog extends Model
{
    protected $guarded = [];

    public function wechat_user()
    {
        return $this->hasOne('App\WechatUser', 'openid', 'openid');
    }
}
