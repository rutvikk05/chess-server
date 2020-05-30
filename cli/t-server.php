<?php

namespace PgnChessServer;

use Ratchet\Server\IoServer;
use PgnChessServer\Socket\Telnet;

require __DIR__  . '/../vendor/autoload.php';

$server = IoServer::factory(
    new Telnet(),
    8080
);

$server->run();
