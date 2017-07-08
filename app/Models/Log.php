<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $guarded = [];
    public function getLogsByAction($action, $value = null, $start_time = null)
    {
        $db = Log::where('action', $action);
        if ($value !== null)
        {
            $db->where('value', $value);
        }
        if ($start_time)
        {
            $db->where('created_at', '>', $start_time);
        }
        $logs = $db->get();
        return $logs;
    }
    public function addLog($action, $value = 0, $note = "", $uid = 0)
    {
        $log = new Log;
        return $log::create([
            'action' => htmlspecialchars($action),
            'value' => intval($value),
            'note' => htmlspecialchars($note),
            'uid' => intval($uid),
        ]);
    }
    public function addYcjwPortError($port)
    {
        return $this->addLog('YCJW_PORT_ERROR', $port, "原创教务服务器错误或无响应");
    }
    public function resetCurrentYcjwPort()
    {
        $start_time = date('Y-m-d H:00:00', time());
        $min_log_count = count($this->getLogsByAction('YCJW_PORT_ERROR', 0, $start_time));
        $min_log_port = 0;
        for ($i = 83; $i < 87; $i++)
        {
            $log_count = count($this->getLogsByAction('YCJW_PORT_ERROR', $i, $start_time));
            if($min_log_count > $log_count)
            {
                $min_log_count = $log_count;
                $min_log_port = $i;
            }
        }
        (new SystemSetting)->setVars([
            'ycjw_port' => $min_log_port,
        ]);
        return $min_log_port;
    }
}
