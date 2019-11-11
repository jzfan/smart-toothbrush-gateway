<?php

namespace Protocols\Receiver;

use Protocols\Contract\CloseHandler;

class Result extends ReceiverTypes implements CloseHandler
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H*result';
    }

    public function handleData($data, $db)
    {
        $_SESSION['mac'] = $data['mac'];
        $_SESSION['result'] = $data['result'];
        $_SESSION['seq'] = $data['seq'];
    }

    public function handleClose($client_id, $db)
    {
        if ($this->shouldUpdate()) {
            $db->insert('hh_toothbrushing_result')->cols([
                'result' => $_SESSION['result'],
                'mac' => $_SESSION['mac'],
                'points' => 99,
                'add_time' => time()
            ])->query();
        }
    }

    protected function shouldUpdate()
    {
        if (hasSession(['mac', 'result']) && !hasSession(['update_result'])) {
            $_SESSION['update_result'] = true;
            return true;
        }
        return false;
    }
}
