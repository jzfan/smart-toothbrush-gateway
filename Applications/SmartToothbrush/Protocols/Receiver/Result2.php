<?php

namespace Protocols\Receiver;

use Workerman\Http\Client;
use Services\ToothbrushingService;

class Result2 extends ReceiverTypes
{
    protected $last;
    protected $points;
    protected $db;
    protected $mac;
    protected $suid;
    protected $result;
    protected $time;
    protected $active;
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

        if ($this->exists()) {
            return false;
        }

        if ($this->suid) {
            // dump('suid', $this->suid);
            $this->last = $this->getLastDataToday();

            $this->updateOrCreateTotal();

            $this->isInShortTime = $this->isIn6Hours();

            $this->active = $this->shouldBeActive() ? 1 : 0;
            $id = $this->createResult();

            if ($this->shouldInactiveLast) {
                $this->inactiveLast();
            }

            $this->push($id);
        }
    }

    protected function shouldBeActive()
    {
        if (!$this->last) {
            return true;
        }
        if (!$this->isInShortTime && $this->countToday() < 2) {
            return true;
        }
        if ($this->isInShortTime && $this->last['points'] < $this->points) {
            $this->shouldInactiveLast = true;
            return true;
        }
        return false;
    }

    protected function countToday()
    {
        $today_begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        return $this->db->select('count(*) as count')->from('hh_toothbrushing_result')
            ->where("add_time > $today_begin ")
            ->where("mac='" . $this->mac . "'")
            ->single();
    }

    protected function isIn6Hours()
    {
        if (!$this->last) {
            return false;
        }
        return time() - $this->last['add_time'] < 6 * 3600;
    }

    protected function getLastDataToday()
    {
        $today_begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        return $this->db->select('*')->from('hh_toothbrushing_result')
            ->where("mac='" . $this->mac . "'")
            ->where("add_time > $today_begin ")
            ->orderByDesc(['add_time'])
            ->row();
    }

    protected function inactiveLast()
    {
        return $this->db->update('hh_toothbrushing_result')
            ->cols([
                'active' => 0
            ])->where('id=' . $this->last['id'])
            ->query();
    }

    protected function createResult()
    {
        $id = $this->db->insert('hh_toothbrushing_result')
            ->cols([
                'active' => $this->active,
                'result' => $this->result,
                'mac' => $this->mac,
                'points' => $this->points,
                'sub_user_id' => $this->suid,
                'add_time' => $this->time
            ])->query();
        // \dump('active : ' . $active);
        return $id;
    }

    protected function updateOrCreateTotal()
    {
        $row = $this->db->select('*')
            ->from('hh_toothbrushing_result_total')
            ->where("mac='" . $this->mac . "' and sub_user_id=" . $this->suid)
            ->row();
        if (empty($row)) {
            $running_days = $this->isToday($this->time) ? 1 : 0;
            $this->db->insert('hh_toothbrushing_result_total')
                ->cols([
                    'total' => $this->points,
                    'count' => 1,
                    'sub_user_id' => $this->suid,
                    'add_time' => $this->time,
                    'update_time' => $this->time,
                    'mac' => $this->mac,
                    'running_days' => $running_days,
                    'total_days' => 1,
                ])->query();
            return;
        }

        $data = [
            'total' => $row['total'] + $this->points,
            'count' => $row['count'] + 1,
            'update_time' => $this->time
        ];

        if ($this->isToday($this->time) && (!$this->last)) {
            $data['total_days'] = $row['total_days'] + 1;
            if ($this->didYestoday()) {
                $data['running_days'] = $row['running_days'] + 1;
            }
        }

        $this->db->update('hh_toothbrushing_result_total')
            ->cols($data)->where("mac='" . $this->mac . "'")
            ->query();
    }

    protected function push($id)
    {
        $http = new Client();
        $http->post(getenv('PUSH_HOST') . '/pusher/points/push', [
            'id' => $id,
            'mac' => $this->mac,
            'points' => $this->points
        ], function ($response) {
            var_dump($response->getStatusCode());
            echo $response->getBody();
        }, function ($exception) {
            echo $exception;
        });
    }

    protected function isToday($time)
    {
        $tomorrow = strtotime('tomorrow');
        $today = strtotime('today');
        return ($time >= $today) && ($time < $tomorrow);
    }

    protected function didYestoday()
    {
        $today_begin = \strtotime(date('Y-m-d', time()));
        $yestoday_begin = $today_begin - 3600 * 24;

        $count = $this->db->select('count(*) as count')->from('hh_toothbrushing_result')
            ->where("mac='" . $this->mac . "'")
            ->where("add_time > $yestoday_begin ")
            ->where("add_time < $today_begin ")
            ->single();

        return $count > 0;
    }
}
