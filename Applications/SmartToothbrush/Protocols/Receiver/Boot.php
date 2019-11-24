<?php

namespace Protocols\Receiver;

class Boot extends ReceiverTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H2position/H2press';
    }

    public function handleData($data, $db)
    {
        $_SESSION['mac'] = $data['mac'];
        $_SESSION['seq'] = hexdec($data['seq']);
        $_SESSION['position'] = intval($data['position']);
        $_SESSION['press'] = intval($data['press']);
    }
}
