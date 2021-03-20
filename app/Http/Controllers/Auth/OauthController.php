<?php
/**
 * 处理所有的第三方认证，如处理code返回openid，或者跳转认证
 */

namespace App\Http\Controllers\Auth;

use App\Jobs\AsyncUserUnionid;
use App\Models\OpenidLink;
use App\Models\User;
use App\Models\UserLink;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Overtrue\Socialite\AuthorizeFailedException;
use EasyWeChat\Foundation\Application;

class OauthController extends Controller
{
    // 跳转到微信认证
    public function wechat(Request $request)
    {
        $app = app('wechat');
        $response = $app->oauth->scopes(['snsapi_userinfo'])->setRequest($request)
            ->redirect();
        return $response;
    }

    public function wechatLogin(Request $request)
    {
        $app = app('wechat');
        $wechatUser = null;
        try {
            //如果没有oauth信息，则跳转微信认证
            if (!$wechatUser = $app->oauth->setRequest($request)->user()) {
                return $this->wechat($request);
            }
        } catch (AuthorizeFailedException $exception) {
            return $this->wechat($request);
        }
        $openid = $wechatUser->original['openid'];

        session([
            'openid' => $openid
        ]);

        if ($uno = session('uno')) {
            if (!$user = User::where('uno', $uno)->first()) {
                return response('没有相关用户信息');
            }
            $unionid = $wechatUser->original['unionid'];
            UserLink::updateOrCreate([
                'uid' => $user->id,
                'type' => 'wechat'
            ], [
                'access_token' => '',
                'openid' => $openid
            ]);
            OpenidLink::updateOrCreate([
                'unionid' => $unionid,
                'type' => 'wechat'
            ], [
                'openid' => $openid
            ]);
        }

        return redirect()->action('Auth\OauthController@webLogin');
    }

    public function unoLogin(Request $request)
    {
        if (!$uno = $request->get('uno')) {
            return redirect()->action('Auth\OauthController@wechatLogin');
        }
        if (!$user = User::where('uno', $uno)->first()) {
            return redirect()->action('Auth\OauthController@wechatLogin');
        }
        $uid = $user->id;
        if (!$userLink = UserLink::where('uid', $uid)->where('type', 'wechat')->first()) {
            session([
                'uno' => $uno
            ]);
            return redirect()->action('Auth\OauthController@wechatLogin');
        }
        return view('login', [
            'isBind' => 'true',
            'openid' => ''
        ]);
    }

    public function webLogin(Request $request)
    {
        if (!$openid = session('openid')) {
            return RJM(null, -1, '没有认证信息');
        }
        $isBind = 'false';

        if ($userLink = UserLink::where('openid', $openid)->first()) {
            $isBind = 'true';
        } else {
            $isBind = 'false';
        }

        return view('login', [
            'isBind' => $isBind,
            'openid' => $openid
        ]);
    }

    // 小程序code换取openid
    public function weapp(Request $request)
    {

        $mode = $request->get('mode');

        if (!$code = $request->get('code')) {
            return RJM(null, -401, 'code不存在');
        }
        $options = [
            'mini_program' => [
                'app_id' => env('WEAPP_APPID'),
                'secret' => env('WEAPP_SECRET'),
                'token' => env('WEAPP_TOKEN'),
                'aes_key' => env('WEAPP_AES_KEY'),
            ],
        ];
        $app = new Application($options);
        $miniProgram = $app->mini_program;
        $userService = $app->user;
        $result = $miniProgram->sns->getSessionKey($code);

        if (!$result->unionid) {
            return RJM(null, -1, '请先关注"zjutjh"和"jxhzjut"公众号');
        }

        if ($link = UserLink::where('openid', $result->unionid)->first()) {
            $link->openid = $result->openid;
            $link->save();
            OpenidLink::updateOrCreate([
                'unionid' => $result->unionid,
                'type' => 'weapp'
            ], [
                'openid' => $result->openid
            ]);
        }
        if (!!$code && $mode === "wechat")
            return LoginController::autoLoginImpl($result->openid, 'weapp');

        return RJM([
            'openid' => $result->openid,
        ], 200, '获取openid成功');
    }
}
