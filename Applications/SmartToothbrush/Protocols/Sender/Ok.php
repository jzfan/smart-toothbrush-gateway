<?php

namespace Protocols\Sender;

use Protocols\Utils;
use GatewayWorker\Lib\Gateway;

class Ok extends SenderTypes
{
    public function send($mac, $cmd)
    {
        return Gateway::sendToCurrentClient([
            'seq' => $this->getSeq($cmd),
            'length' => 12,
            'mac' => $mac,
            'code' => Utils::SERVER_OK
        ]);
    }
}
