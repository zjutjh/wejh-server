<?php
/**
 * 处理所有的第三方认证，如处理code返回openid，或者跳转认证
 */
namespace App\Http\Controllers\Auth;

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
        $result = $miniProgram->sns->getSessionKey($code);

        return RJM([
            'openid' => $result->openid
        ], 200, '获取openid成功');
    }
}
