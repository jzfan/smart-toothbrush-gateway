<?php

namespace Protocols\Types;

class QueryDate extends PackageTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac';
    }

    public function handleData($data, $db)
    { }
}
