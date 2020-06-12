<?php

namespace PgnChessServer;

use Dotenv\Dotenv;
use PGNChess\Game;
use PGNChess\PGN\Symbol;
use PgnChessServer\Command\Start;
use PgnChessServer\Command\Quit;
use PgnChessServer\Exception\ParserException;
// use PgnChessServer\Mode\AiMode;
use PgnChessServer\Mode\DatabaseMode;
// use PgnChessServer\Mode\PlayerMode;
use PgnChessServer\Mode\TrainingMode;
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

        isset($this->games[$from->resourceId])
            ? $game = $this->games[$from->resourceId]->getGame()
            : $game = null;

        $argv = $this->parser->argv;

        if ($game) {
            if (is_a($cmd, Quit::class)) {
                unset($this->games[$from->resourceId]);
                $res = [
                    'message' => 'Good bye!',
                ];
            } else {
                $res = $this->games[$from->resourceId]->res($argv, $cmd);
            }
        } elseif (is_a($cmd, Start::class)) {
            switch ($argv[1]) {
                case DatabaseMode::NAME:
                    $mode = new DatabaseMode($argv, $cmd, new Game);
                    $this->games[$from->resourceId] = $mode;
                    $argv[2] === Symbol::BLACK ? $res['move'] = $mode->getMove() : null;
                    break;
                case TrainingMode::NAME:
                    $this->games[$from->resourceId] = new TrainingMode(new Game);
                    break;
            }
            $res['message'] = "Game started in {$argv[1]} mode.";
        } elseif (in_array(Start::class, $cmd->dependsOn)) {
            $res = [
                'message' => 'A game needs to be started first for this command to be allowed.',
            ];
        }

        $client->send(json_encode($res) . PHP_EOL);
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
