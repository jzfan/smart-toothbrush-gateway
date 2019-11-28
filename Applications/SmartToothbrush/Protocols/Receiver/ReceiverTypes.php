<?php

namespace Protocols\Receiver;

use Protocols\Utils;
use Protocols\PackageHandlerFactory;

abstract class ReceiverTypes
{
    abstract public function getDecodeRule();

    abstract public function handleData($data, $db);

    public function getData($buffer)
    {
        $data = unpack($this->getDecodeRule(), $buffer);
        $data['mac'] = substr($buffer, 4, 12);
        return $data;
    }

    protected function replyOk($mac, $cmd)
    {
        $ok = PackageHandlerFactory::getSender(Utils::SERVER_OK);
        $ok->send($cmd, $mac);
        echo 'reply ok : ' . $cmd . "\n";
    }
}
