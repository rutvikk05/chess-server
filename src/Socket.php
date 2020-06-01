<?php

namespace PgnChessServer;

use Dotenv\Dotenv;
use PGNChess\Game;
use PGNChess\PGN\Symbol;
use PgnChessServer\Command\Captures;
use PgnChessServer\Command\Help;
use PgnChessServer\Command\History;
use PgnChessServer\Command\Metadata;
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
    private $clients = [];

    private $games = [];

    public function __construct()
    {
        $dotenv = new Dotenv(__DIR__.'/../');
        $dotenv->load();

        echo "Welcome to PGN Chess Server" . PHP_EOL;
        echo Help::output() . PHP_EOL;;
        echo "Listening to commands..." . PHP_EOL;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = $conn;

        echo "New connection ({$conn->resourceId})" . PHP_EOL;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $client = $this->clients[$from->resourceId];
        $game = $this->games[$from->resourceId] ?? null;
        if (CommandParser::validate($msg)) {
            $argv = CommandParser::$argv;
            switch (true) {
                case Captures::$name === $argv[0] && $game:
                    $client->send(
                        json_encode([
                            'captures' => $game->captures(),
                        ]) . PHP_EOL
                    );
                    break;
                case History::$name === $argv[0] && $game:
                    $client->send(
                        json_encode([
                            'history' => $game->history(),
                        ]) . PHP_EOL
                    );
                    break;
                case Metadata::$name === $argv[0] && $game:
                    $client->send(
                        json_encode([
                            'metadata' => $game->metadata(),
                        ]) . PHP_EOL
                    );
                    break;
                case Piece::$name === $argv[0] && $game:
                    try {
                        $client->send(
                            json_encode([
                                'piece' => $game->piece($argv[1])
                            ]) . PHP_EOL
                        );
                    } catch(\Exception $e) {
                        $client->send(
                            json_encode([
                                'message' => 'Invalid square.'
                            ]) . PHP_EOL
                        );
                    }
                    break;
                case Pieces::$name === $argv[0] && $game:
                    $client->send(
                        json_encode([
                            'piece' => $game->pieces($argv[1])
                        ]) . PHP_EOL
                    );
                    break;
                case Play::$name === $argv[0] && $game:
                    try {
                        $client->send(
                            json_encode([
                                'legal' => $game->play($argv[1], $argv[2])
                            ]) . PHP_EOL
                        );
                    } catch(\Exception $e) {
                        $client->send(
                            json_encode([
                                'message' => 'Invalid move.'
                            ]) . PHP_EOL
                        );
                    }
                    break;
                case Quit::$name === $argv[0] && $game:
                    unset($this->games[$from->resourceId]);
                    $client->send(
                        json_encode([
                            'message' => 'Good bye!'
                        ]) . PHP_EOL
                    );
                    break;
                case Start::$name === $argv[0] && !$game:
                    $this->games[$from->resourceId] = new Game;
                    $client->send(
                        json_encode([
                            'message' => "Game started in {$argv[1]} mode."
                        ]) . PHP_EOL
                    );
                    break;
                case Status::$name === $argv[0] && $game:
                    $client->send(
                        json_encode([
                            'status' => $game->status(),
                        ]) . PHP_EOL
                    );
                    break;
            }
        } else {
            $client->send(
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
