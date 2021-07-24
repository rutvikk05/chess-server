<?php

namespace ChessServer;

use ChessServer\Socket;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use React\Socket\Server;
use React\Socket\SecureServer;

require __DIR__  . '/../vendor/autoload.php';

$loop = \React\EventLoop\Factory::create();

$socket = new Server('8443', $loop);
$socket = new SecureServer($socket, $loop, [
    'local_cert'  => __DIR__  . '/../ssl/cert.pem',
    'local_pk' => __DIR__  . '/../ssl/key.pem',
    'allow_self_signed' => true,
    'verify_peer' => false
]);

$server = new IoServer(
    new HttpServer(
        new WsServer(
            new Socket()
        )
    ),
    $socket,
    $loop
);

$server->run();
