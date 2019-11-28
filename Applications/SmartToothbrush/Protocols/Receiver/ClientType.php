<?php

namespace Protocols\Receiver;

use Protocols\Utils;

class ClientType extends ReceiverTypes
{
    public function getDecodeRule()
    {
        return 'H2header/H2seq/H2code/H2length/H24mac/H2model/H2version';
    }

    public function handleData($data, $db)
    {
        if (!$this->shouldUpdate()) {
            return false;
        }
        if ($this->update($data, $db)) {
            return $this->replyOk($data['mac'], Utils::CLIENT_TYPE);
        }
        $_SESSION['update_config'] = false;
    }

    protected function update($data, $db)
    {
        return $db->update('hh_user_toothbrush')
            ->cols([
                'version' => $this->formatVersion($data['version']),
                'model' => $data['model'],
                'add_time' => time()
            ])->where("mac='" . $data['mac'] . "'")->query();
    }

    protected function shouldUpdate()
    {
        if (!hasSession(['update_config'])) {
            $_SESSION['update_config'] = true;
            return true;
        }
        return false;
    }

    protected function formatVersion($version)
    {
        return substr_replace($version, '.', -1, 0);
    }
}
