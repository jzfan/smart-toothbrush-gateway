<?php

namespace Protocols\Receiver;

class ClientStatus extends ReceiverTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H2switch/H2runSeconds/H2power';
    }

    public function handleData($data, $db)
    {
        $db->insert('hh_toothbrush_run')
            ->cols([
                'mac' => $data['mac'],
                'switch' => $data['switch'],
                'seconds' => hexdec($data['runSeconds']),
                'power' => hexdec($data['power']),
                'add_time' => time()
            ])->query();
    }
}
