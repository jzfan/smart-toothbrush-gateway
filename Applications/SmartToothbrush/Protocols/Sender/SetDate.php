<?php

namespace Protocols\Sender;

use GatewayWorker\Lib\Gateway;

class SetDate extends SenderTypes
{
    public function cmd()
    {
        return '35';
    }

    public function length()
    {
        return '14';
    }

    public function msg()
    {
        return date('ymdHis');
    }

    public function send($mac, $data)
    {
        $data = [
            'code' => $this->cmd(),
            'lenght' => $this->length(),
            'mac' => $mac,
            'data' => $this->msg()
        ];
        $cid = Gateway::getClientIdByUid($mac)[0];
        Gateway::sendToClient($cid, $data);
    }
}
