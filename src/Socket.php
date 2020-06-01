<?php

namespace PgnChessServer;

use PGNChess\Game;
use PGNChess\PGN\Symbol;
use PgnChessServer\Command\Captures;
use PgnChessServer\Command\Help;
use PgnChessServer\Command\History;
use PgnChessServer\Command\Piece;
use PgnChessServer\Command\Pieces;
use PgnChessServer\Command\Play;
use PgnChessServer\Command\Quit;
use PgnChessServer\Command\Start;
use PgnChessServer\Command\Status;
use PgnChessServer\Parser\CommandParser;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Socket implements MessageComponentInterface
{
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

        echo "New connection ({$conn->resourceId})" . PHP_EOL;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (CommandParser::validate($msg)) {
            $argv = CommandParser::$argv;
            switch ($argv[0]) {
                case Captures::$name:
                    $this->client->send(
                        json_encode([
                            'captures' => $this->game->captures(),
                        ]) . PHP_EOL
                    );
                    break;
                case History::$name:
                    $this->client->send(
                        json_encode([
                            'history' => $this->game->history(),
                        ]) . PHP_EOL
                    );
                    break;
                case Piece::$name:
                    try {
                        $this->client->send(
                            json_encode([
                                'piece' => $this->game->piece($argv[1])
                            ]) . PHP_EOL
                        );
                    } catch(\Exception $e) {
                        $this->client->send(
                            json_encode([
                                'message' => 'Invalid square.'
                            ]) . PHP_EOL
                        );
                    }
                    break;
                case Pieces::$name:
                    $this->client->send(
                        json_encode([
                            'piece' => $this->game->pieces($argv[1])
                        ]) . PHP_EOL
                    );
                    break;
                case Play::$name:
                    try {
                        $this->client->send(
                            json_encode([
                                'legal' => $this->game->play($argv[1], $argv[2])
                            ]) . PHP_EOL
                        );
                    } catch(\Exception $e) {
                        $this->client->send(
                            json_encode([
                                'message' => 'Invalid move.'
                            ]) . PHP_EOL
                        );
                    }
                    break;
                case Quit::$name:
                    unset($this->game);
                    $this->client->send(
                        json_encode([
                            'message' => 'Good bye!'
                        ]) . PHP_EOL
                    );
                    break;
                case Start::$name:
                    $this->game = new Game;
                    $this->client->send(
                        json_encode([
                            'message' => "Game started in {$argv[1]} mode."
                        ]) . PHP_EOL
                    );
                    break;
                case Status::$name:
                    $this->client->send(
                        json_encode([
                            'status' => $this->game->status(),
                        ]) . PHP_EOL
                    );
                    break;
            }
        } else {
            $this->client->send(
                json_encode([
                    'message' => 'Whoops! This seems to be an invalid command. Did you provide a valid parameter?'
                ]) . PHP_EOL
            );
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
