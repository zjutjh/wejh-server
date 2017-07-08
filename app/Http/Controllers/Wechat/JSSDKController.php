<?php
namespace App\Http\Controllers\Wechat;
use BadMethodCallException;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\User;
class JSSDKController extends Controller
{
    public function signPackage(Request $request)
    {
        $url = $request->get('url');
        $js = app('wechat')->js;
        $js->setUrl($url);
        return $js->config(['onMenuShareTimeline','onMenuShareAppMessage', 'hideMenuItems','showMenuItems'],
            false, false, false);
    }
}