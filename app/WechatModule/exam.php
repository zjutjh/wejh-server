<?php
use App\Models\User;
use EasyWeChat\Message\News;

function fixExamResponseText($exams, $term)
{
    $answer = "";
    $answer .= "【".$term." 考试安排】\n";
    if($exams) {
        foreach ($exams as $key => $value)
        {
            $answer .= "\u{27A1}".$value['课程'];
            $value['日期'] && $answer .= "\n日期：".$value['日期'];
            $value['时段'] && $answer .= "\n时段：".$value['时段'];
            $value['教室'] && $answer .= "\n教室：".$value['教室'];
            $value['班级'] && $answer .= "\n班级：".$value['班级'];
            $value['教师'] && $answer .= "\n教师：".$value['教师'];
            $answer .= "\n";
        }
    } else {
        $answer .= "\n惊了！这学期还没有安排考试！\n\n你可以尝试点击下面切换学期";
    }
    $answer .= "\n<a href='" . (url('exam')) . "'>切换学期</a>" . $this->footer;
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
        'description' => '使用查排考服务需要先进入微精弘绑定原创教务系统账号',
        'url' => url('/setting/ycjw/login')
    ]);
    return $news;
}
$api = new \App\Models\Api;
$exam = $api->getYcData('exam', $user->uno, decrypt($yc_password), $user['ext']['terms']['exam_term']);
if(is_array($exam))
{
    $text = fixExamResponseText($exam['list'], $user['ext']['terms']['exam_term']);
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
    } else if ($error == '服务器错误') {
        $news = new News([
            'title' => '服务器错误',
            'description' => '请尝试进入微精弘做查询操作',
            'url' => url('/')
        ]);
        return $news;
    }
    return $error;
}
return '查询排考错误';