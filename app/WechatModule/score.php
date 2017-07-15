<?php
use EasyWeChat\Message\News;
use App\Models\User;

function fixScoreResponseText($msg, $gpa) {
    $answer = "";
    $answer .= "【".$msg[0]['学期']."】\n";
    foreach ($msg as $key => $value) {
        $answer .= "\u{27A1}".$value['名称']."\n成绩：".$value['成绩'];
        $b = $value['成绩'];
        if(!is_numeric($b))
            switch($b)
            {
                case "优秀":
                    $b=4.5;
                    break;
                case "良好":
                    $b=3.5;
                    break;
                case "中等":
                    $b=2.5;
                    break;
                case "及格":
                    $b=1.5;
                    break;
                default:
                    $b=0;
            }
        else
            $b=60<=$b?($b-50)/10:0;
        if($b==5)
            $answer .="[惊恐]";
        else if($b>=4&&$b<5)
            $answer .="[酷]";
        else if($b>=3&&$b<4)
            $answer .="[得意]";
        else if($b>=2&&$b<3)
            $answer .="[微笑]";
        else if($b>=1&&$b<2)
            $answer .="[奋斗]";
        else if($value['成绩']=="免修")
            $answer .="[惊讶]";
        else if($value['成绩']=="取消")
            $answer .="[疑问]";
        else
            $answer .="[衰]";
        $answer .= "\n";
    }
    $answer .= "本学期平均绩点为: ".$gpa."\n\n<a href='" . (url('score')) . "'>切换学期</a>\n";
    return $answer;
}

$openid = $message->FromUserName;
if(!$user = User::getUserByOpenid($openid)) {
    $news = new News([
        'title' => '请先绑定账号',
        'description' => '使用本公众号服务需要先进入微精弘绑定账号',
        'url' => url('/'),
    ]);
    return $news;
}
if(!$user->uno) {
    $news = new News([
        'title' => '请先绑定精弘通行证',
        'description' => '使用本公众号服务需要先绑定精弘通行证',
        'url' => url('/login')
    ]);
    return $news;
}
if(!$yc_password = $user['ext']['passwords']['yc_password']) {
    $news = new News([
        'title' => '请先绑定原创教务系统账号',
        'description' => '使用查成绩服务需要先进入微精弘绑定原创教务系统账号',
        'url' => url('/setting/ycjw/login')
    ]);
    return $news;
}
$scores = getYcData('score', $user->uno, decrypt($yc_password), $user['ext']['terms']['score_term']);
if(is_array($scores))
{
    if(count($scores) == 0) {
        $news = new News([
            'title' => '本学期没有相关信息',
            'description' => '点击这里切换学期',
            'url' => url('/score')
        ]);
        return $news;
    }

    $text = fixScoreResponseText($scores['list'], $scores['gpa']);
    return respText($text);
}
if($error = $api->getError()) {
    if($error == '用户名或密码错误') {
        $news = new News([
            'title' => '用户名或密码错误',
            'description' => '请重新绑定原创教务系统账号',
            'url' => url('/setting/ycjw/login')
        ]);
        return $news;
    } else if ($error == '原创服务器错误') {
        $news = new News([
            'title' => '原创服务器炸了',
            'description' => '请尝试进入微精弘做查询操作',
            'url' => url('/')
        ]);
        return $news;
    }
    return $error;
}
return '查询成绩错误';