<?php

namespace Protocols\Receiver;

class Result extends ReceiverTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H*result';
    }

    public function handleData($data, $db)
    {
        $_SESSION['mac'] = $data['mac'];
        $_SESSION['result'] = $data['result'];
    }
}
