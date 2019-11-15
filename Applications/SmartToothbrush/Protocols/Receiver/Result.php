<?php

namespace Protocols\Receiver;

use Services\ToothbrushingService;

class Result extends ReceiverTypes
{
    protected $last;
    protected $points;

    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H*result';
    }

    public function handleData($data, $db)
    {
        $_SESSION['mac'] = $data['mac'];
        $_SESSION['result'] = $data['result'];
        $_SESSION['seq'] = $data['seq'];
        $_SESSION['close'] = 1;

        $this->points = ToothbrushingService::getPoints($_SESSION['result']);
        $this->updateOrCreateTotal($db, $data['mac']);
        // dump('points', $this->points);
        $this->last = $this->getLastDataToday($db, $data['mac']);

        if ($_SESSION['seq'] > 0 || !hasSession(['mac', 'result'])) {
            return;
        }
        if ($this->shouldCreate($db, $data['mac'])) {
            $this->createResult($db);
            // \dump('created');
            return;
        }
        if ($this->shouldUpdate()) {
            $this->updateResult($db);
            // \dump('updated');
        }
    }

    protected function shouldCreate($db, $mac)
    {
        if (!$this->last) {
            return true;
        }
        // var_export('count : ' . $this->countToday($db, $mac) . "\n");
        if (!$this->isIn6Hours() && $this->countToday($db, $mac) < 2) {
            return true;
        }
        return false;
    }

    protected function countToday($db, $mac)
    {
        $today_begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        return $db->select('count(*) as count')->from('hh_toothbrushing_result')
            ->where("add_time > $today_begin ")
            ->where("mac='" . $mac . "'")
            ->single();
    }

    protected function shouldUpdate()
    {
        // \dump('in 6 hours?', $this->isIn6Hours() ? 1 : 0);
        return $this->isIn6Hours() && $this->last['points'] < $this->points;
    }

    protected function isIn6Hours()
    {
        return time() - $this->last['add_time'] < 6 * 3600;
    }

    protected function getLastDataToday($db, $mac)
    {
        $today_begin = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        return $db->select('*')->from('hh_toothbrushing_result')
            ->where("mac='" . $mac . "'")
            ->where("add_time > $today_begin ")
            ->orderByDESC(['id'])
            ->row();
    }

    protected function updateResult($db)
    {
        return $db->update('hh_toothbrushing_result')
            ->cols([
                'result' => $_SESSION['result'],
                'mac' => $_SESSION['mac'],
                'points' => $this->points,
                'add_time' => time()
            ])->where('id=' . $this->last['id'])
            ->query();
    }

    protected function createResult($db)
    {
        return $db->insert('hh_toothbrushing_result')
            ->cols([
                'result' => $_SESSION['result'],
                'mac' => $_SESSION['mac'],
                'points' => $this->points,
                'add_time' => time()
            ])->query();
    }

    protected function updateOrCreateTotal($db, $mac)
    {
        if ($_SESSION['seq'] != 0) {
            return;
        }
        $row = $db->select('*')
            ->from('hh_toothbrushing_result_total')
            ->where("mac='" . $mac . "'")
            ->row();
        \dump('row : ', $row);
        if (empty($row)) {
            $db->insert('hh_toothbrushing_result_total')
                ->cols([
                    'total' => $this->points,
                    'count' => 1,
                    'add_time' => time(),
                    'mac' => $mac
                ])->query();
            return;
        }
        $db->update('hh_toothbrushing_result_total')
            ->cols([
                'total' => $row['total'] + $this->points,
                'count' => $row['count'] + 1,
                'update_time' => time()
            ])->where("mac='" . $mac . "'")
            ->query();
    }
}
