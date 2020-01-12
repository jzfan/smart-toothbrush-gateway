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

    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H12date/H96result';
    }

    public function handleData($data, $db)
    {
        if ($data['seq']  > 1) {
            return;
        }
        $this->db = $db;
        $this->mac = $data['mac'];
        $this->result = $data['result'];
        $dt = \str_split($data['date'], 2);
        $this->time = strtotime("$dt[0]-$dt[1]-$dt[2] $dt[3]:$dt[4]:$dt[5]");
        $this->points = ToothbrushingService::getPoints($data['result']);

        $this->suid = $this->db->select('sub_user_id')->from('hh_user_toothbrush')
            ->where("mac='" . $data["mac"] . "'")
            ->orderByDESC(['id'])
            ->single();

        dump('result', $data);
        if ($this->suid) {
            // dump('suid', $this->suid);
            $this->updateOrCreateTotal();

            $this->last = $this->getLastDataToday();
            $active = $this->shouldBeActive() ? 1 : 0;
            $id = $this->createResult($active);
            $this->push($id);
        }
    }

    protected function shouldBeActive()
    {
        if (!$this->last) {
            return true;
        }
        if (!$this->isIn6Hours() && $this->countToday() < 2) {
            return true;
        }
        if ($this->isIn6Hours() && $this->last['points'] < $this->points) {
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
        return time() - $this->last['add_time'] < 6 * 3600;
    }

    protected function getLastDataToday()
    {
        $today_begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        return $this->db->select('*')->from('hh_toothbrushing_result')
            ->where("mac='" . $this->mac . "'")
            ->where("add_time > $today_begin ")
            ->orderByDesc(['id'])
            ->row();
    }

    // protected function updateResult()
    // {
    //     return $this->db->update('hh_toothbrushing_result')
    //         ->cols([
    //             'result' => $this->result,
    //             'mac' => $this->mac,
    //             'points' => $this->points,
    //             'add_time' => $this->time
    //         ])->where('id=' . $this->last['id'])
    //         ->query();
    // }

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
        if ($active === 1 && isset($this->last['id'])) {
            return $this->db->update('hh_toothbrushing_result')
                ->cols([
                    'active' => 0
                ])->where('id=' . $this->last['id'])
                ->query();
        }
        return $id;
    }

    protected function updateOrCreateTotal()
    {
        $row = $this->db->select('*')
            ->from('hh_toothbrushing_result_total')
            ->where("mac='" . $this->mac . "' and sub_user_id=" . $this->suid)
            ->row();
        if (empty($row)) {
            $this->db->insert('hh_toothbrushing_result_total')
                ->cols([
                    'total' => $this->points,
                    'count' => 1,
                    'sub_user_id' => $this->suid,
                    'add_time' => $this->time,
                    'update_time' => $this->time,
                    'mac' => $this->mac
                ])->query();
            return;
        }
        $this->db->update('hh_toothbrushing_result_total')
            ->cols([
                'total' => $row['total'] + $this->points,
                'count' => $row['count'] + 1,
                'update_time' => $this->time
            ])->where("mac='" . $this->mac . "'")
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
}
