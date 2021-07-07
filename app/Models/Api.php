<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mockery\Exception;

class Api extends Model
{
    /**
     * 这边是获取外部api的数据
     * 一般列表数据的返回请规范为
     *  [
     *      'list' => $list
     *  ]
     *  也就是另外包一层，以便以后增加新的信息
     *  另外列表数据即使字段相同也要重新转换一遍，一般转换为中文以便理解
     */
    public $error = '';

    public function setError($message) {
        $this->error = $message;

        return false;
    }

    public function getError() {
        return $this->error;
    }

    public function resetError() {
        $this->error = '';
    }

    public function getYcData(...$arg) {
        $function = array_shift($arg);
        $port = setting('ycjw_port');
        $port = $port == '0' ? null : $port;
        switch ($function) {
            case 'score':
                $result = $this->getYcScore($arg[0], $arg[1], $arg[2], $port, 500);
                if(!$result && $this->getError() == '原创服务器错误') {
                    addYcjwPortError($port);
                    resetCurrentYcjwPort();
                    return false;
                }
                return $result;
                break;
            case 'timetable':
                $result = $this->getYcClass($arg[0], $arg[1], $arg[2], $port, 500);
                if(!$result && $this->getError() == '原创服务器错误') {
                    addYcjwPortError($port);
                    resetCurrentYcjwPort();
                    return false;
                }
                return $result;
                break;
            case 'exam':
                $result = $this->getYcExam($arg[0], $arg[1], $arg[2], $port, 500);
                if(!$result && $this->getError() == '原创服务器错误') {
                    addYcjwPortError($port);
                    resetCurrentYcjwPort();
                    return false;
                }
                return $result;
                break;
            default:
                return false;
        }
    }

    /**
     * 精弘用户中心登录验证
     *
     * @param string
     * @param string
     * @return boolean
     */
    public function checkJhPassport($user_name, $password) {
        if (!$user_name OR !$password) {
            return $this->setError('用户名或密码为空');
        }
        if (strstr($password, '../') != false) {
            return $this->setError('密码不允许带../');
        }
        $url = api('jh.user', null);
        $data = [
            'app' => 'passport',
            'action' => 'login',
            'passport' => $user_name,
            'password' => ($password),
        ];
        if(!$content = http_get($url, $data))
            return $this->setError('用户中心服务器错误');
        if(!$value = json_decode($content, true)) {
            return $this->setError('用户中心服务器错误');
        }
        if(isset($value['state']) && $value['state'] == 'success') {
            return true;
        } else {
            return $this->setError('用户名或密码错误');
        }
    }

    /**
     * 重置精弘通行证
     *
     * @param string
     * @param string
     * @param string
     * @return mixed
     */
    public function resetJhPassport($user_name, $password, $iid) {
        if (!$user_name OR !$password OR !$iid) {
            return $this->setError('参数错误');
        }
        $url = api('jh.user', null);
        $data = [
            'app' => 'passport',
            'action' => 'reset',
            'passport' => $user_name,
            'password' => ($password),
            'iid' => $iid,
        ];

        if(!$content = http_get($url, $data)) {
            return $this->setError('用户中心服务器错误');
        }
        if(!$value = json_decode($content, true)) {
            return $this->setError('用户中心服务器错误');
        }
        if(isset($value['state']) && $value['state'] == 'success') {
            return true;
        } else {
            return $this->setError(isset($value['info']) ? $value['info'] : '重置凭证不正确');
        }
    }

    /**
     * 激活精弘通行证
     *
     * @param string
     * @param string
     * @param string
     * @param string
     * @return boolean
     */
    public function activeJhPassport($user_name, $password, $iid, $email)
    {
        if (!$user_name OR !$password OR !$iid OR !$email) {
            return $this->setError('参数错误');
        }

        $url = api('jh.user', null);
        $data = [
            'app' => 'passport',
            'action' => 'active',
            'username' => ($user_name),
            'password' => ($password),
            'iid' => ($iid),
            'email' => ($email),
        ];

        if(!$content = http_get($url, $data, 2000)) {
            return $this->setError('用户中心服务器错误');
        }
        if(!$value = json_decode($content, true)) {
            return $this->setError('用户中心服务器错误');
        }

        if(isset($value['state']) && $value['state'] == 'success') {
            return true;
        } else {
            if(isset($value['error']) && $value['error'] == "无返回数据") {
                return true;
            }
            return $this->setError(isset($value['info']) ? $value['info'] : $value['error']);
        }
    }

    /**
     * 原创登录验证
     *
     * @param string
     * @param string
     * @param integer
     * @param integer
     * @return boolean
     */
    public function checkYcLogin($user_name, $password, $port = null, $timeout = 500) {
        if (!$user_name OR !$password) {
            return $this->setError('用户名或密码为空');
        }
        if (strstr($password, '../') != false) {
            return $this->setError('密码不允许带../');
        }
        $check = $this->getYcClass($user_name, $password, null, $port, $timeout);
        if(!$this->getError()) {
            return true;
        }
        return false;
    }

