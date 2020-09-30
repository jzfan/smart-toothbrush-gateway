<?php

namespace Protocols\Receiver;

use Workerman\Http\Client;
use Services\ToothbrushingService;

class Result2 extends ReceiverTypes
{
    protected $points;
    protected $db;
    protected $mac;
    protected $suid;
    protected $result;
    protected $time;
    protected $lastActive;
    protected $activeTodayCount;
    protected $isInShortTime;
    protected $shouldInactiveLast = false;

    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H12date/H96result';
    }

    public function initData($data, $db)
    {
        $this->db = $db;
        $this->mac = $data['mac'];
        $this->result = $data['result'];
        $dt = \str_split($data['date'], 2);
        $this->time = strtotime("$dt[0]-$dt[1]-$dt[2] $dt[3]:$dt[4]:$dt[5]");
        $this->points = ToothbrushingService::getPoints($data['result']);

        $this->suid = $db->select('sub_user_id')->from('hh_user_toothbrush')
            ->where("mac='" . $data["mac"] . "'")
            ->orderByDESC(['id'])
            ->single();

        $this->activeTodayCount = $this->countActiveToday();
    }

    public function exists()
    {
        \usleep(\rand(0, 2000000));

        $last = $this->db->select('add_time')->from('hh_toothbrushing_result')
            ->where("mac='" . $this->mac . "'")
            ->orderByDESC(['add_time'])
            ->single();

        return (time() - $last) < 10;
    }

    public function handleData($data, $db)
    {
        $this->initData($data, $db);

        if ($this->exists() || !$this->suid) {
            return false;
        }

        if (0 == $this->activeTodayCount) {
            $this->createResult(1);
            $this->updateOrCreateTotal();
            return;
        }

        $this->lastActive = $this->getLastActiveToday();

        if (1 == $this->activeTodayCount) {
            if ($this->isInOneHour() && $this->isPointsGreater()) {
                $this->inactiveLast();
                $this->createResult(1);

                $this->updateTotal($this->points);
                return;
            }
            if (!$this->isIn6Hours() && $this->isToday()) {
                $this->createResult(1);
                $this->updateTotal($this->points);
                return;
            }
        }

        if (2 == $this->activeTodayCount && $this->isPointsGreater()) {
            $this->inactiveLast();
            $this->createResult(1);

            $diff = \bcsub($this->points, $this->lastActive['points'], 2);
            $this->updateTotal($diff);
            return;
        }

        $this->createResult(0);
    }

    protected function isPointsGreater()
    {
        return $this->points > $this->lastActive['points'];
    }

    protected function countActiveToday()
    {
        $today_begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        return $this->db->select('count(*) as count')->from('hh_toothbrushing_result')
            ->where("add_time > $today_begin ")
            ->where("mac='" . $this->mac . "'")
            ->single();
    }

    protected function isIn6Hours()
    {
        return time() - $this->lastActive['add_time'] < 6 * 3600;
    }

    protected function isInOneHour()
    {
        return time() - $this->lastActive['add_time'] < 1 * 3600;
    }

    protected function getLastActiveToday()
    {
        $today_begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        return $this->db->select('*')->from('hh_toothbrushing_result')
            ->where("mac='" . $this->mac . "'")
            ->where("add_time > $today_begin ")
            ->where("active = 1 ")
            ->orderByDesc(['add_time'])
            ->row();
    }

    protected function inactiveLast()
    {
        return $this->db->update('hh_toothbrushing_result')
            ->cols([
                'active' => 0
            ])->where('id=' . $this->lastActive['id'])
            ->query();
    }

    protected function createResult($active)
    {
        $id = $this->db->insert('hh_toothbrushing_result')
            ->cols([
                'active' => $active,
                'result' => $this->result,
                'mac' => $this->mac,
                'points' => $this->points,
                'sub_user_id' => $this->suid,
                'add_time' => $this->time
            ])->query();
        // \dump('active : ' . $active);
        if (1 == $active) {
            $this->push($id);
        }
    }

    protected function updateOrCreateTotal()
    {
        $row = $this->getTotalRow();

        if (empty($row)) {
            $this->db->insert('hh_toothbrushing_result_total')
                ->cols([
                    'total' => $this->points,
                    'count' => 1,
                    'sub_user_id' => $this->suid,
                    'add_time' => $this->time,
                    'update_time' => $this->time,
                    'mac' => "'" . $this->mac . "'",
                    'running_days' => 1,
                    'total_days' => 1,
                ])->query();
        } else {
            $this->updateTotal($this->points, $row);
        }
    }

    protected function push($id)
    {
        $http = new Client();
        $http->post(getenv('PUSH_HOST') . '/pusher/points/push', [
            'id' => $id,
            'mac' => $this->mac,
            'points' => $this->points
        ], function ($response) {
            // var_dump($response->getStatusCode());
            echo $response->getBody();
        }, function ($exception) {
            echo $exception;
        });
    }

    protected function isToday()
    {
        $tomorrow = strtotime('tomorrow');
        $today = strtotime('today');
        return ($this->time >= $today) && ($this->time < $tomorrow);
    }

    protected function didYestoday()
    {
        $today_begin = \strtotime(date('Y-m-d', time()));
        $yestoday_begin = $today_begin - 3600 * 24;

        $count = $this->db->select('count(*) as count')
            ->from('hh_toothbrushing_result')
            ->where("mac='" . $this->mac . "'")
            ->where("add_time > $yestoday_begin ")
            ->where("add_time < $today_begin ")
            ->single();

        return $count > 0;
    }

    protected function updateTotal($incr, $row = null)
    {
        if (!$row) {
            $row = $this->getTotalRow();
        }

        $data = [
            'total' => bcadd($row['total'], $incr, 2),
            'count' => $row['count'] + 1,
            'update_time' => $this->time
        ];

        if ($this->isFirstActiveToday()) {
            $data['total_days'] = $row['total_days'] + 1;
            $data['running_days'] = $this->didYestoday() ? $row['running_days'] + 1 : 1;
        }

        $this->db->update('hh_toothbrushing_result_total')
            ->cols($data)
            ->where("sub_user_id=" . $this->suid)
            ->query();
    }

    protected function isFirstActiveToday()
    {
        return $this->isToday() && (!$this->lastActive);
    }

    protected function getTotalRow()
    {
        return $this->db->select('*')
            ->from('hh_toothbrushing_result_total')
            // ->where("mac='" . $this->mac . "' and sub_user_id=" . $this->suid)
            ->where("sub_user_id=" . $this->suid)
            ->row();
    }
}
