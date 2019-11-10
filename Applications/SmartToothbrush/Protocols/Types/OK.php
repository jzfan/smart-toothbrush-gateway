<?php

namespace Protocols\Types;

class Fallback extends PackageTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2cmd/H2code/H2length/H24mac';
    }

    public function handleData($data, $db)
    {
        if ($data['code'] == '06') {
            var_export($data);
        }
        var_export($data['code']);
    }
}
