<?php
/**
 * 处理所有的第三方认证，如处理code返回openid，或者跳转认证
 */
namespace App\Http\Controllers\Auth;

use App\Models\OpenidLink;
use App\Models\UserLink;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Overtrue\Socialite\AuthorizeFailedException;
use EasyWeChat\Foundation\Application;

class OauthController extends Controller
{
    // 跳转到微信认证
    public function wechat(Request $request) {
        $app = app('wechat');
        $response = $app->oauth->scopes(['snsapi_userinfo'])->setRequest($request)
            ->redirect();
        return $response;
    }

    public function wechatLogin(Request $request) {
        $app = app('wechat');
        $wechatUser = null;
        try {
            //如果没有oauth信息，则跳转微信认证
            if(!$wechatUser = $app->oauth->setRequest($request)->user()) {
                return $this->wechat($request);
            }
        } catch (AuthorizeFailedException $exception) {
            return $this->wechat($request);
        }
        $unionid = $wechatUser->original['unionid'];

        return redirect(url('/') . '?unionid=' . $unionid);
    }

    // 小程序code换取openid
    public function weapp(Request $request) {
        if(!$code = $request->get('code')) {
            return RJM(null, -401, 'code不存在');
        }
        $options = [
            'mini_program' => [
                'app_id'   => env('WEAPP_APPID'),
                'secret'   => env('WEAPP_SECRET'),
                'token'    => env('WEAPP_TOKEN'),
                'aes_key'  => env('WEAPP_AES_KEY'),
            ],
        ];
        $app = new Application($options);
        $miniProgram = $app->mini_program;
        $userService = $app->user;
        $result = $miniProgram->sns->getSessionKey($code);

        if (!$result->unionid) {
            return RJM(null, -1, '请先关注"zjutjh"和"jxhzjut"公众号');
        }

        if($link = UserLink::where('openid', $result->unionid)->first()) {
            $link->openid = $result->openid;
            $link->save();
            if (!$openidLink = OpenidLink::where('unionid', $result->unionid)->where('type', 'weapp')->first()) {
                OpenidLink::create([
                    'unionid' => $result->unionid,
                    'type' => 'weapp',
                    'openid' => $result->openid
                ])->save();
            } else {
                $openidLink->openid = $result->openid;
                $openidLink-save();
            }
        }

        return RJM([
            'openid' => $result->openid,
        ], 200, '获取openid成功');
    }
}
