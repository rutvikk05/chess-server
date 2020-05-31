<?php

namespace PgnChessServer;

use PgnChessServer\Socket;
use Ratchet\Server\IoServer;

require __DIR__  . '/../vendor/autoload.php';

$server = IoServer::factory(
    new Socket(),
    8080
);

$server->run();