    /**
     * 允许重试的获取教务系统数据的函数
     * @param $type
     * @param $username
     * @param $password
     * @param $term
     * @param $port
     * @param $retry
     * @param $timeout
     * @return bool
     */
    public function getUEASData($type, $username, $password, $term, $port, $retry, $timeout = null) {
        if ($term) {
            preg_match_all('/\d+/', $term, $pregResult);
            $year = intval($pregResult[0][0]);
            if ($year >= 2017) {
                // 正方查询逻辑
                $termNum = intval($pregResult[0][2]);
                switch ($termNum) {
                    case '1':
                        $term = 3;
                        break;
                    case '2':
                        $term = 12;
                        break;
                    case '3':
                        $term = 16;
                        break;
                    default:
                        return $this->setError('参数错误');
                }
                $func = 'get' . 'Zf' . ucwords($type);
                if (!$password['zf']) {
                    return $this->setError('请先绑定正方账号');
                }
                try {
                    $timeout = $timeout === null ? 5000 : $timeout;
                    return $this->$func($username, $password['zf'], $year, $term, $timeout);
                } catch (Exception $e) {
                    return $this->setError('方法不存在');
                }
            } else {
                // 原创查询逻辑
                $termNum = intval($pregResult[0][2]);
                if (!($termNum === 1 || $termNum === 2)) {
                    // 原创教务只允许查询上/下学期数据
                    if ($termNum === 3) {
                        return $this->setError('原创教务不支持短学期查询');
                    } else {
                        return $this->setError('参数错误');
                    }
                } 
                $timeout = $timeout === null ? 800 : $timeout;
                $func = 'get' . 'Yc' . ucwords($type);
                if (!$password['yc']) {
                    return $this->setError('请先绑定原创账号');
                }
                try {
                    $result = $this->$func($username, $password['yc'], $term, $port, $timeout);
                    if(!is_array($result) && !$retry) {
                        return $this->setError('原创服务器错误');
                    }
                    if(!is_array($result) && $retry) {
                        for ($i = 83; $i <= 86; $i++) {
                            $result = $this->$func($username, $password['yc'], $term, $i, $timeout);
                            if(is_array($result)) {
                                break;
                            }
                        }
                        if(!is_array($result)) {
                            return $this->setError('原创服务器错误');
                        }
                    }
                    return $result;
                } catch (Exception $e) {
                    return $this->setError('方法不存在');
                }
            }
        } else {
            return $this->setError('参数错误');
        }
    }

    /**
     * 原创成绩获取
     *
     * @param string
     * @param string
     * @param string
     * @param integer
     * @param integer
     * @return mixed
     */
    public function getYcScore($user_name, $password, $term = null, $port = null, $timeout = 500) {
        if (!$user_name OR !$password) {
            return $this->setError('用户名或密码为空');
        }
        if (strstr($password, '../') != false) {
            return $this->setError('密码不允许带../');
        }
        $url = api('ycjw.score', $port == null ? null : false);
        $data = [
            'username' => $user_name,
            'password' => $password,
            'timeout' => $timeout / 1000,
        ];

        if(!$port) {
            $url = api('ycjw.score', true);
        } else {
            $data['ip'] = $port;
        }
        if($term != null && $term != "") {
            $data['term'] = $term;
        }
        if(!$contents = http_get($url, $data, $timeout)) {
            return $this->setError('原创服务器错误');
        }
        // 处理掉偶尔出现的空白符
        $preg = '/{.*}/';
        preg_match_all($preg, $contents, $array);
        $arr = json_decode(array_get($array, '0.0', '{}'), true);

        if(!isset($arr['status'])) {
            return $this->setError('原创服务器错误');
        }
        if($arr['status'] != 'success') {
            if ($arr['msg'] === '服务器错误') {
                return $this->setError('原创服务器错误');
            }
            return $this->setError($arr['msg']);
        }
        if($arr['msg'] == "没有相关信息") {
            $arr['msg'] = [];
        }
        $score_list = [];
        //务必对做接受来的数据做一个转换
        foreach ($arr['msg'] as $key => $value) {
            $g = array();
            $g['学期']=$value['term'];
            $g['名称']=$value['name'];
            $g['考试性质']=$value['classprop'];
            $g['成绩']=$value['classscore'];
            $g['学时']=$value['classhuor'];
            $g['学分']=$value['classcredit'];
            array_push($score_list,$g);
        }
        $res = [
            'list' => $score_list,
            'gpa' => $this->getGpa($score_list)
        ];

        return $res;
    }

