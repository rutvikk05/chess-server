<?php

namespace PgnChessServer\Socket;

use PGNChess\Game;
use PGNChess\PGN\Symbol;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Telnet implements MessageComponentInterface {

    private $client;

    private $game;

    public function __construct() {
        echo "Welcome to PGN Chess Server" . PHP_EOL;
        echo "Examples of valid moves:" . PHP_EOL;
        echo '"w e4"' . PHP_EOL;
        echo '"b e5"' . PHP_EOL;
        echo "Listening to messages..." . PHP_EOL;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->client = $conn;
        $this->game = new Game;

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
      $msg = explode(' ', $msg);
      $color = preg_replace('~[.[:cntrl:][:space:]]~', '', $msg[0]);
      $move = preg_replace('~[.[:cntrl:][:space:]]~', '', $msg[1]);
      try {
          $isLegalMove = $this->game->play($color, $move);
      } catch(\Exception $e) {
          $isLegalMove = false;
          echo "{$e->getMessage()}\n";
      }

      $this->client->send(var_export($isLegalMove, true) . PHP_EOL);
    }

    public function onClose(ConnectionInterface $conn) {
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
