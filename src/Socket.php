<?php

namespace PgnChessServer;

use PGNChess\Game;
use PGNChess\PGN\Symbol;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Socket implements MessageComponentInterface {

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
      $move = explode(' ', $msg);
      try {
          $isLegalMove = $this->game->play($move[0], $move[1]);
      } catch(\Exception $e) {
          echo "{$e->getMessage()}\n";
      }

      $this->client->send(json_encode($isLegalMove));
    }

    public function onClose(ConnectionInterface $conn) {
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