    /**
     * 正方成绩获取
     *
     * @param string
     * @param string
     * @param string
     * @param integer
     * @param integer
     * @return mixed
     */
    public function getZfScore($user_name, $password, $year = null, $term = null, $timeout = 500) {
        if (!$user_name OR !$password) {
            return $this->setError('用户名或密码为空');
        }
        if (strstr($password, '../') != false) {
            return $this->setError('密码不允许带../');
        }
        $url = api('zf.score', null);
        $data = [
            'username' => $user_name,
            'password' => $password,
            'year' => $year,
            'term' => $term,
            // 'ip' => '160',
            'timeout' => $timeout / 1000,
        ];
        if(!$contents = http_get($url, $data, $timeout)) {
            return $this->setError('正方服务器错误');
        }
        $arr = json_decode($contents, true);

        if(!isset($arr['status'])) {
            return $this->setError('正方服务器错误');
        }
        if($arr['status'] != 'success') {
            if ($arr['msg'] === '服务器错误') {
                return $this->setError('正方服务器错误');
            }
            return $this->setError($arr['msg']);
        }
        if($arr['msg'] == "没有相关信息") {
            $arr['msg'] = [];
        }
        $score_list = [];
        //务必对做接受来的数据做一个转换
        foreach ($arr['msg'] as $key => $value) {
            $g = array();
            $g['学期']=$value['term'];
            $g['名称']=$value['name'];
            $g['考试性质']=$value['classprop'];
            $g['成绩']=$value['classscore'];
            $g['学时']=$value['classhuor'];
            $g['学分']=$value['xf'];
            $g['绩点']=isset($value['jd'])?$value['jd']:'0';
            $g['考试性质']=isset($value['ksxz'])?$value['ksxz']:'';
            $g['课程性质名称']=isset($value['kcxzmc'])?$value['kcxzmc']:'';
            $g['课程归属名称']=isset($value['kcgsmc'])?$value['kcgsmc']:'';
            array_push($score_list,$g);
        }
        $res = [
            'list' => $score_list,
            'gpa' => $this->getGpa($score_list)
        ];

        return $res;
    }

    public function getZfGpa($score_list) {
        $sum = 0;
        $count = 0;
        foreach ($score_list as $key => $value) {
            if (isset($value['绩点']) && $value['绩点']) {
                if (!isset($value['考试性质']) || $value['考试性质'] == "公选课"|| $value['成绩'] == "取消"
                || (
                    isset($value['考试性质']) && (
                        $value['考试性质'] === '重修'
                        || $value['考试性质'] === '补考'
                    )
                )
                || (
                    isset($value['课程归属名称']) && $value['课程归属名称'] !== '个性化课程' && (
                        $value['课程归属名称'] && isset($value['课程性质名称']) && (
                            $value['课程性质名称'] === '任选课'
                        )
                    )
                )) {
                    continue;
                }
                $count++;
                $sum += $value['绩点'];
            }
        }
        if ($count === 0) {
            return 0;
        }
        return sprintf("%.3f", $sum / $count);
    }


    /**
     * 正方成绩明细获取
     *
     * @param string
     * @param string
     * @param string
     * @param integer
     * @param integer
     * @return mixed
     */
    public function getZfScoreDetail($user_name, $password, $year = null, $term = null, $timeout = 500) {
        if (!$user_name OR !$password) {
            return $this->setError('用户名或密码为空');
        }
        if (strstr($password, '../') != false) {
            return $this->setError('密码不允许带../');
        }
        $url = api('zf.scoreDetail', null);
        $data = [
            'username' => $user_name,
            'password' => $password,
            'year' => $year,
            'term' => $term,
            'timeout' => $timeout / 1000,
        ];
        if(!$contents = http_get($url, $data, $timeout)) {
            return $this->setError('正方服务器错误');
        }
        $arr = json_decode($contents, true);

        if(!isset($arr['status'])) {
            return $this->setError('正方服务器错误');
        }
        if($arr['status'] != 'success') {
            if ($arr['msg'] === '服务器错误') {
                return $this->setError('正方服务器错误');
            }
            return $this->setError($arr['msg']);
        }
        if($arr['msg'] == "没有相关信息") {
            $arr['msg'] = [];
        }
        $score_list = [];
        //务必对做接受来的数据做一个转换
        foreach ($arr['msg'] as $key => $value) {
            $g = array();
            $g['学期']=$value['学期'];
            $g['名称']=$value['名称'];
            $g['成绩']=$value['成绩'];
            $g['成绩分项']=preg_replace('/\d+/', '**', $value['成绩分项']);
            $g['学分']=$value['学分'];
            // $g['绩点']=isset($value['jd'])?$value['jd']:'0';
            // $g['考试性质']=isset($value['ksxz'])?$value['ksxz']:'';
            // $g['课程性质名称']=isset($value['kcxzmc'])?$value['kcxzmc']:'';
            // $g['课程归属名称']=isset($value['kcgsmc'])?$value['kcgsmc']:'';

            if(preg_match('/期中|总评/', $value['成绩分项'])) {
                array_push($score_list,$g);
            }
        }
        $res = [
            'list' => $score_list
        ];

        return $res;
    }

