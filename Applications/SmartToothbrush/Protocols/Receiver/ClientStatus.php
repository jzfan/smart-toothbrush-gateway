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
        $_SESSION['seconds'] = hexdec($data['runSeconds']);
        $_SESSION['power'] = hexdec($data['power']);
    }
}
