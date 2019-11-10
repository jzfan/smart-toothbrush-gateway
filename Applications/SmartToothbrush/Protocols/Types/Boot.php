<?php

namespace Protocols\Types;

class Boot extends PackageTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H2position/H2press';
    }

    public function handleData($data, $db)
    {
        $_SESSION['mac'] = $data['mac'];
        $_SESSION['seq'] = $data['seq'];
        $_SESSION['position'] = $data['position'];
        $_SESSION['press'] = $data['press'];
    }
}
