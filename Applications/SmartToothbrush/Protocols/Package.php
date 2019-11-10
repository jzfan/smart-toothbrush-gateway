<?php

namespace Protocols;

use Protocols\PackageTypeFactory;

class Package
{
    public static function input($buffer)
    {
        //长度小于2，继续等待
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
        $type = PackageTypeFactory::getType($code);
        return $type->getData($buffer);
    }



    /**
     * 编码
     * @param array $order
     *
     * @return string 
     */
    public static function encode($order)
    {
        $str = 'aa01';
        $cmd = '3a';
        $length = '15';
        $mac = '98D86379C8E8';
        $mode = '030101';
        $crc = '1234';

        $high = pack('H*', $str . $cmd . $length);
        $low = pack('H*', $mode . $crc);
        return $high . $mac . $low;
    }
}
