<?php

use Workerman\Worker;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once 'LoggerServer.php';

$logger_worker = new Worker("Websocket://0.0.0.0:2207");
$logger_worker->name = 'LoggerServer';
$logger_worker->count = 1;

$logger_worker->onWorkerStart = array('LoggerServer', 'onWorkerStart');
$logger_worker->onConnect     = array('LoggerServer', 'onConnect');
$logger_worker->onMessage     = array('LoggerServer', 'onMessage');
$logger_worker->onClose       = array('LoggerServer', 'onClose');
$logger_worker->onWorkerStop  = array('LoggerServer', 'onWorkerStop');

$log = new LoggerServer($logger_worker);

// 如果不是在根目录启动，则运行runAll方法

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
