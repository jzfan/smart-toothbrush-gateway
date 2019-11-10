<?php

namespace Protocols\Receiver;

class QueryDate extends ReceiverTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac';
    }

    public function handleData($data, $db)
    { }
}
