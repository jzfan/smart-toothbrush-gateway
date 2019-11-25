<?php

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use Protocols\Utils;
use \GatewayWorker\Lib\Gateway;
use Protocols\PackageHandlerFactory;

require_once __DIR__ . '/../../vendor/autoload.php';

class Events
{
    public static $db = null;

    public static function onWorkerStart($worker)
    {
        self::$db = new \Workerman\MySQL\Connection('127.0.0.1', '3306', getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_NAME'));
    }

    public static function onConnect($client_id)
    {
        LoggerServer::log(Utils::INFO, "client:{" . $client_id . "} connecting...\n");
    }

    public static function onMessage($client_id, $message)
    {
        self::bindIfNot($client_id, $message['mac']);
        // echo "message received: " . bin2hex($message) . "\n";
        var_export('message type: ' . $message['code'] . "\n");
        $receiver = PackageHandlerFactory::getReceiver($message['code']);
        $receiver->handleData($message, self::$db);
    }

    public static function onClose($client_id)
    {
        // $handlers = PackageHandlerFactory::all('CloseHandler');
        // foreach ($handlers as $handler) {
        //     $handler->handleClose($client_id, self::$db);
        // }
    }

    protected static function bindIfNot($cid, $mac)
    {
        if (!isset($_SESSION['bind'])) {
            Gateway::bindUid($cid, $mac);
            $_SESSION['bind'] = true;
        }
    }
}