    /**
     * 计算成绩绩点
     *
     * @param array
     * @return string
     */
    public function getGpa($score_list) {
        if (sizeof($score_list) == 0) {
            return 0;
        }
        if ($score_list == null || !is_array($score_list)) {
            return 0;
        }
        $zcj = 0;
        $zxf = 0;
        foreach ($score_list as $key => $value) {
            if(!isset($value['考试性质']) || $value['考试性质']=="公选课"|| $value['成绩'] == "取消"
                || (
                    isset($value['考试性质']) && (
                        $value['考试性质'] === '重修'
                        || $value['考试性质'] === '补考'
                    )
                )
                || (
                    isset($value['课程归属名称']) && $value['课程归属名称'] !== '个性化课程' && (
                        $value['课程归属名称'] && isset($value['课程性质名称']) && (
                            $value['课程性质名称'] === '任选课'
                        )
                    )
                )
                || (
                    isset($value['课程归属名称']) && (
                        $value['课程归属名称'] === '科学素养' ||
                        $value['课程归属名称'] === '人文情怀' ||
                        $value['课程归属名称'] === '社会责任' ||
                        $value['课程归属名称'] === '国际视野' ||
                        $value['课程归属名称'] === '新生研讨课' ||
                        $value['课程归属名称'] === '创新创业'
                    )
                )
            )
                continue;
            if (array_get($value, '名称', null) === '党的基本知识') {
                continue;
            }
            if(!isset($value['学分']) || !is_numeric($value['学分']) || $value['成绩'] == "免修" || $value['成绩'] == "缓考")
                continue;
            $b = $value['成绩'];
            if(!is_numeric($b)) {
                switch($b) {
                    case "优秀":
                        $b = 4.5;
                        break;
                    case "良好":
                        $b = 3.5;
                        break;
                    case "中等":
                    case "合格":
                        $b = 2.5;
                        break;
                    case "及格":
                        $b = 1.5;
                        break;
                    case "通过":
                        $b = 1;
                        break;
                    default:
                        $b = 0;
                }
            } else {
                if ($b <= 5 && $b > 0) {
                } else {
                    $b = 60 <= $b ? ($b - 50) / 10 : 0;
                }
            }
            $zcj += $b * $value['学分'];
            $zxf += $value['学分'];
        }
        if($zxf == 0) {
            return 0;
        }
        return sprintf("%.3f", $zcj / $zxf);
    }

    /**
     * 原创课表获取
     *
     * @param string
     * @param string
     * @param string
     * @param integer
     * @param integer
     * @return mixed
     */
    public function getYcClass($user_name, $password, $term = null, $port = null, $timeout = 500) {
        if (!$user_name OR !$password) {
            return $this->setError('用户名或密码为空');
        }
        if (strstr($password, '../') != false) {
            return $this->setError('密码不允许带../');
        }

        $url = api('ycjw.class', $port == null ? null : false);
        $data = [
            'username' => $user_name,
            'password' => $password,
            'timeout' => $timeout / 1000,
        ];
        if(!$port) {
            $url = api('ycjw.class', true);
        } else {
            $data['ip'] = $port;
        }
        if($term != null && $term != "") {
            $data['grade'] = $term;
        }

        $m_time = explode(' ',microtime());//开始时间
        $start_time = $m_time[1] + $m_time[0];

        $contents = http_get($url, $data, $timeout);

        $m_time = explode(' ',microtime());//结束时间
        $end_time = $m_time[1] + $m_time[0];
        $pending_time = $end_time - $start_time;//持续时间

        if(!$contents) {
            return $this->setError('原创服务器错误');
        }
        //防止偶尔出现的空字符导致的解析失败
        $preg = '/{.*}/';
        preg_match_all($preg, $contents, $array);

        if(!$arr = json_decode($array[0][0], true)) {
            return $this->setError('原创服务器错误');
        }
        if(!isset($arr['status'])) {
            return $this->setError('原创服务器错误');
        }
        if($arr['status'] != 'success') {
            if ($arr['msg'] === '服务器错误') {
                return $this->setError('原创服务器错误');
            }
            return $this->setError($arr['msg']);
        }
        if($arr['msg'] == "没有相关信息") {
            $arr['msg'] = [];
        }
        if(!is_array($arr['msg'])) {
            return $this->setError('原创服务器错误');
        }
        $class_list = [];
        foreach ($arr['msg'] as $key => $value) {
            $g = array();
            $g['课程名称'] = trim($value['name']);
            $g['开课学院'] = trim($value['collage']);
            $g['课程信息'] = trim($value['classinfo']);
            $g['课程类型'] = trim($value['classtype']);
            $g['学时'] = trim($value['classhuor']);
            $g['学分'] = trim($value['classscore']);
            $g = $this->fixYcClass($g);
            array_push($class_list,$g);
        }
        return [
            'pending_time' => $pending_time,
            'list' => $class_list,
        ];
    }

