<?php

namespace Protocols\Receiver;

abstract class PackageTypes
{

    abstract public function getDecodeRule();

    abstract public function handleData($data, $db);

    public function getData($buffer)
    {
        $data = unpack($this->getDecodeRule(), $buffer);
        $data['mac'] = substr($buffer, 4, 12);
        return $data;
    }
}
