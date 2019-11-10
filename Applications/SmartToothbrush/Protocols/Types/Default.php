<?php

namespace Protocols\Types;

class Fallback extends PackageTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/C12mac/H*data';
    }
}
