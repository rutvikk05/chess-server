<?php

namespace PgnChessServer;

use Dotenv\Dotenv;
use PGNChess\Game;
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
use PgnChessServer\Exception\ParserException;
use PgnChessServer\Parser\CommandParser;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Socket implements MessageComponentInterface
{
    private $clients = [];

    private $games = [];

    private $parser;

    public function __construct()
    {
        $dotenv = new Dotenv(__DIR__.'/../');
        $dotenv->load();
        $this->parser = new CommandParser;

        echo "Welcome to PGN Chess Server" . PHP_EOL;
        echo "Commands available:" . PHP_EOL;
        echo $this->parser->cli->help() . PHP_EOL;
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

        try {
            $cmd = $this->parser->validate($msg);
        } catch (ParserException $e) {
            $client->send(
                json_encode([
                    'message' => $e->getMessage(),
                ]) . PHP_EOL
            );
            return;
        }

        $game = $this->games[$from->resourceId] ?? null;
        $argv = $this->parser->argv;

        if (!$game && get_class($cmd) === Start::class) {
            $this->games[$from->resourceId] = new Game;
            $client->send(
                json_encode([
                    'message' => "Game started in {$argv[1]} mode."
                ]) . PHP_EOL
            );
        } elseif (!$game && in_array(Start::class, $cmd->dependsOn)) {
            $client->send(
                json_encode([
                    'message' => 'A game needs to be started first for this command to be allowed.',
                ]) . PHP_EOL
            );
        } elseif ($game) {
            switch (get_class($cmd)) {
                case Captures::class:
                    $client->send(
                        json_encode([
                            'captures' => $game->captures(),
                        ]) . PHP_EOL
                    );
                    break;
                case History::class:
                    $client->send(
                        json_encode([
                            'history' => $game->history(),
                        ]) . PHP_EOL
                    );
                    break;
                case Metadata::class:
                    $client->send(
                        json_encode([
                            'metadata' => $game->metadata(),
                        ]) . PHP_EOL
                    );
                    break;
                case Piece::class:
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
                case Pieces::class:
                    $client->send(
                        json_encode([
                            'piece' => $game->pieces($argv[1])
                        ]) . PHP_EOL
                    );
                    break;
                case Play::class:
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
                case Quit::class:
                    unset($this->games[$from->resourceId]);
                    $client->send(
                        json_encode([
                            'message' => 'Good bye!'
                        ]) . PHP_EOL
                    );
                    break;
                case Status::class:
                    $client->send(
                        json_encode([
                            'status' => $game->status(),
                        ]) . PHP_EOL
                    );
                    break;
            }
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