    /**
     * 正方课表获取
     *
     * @param string
     * @param string
     * @param string
     * @param integer
     * @param integer
     * @return mixed
     */
    public function getZfClass($user_name, $password, $year, $term = null, $timeout = 500) {
        if (!$user_name OR !$password) {
            return $this->setError('用户名或密码为空');
        }
        if (strstr($password, '../') != false) {
            return $this->setError('密码不允许带../');
        }

        $url = api('zf.class', null);
        $data = [
            'username' => $user_name,
            'password' => $password,
            'year' => $year,
            'term' => $term,
            'timeout' => $timeout / 1000,
        ];

        $m_time = explode(' ',microtime());//开始时间
        $start_time = $m_time[1] + $m_time[0];

        $contents = http_get($url, $data, $timeout);

        $m_time = explode(' ',microtime());//结束时间
        $end_time = $m_time[1] + $m_time[0];
        $pending_time = $end_time - $start_time;//持续时间

        if(!$contents) {
            return $this->setError('正方服务器错误');
        }

        if(!$arr = json_decode($contents, true)) {
            return $this->setError('正方服务器错误');
        }
        if(!isset($arr['status'])) {
            return $this->setError('正方服务器错误');
        }
        if($arr['status'] != 'success') {
            if ($arr['msg'] === '服务器错误') {
                return $this->setError('正方服务器错误');
            }
            return $this->setError($arr['msg']);
        }
        if($arr['msg'] == "没有相关信息") {
            $arr['msg'] = [];
        }
        if(!is_array($arr['msg'])) {
            return $this->setError('原创服务器错误');
        }
        $class_list = [];
        foreach ($arr['msg'] as $key => $value) {
            $g = array();
            $g['课程名称'] = trim($value['name']);
            $g['开课学院'] = trim($value['collage']);
            $g['课程信息'] = trim($value['classinfo']);
            $g['课程类型'] = trim($value['classtype']);
            $g['学时'] = trim($value['classhuor']);
            $g['学分'] = trim($value['classscore']);
            $g = $this->fixYcClass($g);
            array_push($class_list,$g);
        }
        return [
            'pending_time' => $pending_time,
            'list' => $class_list,
        ];
    }

    /**
     * 原创课程处理
     *
     * @param array
     * @return array
     */
    public function fixYcClass($class_info) {
        $preg = '/[^:]+/';
        preg_match_all($preg, $class_info['课程名称'], $arr);
        $class_info['名称'] = $arr[0][0];
        if(isset($arr[0][1]) && !empty($arr[0][1]))
            $class_info['老师'] = $arr[0][1];
        else
            $class_info['老师'] = "";
        $preg = '/(\d+)[-]?(\d+)?周?\(?.+\)?:星期\d\(\d\d?-\d\d?\)([^;]+|[^;]?)/';
        preg_match_all($preg, $class_info['课程信息'], $arr);
        $class_info['信息'] = array();
        foreach ($arr[0] as $key => $val) {
            $preg = '/(\d+)[-]?(\d+)?周?\(?([^):]+)?\)?/';
            preg_match_all($preg, $val, $matches);
            foreach ($matches[0] as $m => $match) {
                $preg = '/(\d+)[-]?(\d+)?周?\(?([^):]+)?\)?/';
                preg_match_all($preg, $match, $array);
                if (!$array || !$array[0]) {
                    continue;
                }
                $one = array();
                $one['周'] = $array[0][0];
                if(isset($array[1][0]) && !empty($array[1][0]))
                {
                    $one['开始周'] = $array[1][0];
                    $one['结束周'] = $array[2][0] ? $array[2][0] : $array[1][0];
                }
                else
                {
                    $one['开始周'] = $one['周'];
                    $one['结束周'] = $one['周'];
                }

                if(isset($array[3][0]) && !empty($array[3][0])) {
                    $one['周类型'] = $array[3][0] === '单' ? 'odd' : ($array[3][0] === '双' ? 'even' : 'default');
                } else {
                    $one['周类型'] = 'default';
                }
                $preg = '/星期(\d)/';
                preg_match_all($preg, $val, $array);
                $one['星期'] = $array[1][0];
                $preg = '/\((\d+)/';
                preg_match_all($preg, $val, $array);
                $one['开始节'] = $array[1][0];
                $preg = '/(\d+)\)/';
                preg_match_all($preg, $val, $array);
                $one['结束节'] = $array[1][0];
                $preg = '/\)\s([^;]*)/';
                preg_match_all($preg, $val, $array);
                if(isset($array[1][0]) && !empty($array[1][0]))
                {
                    $one['地点'] = $array[1][0];
                }
                else
                {
                    $one['地点'] = "";
                }
                $info_pieces = explode(',', $one['周']);
                if (isset($info_pieces[1])) {
                    foreach ($info_pieces as $k => $piece) {
                        $piece_info = $one;
                        $piece_info['周'] = $piece;
                        $preg = '/(\d+)[-]?(\d+)?周?\(?([^):]+)?\)?/';
                        preg_match_all($preg, $piece, $array);
                        if(isset($array[1][0]) && !empty($array[1][0]))
                        {
                            $piece_info['开始周'] = $array[1][0];
                            $piece_info['结束周'] = $array[2][0] ? $array[2][0] : $array[1][0];
                        }
                        else
                        {
                            $piece_info['开始周'] = $piece_info['周'];
                            $piece_info['结束周'] = $piece_info['周'];
                        }
                        array_push($class_info['信息'], $piece_info);
                    }
                } else {
                    array_push($class_info['信息'], $one);
                }
            }
        }
        return $class_info;
    }

