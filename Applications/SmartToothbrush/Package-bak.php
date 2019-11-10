<?php

class Package
{
    ///////////设备端口
    /**
     * 接收类型消息
     */
    const RESULT = 0x01; // 刷牙结果上报
    const BOOT_DATA = 0x02;  // 引导页数据上报
    const QUERY_DATE = 0x05;     // mcu查询⽇期指令
    const CLIENT_OK = 0x06;          // 发给服务器的OK指令
    const CLIENT_NG = 0x07;    // 发给服务器的NG指令
    const CLIENT_TYPE = 0x09;       // ⽛刷型号、固件信息上报
    const CLIENT_CONFIG = 0x0A;     //⽛刷设置信息上报
    const CLIENT_STATUS = 0x0B;     // 运⾏状态数据上报
    /**
     * 发送类型消息
     */
    const BOOT_CMD = 0x33; //引导⻚发送控制指令（服务器→⽛刷）
    const SET_DATE = 0x35;     // 服务器下发⽇期指令
    const SERVER_OK = 0x36;  //发给⽛刷硬件的OK指令
    const SERVER_NG = 0x37;  //发给⽛刷硬件的NG指令
    const CONTROL = 0x3A;  //服务器→MCU控制指令
    const CHANGE_IP = 0x3E;  //服务器IP和端⼝更改 
    const QUERY = 0x3F;  //服务器、mcu查询命令

    static public function handleResult($package)
    {
        $data = unpack('H2header/H2seq/H2type/H2length/H12mac/H2year/H2month/H2day/H2hour/H2minute/H2second/H2t1_seconds/H2t1_clean/H2t1_press/H2t2_seconds/H2t2_clean/H2t2_press/H2t3_seconds/H2t3_clean/H2t3_press/H2t4_seconds/H2t4_clean/H2t4_press/H2t5_seconds/H2t5_clean/H2t5_press/H2t6_seconds/H2t6_clean/H2t6_press/H*', $package);
        // /H2t7_seconds/H2t7_clean/H2t7_press
        // /H2t8_seconds/H2t8_clean/H2t8_press
        // /H2t9_seconds/H2t9_clean/H2t9_press
        // /H2t10_seconds/H2t10_clean/H2t10_press
        // /H2t11_seconds/H2t11_clean/H2t11_press
        // /H2t12_seconds/H2t12_clean/H2t12_press
        // /H2t13_seconds/H2t13_clean/H2t13_press
        // /H2t14_seconds/H2t14_clean/H2t14_press
        // /H2t15_seconds/H2t15_clean/H2t15_press
        // /H2t16_seconds/H2t16_clean/H2t16_press
        // /H2crc1/H2crc2', $package);
        var_export($data);
        LogServer::log('info', "data: " . json_encode($data) . "\n");
    }

    public function handleBootDate($package)
    {
        $data = unpack('H2header/H2seq/H2type/H2length/H24mac/H2position/H2press/H2hight/H2low', $package);
        var_export($data);
        LogServer::log('info', "data: " . json_encode($data) . "\n");
    }
}
