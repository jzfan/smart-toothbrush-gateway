<?php

namespace Protocols;

class Utils
{

    ///////////设备端口
    /**
     * 接收类型消息
     */
    const RESULT = '01'; // 刷牙结果上报
    const BOOT_DATA = '02';  // 引导页数据上报
    const QUERY_DATE = '05';     // mcu查询⽇期指令
    const CLIENT_OK = '06';          // 发给服务器的OK指令
    const CLIENT_NG = '07';    // 发给服务器的NG指令
    const CLIENT_TYPE = '09';       // ⽛刷型号、固件信息上报
    const CLIENT_CONFIG = '0a';     //⽛刷设置信息上报
    const CLIENT_STATUS = '0b';     // 运⾏状态数据上报
    /**
     * 发送类型消息
     */
    const BOOT_CMD = '33'; //引导⻚发送控制指令（服务器→⽛刷）
    const SET_DATE = '35';     // 服务器下发⽇期指令
    const SERVER_OK = '36';  //发给⽛刷硬件的OK指令
    const SERVER_NG = '37';  //发给⽛刷硬件的NG指令
    const CONTROL = '3a';  //服务器→MCU控制指令
    const CHANGE_IP = '3e';  //服务器IP和端⼝更改 
    const QUERY = '3f';  //服务器、mcu查询命令

    //logger
    //
    //
    /**
     * Detailed debug information
     */
    const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;

    /**
     * Uncommon events
     */
    const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;

    /**
     * Runtime errors
     */
    const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT = 550;

    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;
}