    /**
     * 原创排考获取
     *
     * @param string
     * @param string
     * @param string
     * @param integer
     * @param integer
     * @return mixed
     */
    public function getYcExam($user_name, $password, $term = null, $port = null, $timeout = 1000) {
        if (!$user_name OR !$password) {
            return $this->setError('用户名或密码为空');
        }
        if (strstr($password, '../') != false) {
            return $this->setError('密码不允许带../');
        }

        $url = api('ycjw.exam', $port == null ? null : false);
        $data = [
            'username' => $user_name,
            'password' => $password,
            'timeout' => $timeout / 1000,
        ];
        if(!$port) {
            $url = api('ycjw.exam', true);
        } else {
            $data['ip'] = $port;
        }
        if($term != null && $term != "") {
            $data['term'] = $term;
        }

        if(!$contents = http_get($url, $data, $timeout)) {
            return $this->setError('原创服务器错误');
        }

        //去除偶尔出现的空白字符
        $preg = '/{.*}/';
        preg_match_all($preg, $contents, $array);
        if(!$arr = json_decode($array[0][0], true)) {
            return $this->setError('原创服务器错误');
        }

        if(!isset($arr['status'])) {
            return $this->setError('原创服务器错误');
        }
        if($arr['status']!='success' && $arr['msg'] == "用户名或密码错误") {
            return $this->setError('用户名或密码错误');
        } else if($arr['status']!='success') {
            if ($arr['msg'] === '服务器错误') {
                return $this->setError('原创服务器错误');
            }
            return $this->setError($arr['msg']);
        }
        if($arr['msg'] == "没有相关信息") {
            $arr['msg'] = [];
        }

        $exam_list = [];
        foreach ($arr['msg'] as $key => $value) {
            $g = array();
            $g['班级'] = $value['班级'];
            $g['教师'] = $value['教师'];
            $g['课程'] = $value['课程'];
            $g['日期'] = $value['日期'];
            $g['时段'] = $value['时段'];
            $g['教室'] = $value['教室'];
            $g = $this->fixYcExam($g);

            $now = date('Y-m-d');
            $exam_time = strtotime($g['日']);
            $now_time = strtotime($now);
            $between = ($exam_time - $now_time) / 3600 / 24;
            $g['倒计时'] = $between;

            if($between < 0) {
                $g['倒计时名'] = '已经过去' . -$between . '天';
            } else if ($between === 0){
                $g['倒计时名'] = '今天';
            } else {
                $g['倒计时名'] = '还有' . $between . '天';
            }

            array_push($exam_list, $g);
        }
        return [
            'term' => $term,
            'list' => $exam_list
        ];
    }



    /**
     * 正方排考获取
     *
     * @param string
     * @param string
     * @param string
     * @param integer
     * @param integer
     * @return mixed
     */
    public function getZfExam($user_name, $password, $year = null, $term = null, $timeout = 1200) {
        if (!$user_name OR !$password) {
            return $this->setError('用户名或密码为空');
        }
        if (strstr($password, '../') != false) {
            return $this->setError('密码不允许带../');
        }

        $url = api('zf.exam', null);
        $data = [
            'username' => $user_name,
            'password' => $password,
            'year' => $year,
            'term' => $term,
            // 'ip' => '160',
            'timeout' => $timeout / 1000,
        ];

        if(!$contents = http_get($url, $data, $timeout)) {
            return $this->setError('正方服务器错误');
        }

        $arr = json_decode($contents, true);

        if(!isset($arr['status'])) {
            return $this->setError('正方服务器错误');
        }
        if($arr['status']!='success' && $arr['msg'] == "用户名或密码错误") {
            return $this->setError('用户名或密码错误');
        } else if($arr['status']!='success') {
            if ($arr['msg'] === '服务器错误') {
                return $this->setError('正方服务器错误');
            }
            return $this->setError($arr['msg']);
        }
        if($arr['msg'] == "没有相关信息") {
            $arr['msg'] = [];
        }

        $exam_list = [];
        foreach ($arr['msg'] as $key => $value) {
            $g = array();
            $g['班级'] = $value['班级'];
            $g['教师'] = $value['教师'];
            $g['课程'] = $value['课程'];
            $g['日期'] = $value['日期'];
            $parg = '/(\d{4}-\d{2}-\d{2})\((\d{2}:\d{2}-\d{2}:\d{2})\)/';
            if (preg_match_all($parg, $value['日期'], $matches)) {
                $g['日期'] = $matches[1][0];
                $g['时段'] = $matches[2][0];
            } else {
                $g['时段'] = $values['时段'];
            }
            $g['时段'] = isset($g['时段']) ? $g['时段'].'（实际为准）' : '暂无考试时间';
            $g['教室'] = $value['教室'];
            $g['座位号'] = isset($value['zwh']) ? $value['zwh'] : '暂无座位号';
            $g['考试方式'] = isset($value['ksfs']) ? $value['ksfs'] : '暂无考试方式';
            $g['课程名称'] = $value['kcmc'];
            $g = $this->fixZfExam($g);

            $now = date('Y-m-d');
            $exam_time = strtotime($g['日']);
            if ($exam_time) {
                $now_time = strtotime($now);
                $between = ($exam_time - $now_time) / 3600 / 24;
                $g['倒计时'] = $between;

                if($between < 0) {
                    $g['倒计时名'] = '已经过去' . -$between . '天';
                } else {
                    $g['倒计时名'] = '还有' . $between . '天';
                }
            } else {
                $g['倒计时'] = '未知';
                $g['倒计时名'] = '未知';
            }

            array_push($exam_list, $g);
        }
        return [
            'term' => $term,
            'list' => $exam_list
        ];
    }

