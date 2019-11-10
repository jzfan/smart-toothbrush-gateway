<?php

namespace Protocols\Types;

class Fallback extends PackageTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H*data';
    }

    public function handleData($data, $db)
    {
        if ($data['code'] == '06') {
            var_export($data);
        }
        var_export($data['code']);
    }
}
