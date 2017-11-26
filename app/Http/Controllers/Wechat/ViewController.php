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
        return redirect($url);
    }
}