    /**
     * 正方排考处理
     *
     * @param array
     * @return array
     */
    public function fixZfExam($exam) {
        $exam['日'] = $exam['日期'];
        $exam['星期'] = date('w', strtotime($exam['日期']));
        $day_list = ['日', '一', '二', '三', '四', '五', '六', '日'];
        if (strtotime($exam['日期'])) {
            $exam['星期名'] = $day_list[intval($exam['星期'])];
        } else {
            $exam['星期名'] = '未知';
        }

        // $exam['教师'] = $exam['教师'] ? $exam['教师'] : $exam['课程名称'];
        $exam['教室'] = $exam['教室'] . ' - 座位号: ' . $exam['座位号'];

        return $exam;
    }

    /**
     * 原创排考处理
     *
     * @param array
     * @return array
     */
    public function fixYcExam($exam) {
        $arr = explode(' ', $exam['日期']);
        $exam['日'] = isset($arr[0])?$arr[0]:'';
        if(isset($arr[1])) {
            preg_match('/(\d+)/', $arr[1], $result);
            $exam['周'] = $result[1];
        } else {
            $exam['周'] = '';
        }
        if(isset($arr[2])) {
            preg_match('/(\d+)/', $arr[2], $result);
            $exam['星期'] = $result[1];
            $day_list = ['一', '二', '三', '四', '五', '六', '日'];
            $exam['星期名'] = $day_list[$result[1] - 1];
        } else {
            $exam['星期'] = '';
        }

        return $exam;
    }

    /**
     * 校园卡余额获取
     *
     * @param string
     * @param string
     * @return mixed
     */
    public function getCardBalance($user_name, $password, $timeout = 1500) {
        if (!$user_name OR !$password) {
            return $this->setError('账号错误');
        }

        $url = api('card.balance', null);
        $data = [
            'username' => $user_name,
            'password' => $password,
            'timeout' => $timeout / 1000,
        ];

        if(!$value = http_get($url, $data, $timeout)) {
            return $this->setError('服务器错误');
        }

        $arr = json_decode($value, true);
        if(!isset($arr['status']) || $arr['status'] != 'success') {
            return $this->setError('服务器错误');
        }

        $g = array();
        $g['姓名'] = $arr['msg']['余额']['姓名'];
        $g['卡余额'] = $arr['msg']['余额']['卡余额'];
        $g['今日账单'] = $this->getCardTodayRecords($arr['msg']['今日账单']);
        return $g;
    }
    /**
     * 校园卡当天记录获取
     *
     * @param array
     * @return mixed
     */
    public function getCardTodayRecords($arr) {
        if (!$arr) {
            return false;
        }
        $records_list = array();
        if($arr['num'] == 0) {
            return [];
        }
        foreach ($arr['msg'] as $key => $value) {
            $g = array();
            $g['流水号']=$value['流水号'];
            $g['账号']=$value['账号'];
            $g['卡片类型']=$value['卡片类型'];
            $g['交易类型']=$value['交易类型'];
            $g['商户']=html_entity_decode($value['商户']);
            $g['站点']=$value['站点'];
            $g['终端号']=$value['终端号'];
            $g['交易额']=$value['交易额'];
            $g['到账时间']=$value['到账时间'];
            $g['钱包名称']=$value['钱包名称'];
            $g['卡余额']=$value['卡余额'];
            array_push($records_list,$g);
        }
        return $records_list;
    }

    /**
     * 空教室获取
     *
     * @param string
     * @param string
     * @return mixed
     */
    public function getFreeRoom($username, $password, $term, $area, $startTime, $endTime, $weekday, $week, $timeout = 5000) {
        if (!$area or $startTime == null or $endTime == null or !$weekday or !$week) {
            return $this->setError('参数错误');
        }
        $termNumArr = array(
            '1' => '3',
            '2' => '12',
            '短' => '16'
        );
        preg_match_all('/\d+/', $term, $pregResult);
        $year = intval($pregResult[0][0]);
        $termNum = $termNumArr[intval($pregResult[0][2])];

        $lessons = 0;
        for ($i = intval($startTime); $i <= intval($endTime); $i++) {
            $lessons += pow(2, $i);
        }

        $weeks = pow(2, intval($week) - 1);

        $url = api('zf.freeroom', null);
        $data = [
            'username' => $username,
            'password' => $password,
            'year' => intval($year),
            'term' => intval($termNum),
            'area' => $area,
            'weekdays' => $weekday,
            'weeks' => $weeks,
            'lessons' => $lessons,
            'timeout' => $timeout / 1000,
        ];
        $contents = http_get($url, $data, $timeout);
        if(!$contents) {
            return $this->setError('正方服务器错误');
        }
        $arr = json_decode($contents, true);

        if($arr['status'] != 'success') {
            return $this->setError($arr['msg']);
        } else if($arr['status'] != 'success') {
            return $this->setError('正方服务器错误');
        }
        if($arr['msg'] == "没有相关信息")
        {
            $arr['msg'] = [];
        }

        $room_list = [];

        foreach ($arr['msg'] as $key => $value) {
            $g = array();
            $g['校区名称']=$value['校区名称'];
            $g['区域名称']=$value['区域名称'];
            $g['教室名称']=$value['教室名称'];
            $g['教室类型名称']=$value['教室类型名称'];
            $g['容量']=$value['容量'];
            $g['托管部门']=isset($value['jgmc']) ? $value['jgmc'] : '';
            $g['使用部门']=isset($value['sydxmc']) ? $value['sydxmc'] : '';
            $g['使用班级']=isset($value['sybj']) ? $value['sybj'] : '';
            $g['场地借用类型']=isset($value['cdjylx']) ? $value['cdjylx'] : '';

            array_push($room_list,$g);
        }

        return [
            'list' => $room_list,
        ];
    }

