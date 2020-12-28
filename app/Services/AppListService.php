<?php
namespace App\Services;

class AppListService {

    public static function getAppList()
    {
        $colors = ['red', 'green', 'blue', 'yellow', 'purple'];
        $icons = [
            'timetable' => [
                'bg' => 'blue',
                'icon' => 'https://gw.alicdn.com/tfs/TB1zXRm4uL2gK0jSZPhXXahvXXa-652-652.png',
                'card' => 'https://gw.alicdn.com/tfs/TB1DBJr4EH1gK0jSZSyXXXtlpXa-1125-300.png',
            ],
            'score' => [
                'bg' => 'red',
                'icon' => 'https://gw.alicdn.com/tfs/TB1bYcjpODsXe8jSZR0XXXK6FXa-652-652.png',
            ],
            'exam' => [
                'bg' => 'green',
                'icon' => 'https://gw.alicdn.com/tfs/TB1oPcIrSR26e4jSZFEXXbwuXXa-652-652.png',
            ],
            'freeroom' => [
                'bg' => 'red',
                'icon' => 'https://gw.alicdn.com/tfs/TB1ZG8t4EY1gK0jSZFCXXcwqXXa-652-652.png',
            ],
            'student' => [
                'bg' => 'purple',
                'icon' => 'https://gw.alicdn.com/tfs/TB1HilgsRFR4u4jSZFPXXanzFXa-652-652.png',
            ],
            'teacher' => [
                'bg' => 'purple',
                'icon' => 'https://gw.alicdn.com/tfs/TB1nqRMt8Bh1e4jSZFhXXcC9VXa-652-652.png',
            ],
            'card' => [
                'bg' => 'yellow',
                'icon' => 'https://gw.alicdn.com/tfs/TB10a8t4EY1gK0jSZFCXXcwqXXa-652-652.png',
                'card' => 'https://gw.alicdn.com/tfs/TB1De6Xn4vbeK8jSZPfXXariXXa-1125-300.png',
            ],
            'borrow' => [
                'bg' => 'blue',
                'icon' => 'https://gw.alicdn.com/tfs/TB13t8D4ET1gK0jSZFrXXcNCXXa-652-652.png',
                'card' => 'https://gw.alicdn.com/tfs/TB1xf0m4Ez1gK0jSZLeXXb9kVXa-1125-300.png',
            ],
            'questionnaire' => [
                'bg' => 'red',
                'icon' => 'https://assets.gettoset.cn/wejh/icon-questionnaire-652-652.png',
            ],
            'tri' => [
                'bg' => 'blue',
                'icon' => '',
                'card' => 'https://gw.alicdn.com/tfs/TB1I4FMptTfau8jSZFwXXX1mVXa-2055-702.png',
            ],
            'publicity' => [
                'bg' => array_random($colors),
                'icon' => 'https://gw.alicdn.com/tfs/TB1EFNu4EY1gK0jSZFMXXaWcVXa-425-400.png',
            ],
            'square' => [
                'bg' => array_random($colors),
                'icon' => 'https://gw.alicdn.com/tfs/TB1Hp0i4AL0gK0jSZFtXXXQCXXa-400-400.png',
            ],
        ];
        $applicationsList = [
            [
                'title' => '课表查询',
                'route' => '/pages/timetable/timetable',
                'bg' => $icons['timetable']['bg'],
                'icon' => $icons['timetable']['icon'],
                'disabled' => false,
            ],
            [
                'title' => '成绩查询',
                'route' => '/pages/score/score',
                'bg' => $icons['score']['bg'],
                'icon' => $icons['score']['icon'],
            ],
            [
                'title' => '考试安排',
                'route' => '/pages/exam/exam',
                'bg' => $icons['exam']['bg'],
                'icon' => $icons['exam']['icon'],
            ],
            [
                'title' => '空教室',
                'route' => '/pages/freeroom/freeroom',
                'bg' => $icons['freeroom']['bg'],
                'icon' => $icons['freeroom']['icon'],
                'badge' => [
                    'type' => 'static',
                    'path' => '/index/freeroom',
                    'content' => '莫干山',
                ],
            ],
            // [
            //     'title' => '吐个槽',
            //     'module' => 'feedback',
            //     'bg' => $icons['square']['bg'],
            //     'icon' => $icons['square']['icon'],
            // ],
            // [
            //     'title' => '教师查询',
            //     'route' => '/pages/teacher/teacher',
            //     'bg' => $icons['teacher']['bg'],
            //     'icon' => $icons['teacher']['icon'],
            //     'disabled' => true,
            // ],
            [
                'title' => '一卡通',
                'route' => '/pages/card/card',
                'bg' => $icons['card']['bg'],
                'icon' => $icons['card']['icon'],
            ],
            [
                'title' => '借阅信息',
                'route' => '/pages/borrow/borrow',
                'bg' => $icons['borrow']['bg'],
                'icon' => $icons['borrow']['icon'],
            ],
            [
                'title' => '问卷调研',
                'appId' => 'wxd947200f82267e58',
                'path' => 'pages/wjxqList/wjxqList?activityId=101523577',
                'bg' => $icons['questionnaire']['bg'],
                'icon' => $icons['questionnaire']['icon'],
                'badge' => [
                    'type' => 'simple',
                    'path' => '/index/questionnaire',
                    'clearPath' => '/index/questionnaire',
                    'content' => '莫干山',
                ],
            ],
            // [
            //     'title' => '志愿者',
            //     'url' => url('/wechat/view') . '?url=' . urlencode('http://zhangyx.cn/VMS/WechatTimeResult.do?id={uno}'),
            //     'bg' => 'red',
            //     // 'icon' => $icons['publicity']['icon'],
            // ],
            // [
            //     'title' => '失物招领',
            //     'bg' => 'green',
            //     'url' => 'https://boomerang.zhutianyu.top',
            // ],
            // [
            //     'title' => '氢动简学车',
            //     'bg' => 'yellow',
            //     'icon' => cdn('img/app-list/qingdong.png', false),
            //     'path' => '/pages/index/index?codefrom=wejh_jxhtech',
            //     'appId' => 'wxb5abdabfd36aca7a'
            // ],
            // [
            //     'title' => '街能换电',
            //     'bg' => 'blue',
            //     'icon' => cdn('img/app-list/jineng.png', false),
            //     'appId' => 'wx43b9394cba2c7241'
            // ],
            // [
            //     'title' => '绑定服务号',
            //     'bg' => 'red',
            //     'url' => url('/oauth/wechat/loginByUno') . '?uno=' . urlencode('{uno}'),
            // ],
        ];
        return [
            'icons' => $icons,
            'app-list' => $applicationsList
        ];
    }

}
