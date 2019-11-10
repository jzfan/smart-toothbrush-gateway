<?php

use Workerman\Worker;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

require_once __DIR__ . '/../../vendor/autoload.php';

class LogServer
{

    public static function log($level, $msg)
    {
        $logger = self::getLoger();
        $logger->$level($msg);
    }

    //得到日志
    private  static function getLoger()
    {
        $logger_time = date('Y-m-d');

        $stream = new StreamHandler(__DIR__ . "/log/$logger_time.log", 200);
        $stream->setFormatter(new LineFormatter());

        $logger = new Logger($logger_time);
        $logger->pushHandler($stream);

        return $logger;
    }
}