    /**
     * 关键词检索图书列表获取
     *
     * @param string
     * @param integer
     * @param integer
     * @return mixed
     */
    public function getBookSearch($wd, $page = null, $timeout = 1000) {
        if (!$wd) {
            return $this->setError('关键词为空');
        }

        $url = api('library.search', null);
        $data = [
            'wd' => $wd,
            'timeout' => $timeout / 1000,
        ];
        if($page) {
            $data['page'] = $page;
        }
        if(!$value = http_get($url, $data, $timeout)) {
            return $this->setError('服务器错误');
        }
        $arr = json_decode($value, true);
        if($arr['status'] != 'success') {
            return $this->setError($arr['msg']);
        }

        if($arr['msg'] == '没有相关信息') {
            $arr['msg'] = [
                'wd' => $wd,
                'page' => 1,
                'num' => 0,
                'list' => [],
            ];
        }

        return [
            'wd' => $wd,
            'page' => $arr['page'],
            'num' => intval($arr['num']),
            'list' => $arr['book_list'],
        ];
    }

    /**
     * 通过书本id获取书本详情
     *
     * @param number
     * @param number
     * @return mixed
     */
    public function getBookInfo($id, $timeout = 1000) {
        if (!$id) {
            return $this->setError('书id为空');
        }
        $url = api('library.book', null);
        $data = [
            'id' => $id,
            'timeout' => $timeout / 1000,
        ];

        if(!$value = http_get($url, $data, $timeout)) {
            return $this->setError('服务器错误');
        }
        $arr = json_decode($value,true);
        if($arr['status'] != 'success') {
            return $this->setError('服务器错误');
        }

        if($arr['msg'] == '没有相关信息') {
            return [
                'book_info' => null
            ];
        }

        return [
            'book_info' => [
                '封面' =>$arr['msg']['cover_iframe'],
                '书名' =>$arr['msg']['title'],
                '系列' =>$arr['msg']['series'],
                '作者' =>$arr['msg']['author'],
                'ISBN' =>$arr['msg']['ISBN'],
                '索书号' =>$arr['msg']['call_number'],
                '中图分类' =>$arr['msg']['call_type'],
                '价格' =>$arr['msg']['price'],
                '出版地' =>$arr['msg']['publish_location'],
                '主题词' =>$arr['msg']['topic'],
                '类型' =>$arr['msg']['type'],
                '出版时间' =>$arr['msg']['publish_date'],
                '出版社' =>$arr['msg']['publisher'],
            ]
        ];
    }

    /**
     * 查找图书借阅情况
     *
     * @param string
     * @param string
     * @param string
     * @param string
     * @param integer
     * @return mixed
     */
    public function getBookBorrow($username, $password, $action = null, $session = null, $timeout = 500) {
        if (!$username) {
            return $this->setError('账号错误');
        }
        if(!$password) {
            $password = $username;
        }
        $url = api('library.borrow', null);
        $data = [
            'username' => $username,
            'password' => $password,
            'timeout' => $timeout / 1000,
        ];
        if($session)
        {
            $data['session'] = $session;
        }
        if($action)
        {
            $data['action'] = $action;
        }
        if(!$value = http_get($url, $data, $timeout)) {
            return $this->setError('服务器错误');
        }

        $arr = json_decode($value,true);
        if($arr['status']!='success') {
            return $this->setError('服务器错误');
        }

        $list = [];
        foreach ($arr['msg']['borrow_list'] as $key => $value) {
            $borrow = [];
            $borrow['书名'] = $value['title'];
            $borrow['馆藏号'] = $value['collection_code'];
            $borrow['馆藏地'] = $value['collection_address'];
            $borrow['借书时间'] = $value['borrow_date'];
            $borrow['借书日期'] = date('Y-m-d', strtotime($value['borrow_date']));
            $borrow['应还日期'] = $value['return_date'];
            $borrow['续借次数'] = $value['renew'];
            $borrow['超期情况'] = $value['status'];
            $borrow['超期天数'] = intval($value['status']) ? intval($value['status']) : 0;
            array_push($list, $borrow);
        }

        return [
            'borrow_list' => $list,
            'session' => $arr['msg']['session'],
            'borrow_num' => $arr['msg']['borrow_num'], // 现借
            'overdue' => $arr['msg']['overdue'], // 超期
            'debt' => $arr['msg']['debet'], // 前框
        ];
    }
}
