## PGN Chess Server

PHP Ratchet WebSocket chess server.

### Set Up

Create an `.env` file:

    cp .env.example .env

Bootstrap the environment:

    bash/dev/start.sh

Find out your Docker container's IP address:

    docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' pgn_chess_server_php_fpm

### WebSocket Server

Start the server:

    docker exec -it --user 1000:1000 pgn_chess_server_php_fpm php cli/ws-server.php

Open a console in your favorite browser and play PGN moves:

    const ws = new WebSocket('ws://172.23.0.2:8080');
    ws.onmessage = (res) => { console.log(res.data) };
    ws.send('w e4');
    ws.send('b e5');

### Telnet Server

Start the server:

    docker exec -it --user 1000:1000 pgn_chess_server_php_fpm php cli/t-server.php
    Welcome to PGN Chess Server
    Commands available:
    /help										Provides information on the commands available.
    /metadata									Metadata of the current game.
    /play color: w,b pgn: move							Plays a chess move on the board.
    /quit										Quits a game.
    /start mode: database,player,training						Starts a new game.
    /status										The current game status.

    Listening to commands...

Open a command prompt and run commands:

    telnet 172.23.0.2 8080
    /start training
    Game started in training mode.
    /play w e4
    true
    /play w e5
    false
    /play b e5
    true
    /quit
    Good bye!
    /start training
    Game started in training mode.
    /play w Nf3
    true
    /play Nc5
    Invalid command.
    /play b Nc5
    false
    /play b Nc6
    true

### Development

Should you want to play around with the development environment follow the steps below.

Run the tests:

    docker exec -it pgn_chess_server_php_fpm vendor/bin/phpunit tests

### License

The MIT License (MIT) Jordi Bassagañas.

### Contributions

Would you help make this app better?

- Feel free to send a pull request
- Drop an email at info@programarivm.com with the subject "PGN Chess Server"
- Leave me a comment on [Twitter](https://twitter.com/programarivm)

Thank you.
