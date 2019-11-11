<?php

namespace Protocols;

use Protocols\PackageHandlerFactory;

class Package
{
    public static function input($buffer)
    {
        //长度小于4，继续等待
        if (strlen($buffer) < 4) {
            return 0;
        }
        //解包
        $unpack_data = unpack('H2header/H2seq/H2code/H2length', $buffer);

        if ($unpack_data['header'] !== 'aa') {
            return false;
        }
        //返回包长
        if ($unpack_data['code'] === Utils::RESULT) {
            return $unpack_data['length'] + 22;
        }
        return $unpack_data['length'] + 6;
    }


    /**
     * 解码
     * @param string $buffer
     *
     * @return array 
     */
    public static function decode($buffer)
    {
        $code = unpack('H2code', substr($buffer, 2, 1))['code'];
        $receiver = PackageHandlerFactory::getReceiver($code);
        return $receiver->getData($buffer);
    }



    /**
     * 编码
     * @param array $order
     *
     * @return string 
     */
    public static function encode($arr)
    {
        $seq = $arr['seq'] ?? '01';
        $length = $arr['length'];
        $data = $arr['data'] ?? '';

        $high = pack('H*', 'aa' . $seq . $arr['code'] . $length);
        $low = pack('H*', $data . '1234');

        var_export('aa' . $seq . $arr['code'] . $length . $arr['mac'] . $data . '1234' . "\n");

        return $high . $arr['mac'] . $low;
    }
}
