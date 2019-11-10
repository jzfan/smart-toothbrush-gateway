<?php

namespace Protocols\Types;

class ClientConfig extends PackageTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H2mode/H2strength/H2time/H2protect';
    }

    public function handleData($data, $db)
    {
        $db->update('hh_user_toothbrush')
            ->cols([
                'mode' => $data['mode'],
                'strength' => $data['strength'],
                'time' => $this->getSecondes($data['time']),
                'protect' => $data['protect'],
                'add_time' => time()
            ])->where("mac='" . $data['mac'] . "'")->query();
    }

    protected function getSecondes($index)
    {
        return [
            '01' => 60,
            '02' => 105,
            '03' => 120,
            '04' => 150,
            '05' => 180,
            '06' => 0
        ][$index];
    }
}
