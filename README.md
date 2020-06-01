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
    Welcome to PGN Chess Server
    Commands available:
    /captures									Gets the pieces captured by both players.
    /history									The current game's history.
    /metadata									Metadata of the current game.
    /piece position: square								Gets a piece by its position on the board.
    /pieces color: w,b								Gets the pieces on the board by color.
    /play color: w,b pgn: move							Plays a chess move on the board.
    /quit										Quits a game.
    /start mode: database,player,training						Starts a new game.
    /status										The current game status.

    Listening to commands...

Open a console in your favorite browser and run commands:

    const ws = new WebSocket('ws://172.23.0.2:8080');
    ws.onmessage = (res) => { console.log(res.data) };
    ws.send('/start training');
    {"message":"Game started in training mode."}
    ws.send('/play w e4');
    {"legal":true}
    ws.send('/play w e5');
    {"legal":false}
    ws.send('/play b e5');
    {"legal":true}
    ws.send('/play w foo');
    {"message":"Invalid move."}
    ws.send('/play w Nf3');
    {"legal":true}
    ws.send('/play Nc5');
    {"message":"Invalid command."}
    ws.send('/quit');
    {"message":"Good bye!"}

### Telnet Server

Start the server:

    docker exec -it --user 1000:1000 pgn_chess_server_php_fpm php cli/t-server.php
    Welcome to PGN Chess Server
    Commands available:
    /captures									Gets the pieces captured by both players.
    /history									The current game's history.
    /metadata									Metadata of the current game.
    /piece position: square								Gets a piece by its position on the board.
    /pieces color: w,b								Gets the pieces on the board by color.
    /play color: w,b pgn: move							Plays a chess move on the board.
    /quit										Quits a game.
    /start mode: database,player,training						Starts a new game.
    /status										The current game status.

    Listening to commands...

Open a command prompt and run commands:

    telnet 172.23.0.2 8080
    /start training
    {"message":"Game started in training mode."}
    /play w e4
    {"legal":true}
    /play w e5
    {"legal":false}
    /play b e5
    {"legal":true}
    /play w foo
    {"message":"Invalid move."}
    /play w Nf3
    {"legal":true}
    /play Nc5
    {"message":"Invalid command."}
    /quit
    {"message":"Good bye!"}

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
