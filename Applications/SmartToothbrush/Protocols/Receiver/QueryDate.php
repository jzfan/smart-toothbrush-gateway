<?php

namespace Protocols\Receiver;

use Protocols\PackageHandlerFactory;
use Protocols\Utils;

class QueryDate extends ReceiverTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac';
    }

    public function handleData($data, $db)
    {
        $setter = PackageHandlerFactory::getSender(Utils::SET_DATE);
        $setter->send($data['mac'], null);
    }
}
