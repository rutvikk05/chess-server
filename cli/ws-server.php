<?php

namespace PgnChessServer;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use PgnChessServer\Socket\Ws as WsSocket;

require __DIR__  . '/../vendor/autoload.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WsSocket()
        )
    ),
    8080
);

$server->run();
