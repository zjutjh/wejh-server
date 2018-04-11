<?php
namespace App\Http\Controllers\Wechat;
use App\Models\FailedJob;
use App\Models\Job;
use App\Models\Log;
use App\Models\SystemSetting;
use App\Models\TemplateMessage;
use BadMethodCallException;
use Carbon\Carbon;
use EasyWeChat\Core\Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Auth;
use Illuminate\Support\Facades\Redis;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Voice;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Material;
use Emoji;
use Excel;
use App\Models\User;
use App\Jobs\SendTemplateMessage;
class WeappServerController extends Controller
{
    public $wechat;
    public $default_message = '对面没有人哦~想要反馈的话，请点击"我的"-"反馈"，然后点击"我要反馈"输入微精弘的相关反馈建议';
    public function __construct()
    {
        $this->wechat = app('wechat');
        //$this->default_message = '这是一条默认消息';
    }
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        $this->wechat->server->setMessageHandler(function($message) {
            $type = $message->MsgType;
            try {
                $response = $this->$type($message); //反射到各个类型的方法
                return $response;
            } catch (BadMethodCallException $e) {
                //do log or sth.
                return '可能发生了一点错误，请联系管理员';//回复默认消息
                // return $this->default_message;//回复默认消息
            }
        });
        $response =  $this->wechat->server->serve();
        return $response;
    }
    /**
     * 处理事件消息
     * 通过反射分发消息给其他方法
     *
     * @return string
     */
    public function event($message) {
        $eventType = $message->Event;
        $response = $this->$eventType($message); //反射到各个事件类型的方法
        return $response;
    }
    /**
     * 模板消息送达通知
     *
     * @return string
     */
    public function TEMPLATESENDJOBFINISH($message) {
        return '';
    }
    /**`
     * 扫描二维码消息
     *
     * @return string
     */
    public function SCAN($message) {
        return '';
    }
    /**
     * 处理文本消息
     *
     * @return string
     */
    public function text($message)
    {
//        $accessToken = $this->get_weapp_access_token();
//        $openId = $message->FromUserName;
//        $post_data = [
//            'touser' => $openId,
//            'msgtype' => "text",
//            'text' => [
//                'content' => $this->default_message
//            ]
//        ];
//        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $accessToken;
//        $res = http_post($url, $post_data, 500, 'json');
//        $result = json_decode($res, true);
//        if (isset($result['errcode']) && intval($result['errcode']) > 0) {
//            $this->get_weapp_access_token(true);
//        }
        return $this->user_enter_tempsession($message);
    }

    /**
     * 处理关键字匹配
     * @param $message
     * @return mixed
     */
    public function matchKeyword($message) {
        $content = $message->MsgType == 'text' ? $message->Content : $message->EventKey;
        $keywords = config('keyword');
        foreach($keywords as $key => $value) {
            if($value['status'] == 1 && $this->isMatch($content, $value)) {
                return $this->reply($value['reply'], $message);
            }
        }
    }

    /**
     * @param $reply
     * @param $message
     * @return bool|Article|Image|Material|News|Text|Video|Voice
     */
    function reply($reply, $message) {
        switch ($reply['type']) {
            case 'userapi':
                $func = $reply['content'];
                try {
                    return wechatModule($func, $message);
                } catch (BadMethodCallException $e) {
                    //do log or sth.
                    return '';//回复默认消息
                }
                break;
            case 'text':
                $text = new Text(['content' => $reply['content']]);
                return $text;
                break;
            case 'image':
                $image = new Image(['media_id' => $reply['content']]);
                return $image;
                break;
            case 'video':
                $video = new Video([
                    'title' => isset($reply['title']) ? $reply['title'] : '',
                    'media_id' => $reply['content'],
                    'description' => isset($reply['description']) ? $reply['description'] : '',
                ]);
                return $video;
                break;
            case 'voice':
                $voice = new Voice(['media_id' => $reply['content']]);
                return $voice;
                break;
            case 'news':
                $news = new News([
                    'title' => isset($reply['title']) ? $reply['title'] : '',
                    'description' => isset($reply['description']) ? $reply['description'] : '',
                    'url' => isset($reply['url']) ? $reply['url'] : '',
                    'image' => isset($reply['image']) ? $reply['image'] : '',
                    // ...
                ]);
                return $news;
                break;
            case 'article':
                $article = new Article([
                    'title' => isset($reply['title']) ? $reply['title'] : '',
                    'author' => isset($reply['author']) ? $reply['author'] : '',
                    'content' => $reply['content'],
                    // ...
                ]);
                return $article;
                break;
            case 'material':
                $material = new Material('mpnews', $reply['content']);
                return $material;
                break;
            default:
                return false;
        }
    }

    /**
     * 处理关键词是否匹配
     * @param $message
     * @param $keyword
     * @return bool
     */
    public function isMatch($message, $keyword) {
        switch ($keyword['type']) {
            case '1':
                return $message == $keyword['content'];
                break;
            case '2':
                return !!strstr($message, $keyword['content']);
                break;
            case '3':
                return !!preg_match('/(' . $keyword['content'] . ')/is', $message);
                break;
            default:
                return false;
        }
    }

    /**
     * 处理浏览会员卡事件
     *
     * @return string
     */
    public function user_view_card($message)
    {
        return '';
    }
    /**
     * 处理从会员卡进入公众号事件
     *
     * @return string
     */
    public function user_enter_session_from_card($message) {
        return '';
    }

    public function get_weapp_access_token($get_new = false) {
        $accessTokenArray = setting('weapp_access_token');

        if ($accessTokenArray && intval($accessTokenArray['expires']) > time() && !$get_new) {
            return $accessTokenArray['access_token'];
        }

        $res = http_get('https://api.weixin.qq.com/cgi-bin/token', [
            'grant_type' => 'client_credential',
            'appid' => env('WEAPP_APPID'),
            'secret' => env('WEAPP_SECRET')
        ]);
        $value = json_decode($res, true);
        $accessToken = $value['access_token'];
        $systemSetting = new SystemSetting();
        $systemSetting->addSetting('weapp_access_token', [
            'access_token' => $accessToken,
            'expires' => intval($value['expires_in']) + time(),
        ]);
        return $accessToken;
    }

    /**
     * 处理用户进入临时会话事件
     *
     * @return string
     */
    public function user_enter_tempsession($message) {
        $SessionFrom = $message->SessionFrom;
        if ($SessionFrom === 'follow') {
            $accessToken = $this->get_weapp_access_token();
            $openId = $message->FromUserName;
            $post_data = [
                'touser' => $openId,
                'msgtype' => "link",
                'link' => [
                    'title' => '精弘网络服务号 | 全工大最好用的服务号',
                    'description' => '提供众多校内实用功能，完美结合精弘网络产品，是工大学子学习生活的好帮手。',
                    'url' => 'https://mp.weixin.qq.com/s?__biz=MzA3ODU1ODQ5Nw==&mid=502376411&idx=1&sn=ac0a250efff6cbef888770a0b4d129bb&chksm=0745e39530326a8340eed03ecd7f77a3dc06e5cf5e9b6c1e768f309e00516c225cbd0a3b580c',
                    'thumb_url' => 'https://mmbiz.qlogo.cn/mmbiz_png/Fa51x3HOXotMMW7tZprORYlreY9BEzcFLyfe5LaSrZBVNUGWpSnCBaT2OvVNUKZmkAp0NHic1q7yEtdtMVDgNkA/0?wx_fmt=png'
                ]
            ];
            $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $accessToken;
            $res = http_post($url, $post_data, 500, 'json');
            $result = json_decode($res, true);
            if (isset($result['errcode']) && intval($result['errcode']) > 0) {
                $this->get_weapp_access_token(true);
            }
            return '';
        }
        return '';
    }
    /**
     * 处理点击事件消息
     *
     * @return string
     */
    public function click($message) {
        $response = $this->matchKeyword($message);
        return $response ? $response:'';
    }
    /**
     * 处理关注事件消息
     *
     * @return string
     */
    public function subscribe($message) {
        return "";
    }
    /**
     * 处理取消关注事件消息
     *
     * @return string
     */
    public function unsubscribe($message) {
        //do sth.
//        if($user = User::where('openid', $message->FromUserName)->first()) {
//            $user->subscribe = 0;
//            $user->save();
//        }
        return;
    }
    /**
     * 处理图片消息
     *
     * @return string
     */
    public function image($message) {
        return "";
    }
    /**
     * 处理语音消息
     *
     * @return string
     */
    public function voice($message) {
        return "";
    }
    /**
     * 处理视频消息
     *
     * @return string
     */
    public function video($message) {
        return "";
    }
    /**
     * 处理小视频消息
     *
     * @return string
     */
    public function shortvideo($message) {
        return "";
    }
    /**
     * 处理位置消息
     *
     * @return string
     */
    public function location($message) {
        return "";
    }
    /**
     * 处理链接消息
     *
     * @return string
     */
    public function link($message) {
        return "";
    }
}