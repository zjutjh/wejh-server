<?php
namespace App\Http\Controllers\Wechat;
use BadMethodCallException;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\User;
class ViewController extends Controller
{
    public function view(Request $request)
    {
        $url = $request->get('url');
        $content = http_get($url);
        $content = preg_replace('/data-src="([^"]+)"/is', 'src="' . 'http://wejh-server.dev/wechat/image' . '?url=' . ('$1') . '"', $content);
        return $content;
    }

    public function image(Request $request)
    {
        $url = $request->get('url');
        $content = file_get_contents($url);
        return response($content)->header('Content-Type', 'image/webp');
    }
}