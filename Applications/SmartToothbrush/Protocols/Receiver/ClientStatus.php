<?php

namespace Protocols\Receiver;

use GatewayWorker\Lib\Gateway;
use Protocols\Utils;

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

        $this->replyOk($data['mac'], Utils::CLIENT_STATUS);

        if ($data['switch'] == 0) {
            $_SESSION['state'] = 0;

            // \dump('close on client switch', $data['switch']);
            // $this->closeConnect($data['mac']);
        }
    }

    protected function closeConnect($mac)
    {
        $cid = Gateway::getClientIdByUid($mac)[0];
        Gateway::closeClient($cid);
    }
}
