<?php

namespace Protocols\Receiver;

class Fallback extends ReceiverTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2cmd/H2code/H2length/H24mac';
    }

    public function handleData($data, $db)
    {
        $type = PackageTypeFactory::getType($data['cmd']);

        if ($data['code'] == '06') {
            var_export($data);
        }
        var_export($data['code']);
    }
}
