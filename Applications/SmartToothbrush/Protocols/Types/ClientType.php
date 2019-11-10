<?php

namespace Protocols\Types;

class ClientType extends PackageTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H2model/H2version';
    }

    public function handleData($data, $db)
    {
        $db->update('hh_user_toothbrush')
            ->cols([
                'version' => $this->formatVersion($data['version']),
                'model' => $data['model'],
                'add_time' => time()
            ])->where("mac='" . $data['mac'] . "'")->query();
    }

    protected function formatVersion($version)
    {
        return substr_replace($version, '.', -1, 0);
    }
}
