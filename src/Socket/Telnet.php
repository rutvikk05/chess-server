<?php

namespace PgnChessServer\Socket;

use PGNChess\Game;
use PGNChess\PGN\Symbol;
use PgnChessServer\Command\Help;
use PgnChessServer\Command\Play;
use PgnChessServer\Command\Quit;
use PgnChessServer\Command\Start;
use PgnChessServer\Parser\CommandParser;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Telnet implements MessageComponentInterface {

    private $client;

    private $game;

    public function __construct()
    {
        echo "Welcome to PGN Chess Server" . PHP_EOL;
        echo Help::output() . PHP_EOL;;
        echo "Listening to commands..." . PHP_EOL;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->client = $conn;

        echo "New connection ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (CommandParser::validate($msg)) {
            $argv = CommandParser::$argv;
            switch ($argv[0]) {
                case Help::$name:
                    $this->client->send(Help::output() . PHP_EOL);
                    break;
                case Play::$name:
                    try {
                        $isLegalMove = $this->game->play($argv[1], $argv[2]);
                    } catch(\Exception $e) {
                        $isLegalMove = false;
                    }
                    $this->client->send(var_export($isLegalMove, true) . PHP_EOL);
                    break;
                case Quit::$name:
                    unset($this->game);
                    $this->client->send("Good bye!" . PHP_EOL);
                    break;
                case Start::$name:
                    $this->game = new Game;
                    $this->client->send("Game started in {$argv[1]} mode." . PHP_EOL);
                    break;
            }
        } else {
            $this->client->send("Invalid command." . PHP_EOL);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
