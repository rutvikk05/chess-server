## PHP Chess Server

PHP Ratchet WebSocket server using [PHP Chess](https://github.com/chesslablab/php-chess).

### Demo

Check out [this demo](https://www.chesslablab.com).

### Setup

Clone the `chesslablab/chess-server` repo into your projects folder as it is described in the following example:

    $ git clone https://github.com/rutvikk05/chess-server.git

Then `cd` the `chess-server` directory and install the Composer dependencies:

    $ composer install

Create an `.env` file:

    $ cp .env.example .env

Finally if you're not using Docker make sure to install the Stockfish chess engine.

```
$ sudo apt-get install stockfish
```

### WebSocket Server

Start the server:

```
$ php cli/ws-server.php
Welcome to PHP Chess Server
Commands available:
/accept {"jwt":"<string>"} Accepts a request to play a game.
/draw {"action":["accept","decline","propose"]} Allows to offer a draw.
/heuristics Takes a balanced heuristic picture of the current game.
/heuristics_bar {"fen":"<string>"} Takes an expanded heuristic picture of the current position.
/leave {"action":["accept"]} Allows to leave a game.
/legal_sqs {"position":"<string>"} Returns the legal squares of a piece.
/online_games Returns the online games waiting to be accepted.
/play_fen {"fen":"<string>"} Plays a chess move in shortened FEN format.
/randomizer {"turn":"<string>","items":"<string>"} Starts a random position.
/rematch {"action":["accept","decline","propose"]} Allows to offer a rematch.
/resign {"action":["accept"]} Allows to resign a game.
/gm Returns a computer generated response to the current position.
/restart {"hash":"<string>"} Restarts a game.
/start {"variant":["960","classical"],"mode":["analysis","gm","fen","pgn","play","stockfish"],"fen":"<string>","movetext":"<string>","color":["w","b"],"settings":"<string>"} Starts a new game.
/stockfish {"options":{"Skill Level":"int"},"params":{"depth":"int"}} Returns Stockfish's response to the current position.
/takeback {"action":["accept","decline","propose"]} Allows to manage a takeback.
/undo Undoes the last move.

Listening to commands...
```

Open a console in your favorite browser and run commands:

    const ws = new WebSocket('ws://127.0.0.1:8080');
    ws.onmessage = (res) => { console.log(res.data) };
    ws.send('/start analysis');

### Secure WebSocket Server

> Before starting the secure WebSocket server for the first time, make sure to copy the `certificate.crt` and `private.key` files into the `ssl` folder as explained in [A Simple Example of SSL/TLS WebSocket With ReactPHP and Ratchet](https://medium.com/geekculture/a-simple-example-of-ssl-tls-websocket-with-reactphp-and-ratchet-e03be973f521).

Start the server:

	$ php cli/wss-server.php

Open a console in your favorite browser and run commands:

    const ws = new WebSocket('wss://pchess.net:8443');
    ws.onmessage = (res) => { console.log(res.data) };
    ws.send('/start analysis');

### License

The MIT License.

### Contributions

See the [contributing guidelines](https://github.com/chesslablab/chess-server/blob/master/CONTRIBUTING.md).

Happy learning and coding! Thank you, and keep it up.
