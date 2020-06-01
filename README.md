## PGN Chess Server

<p align="center">
	<img src="https://github.com/programarivm/pgn-chess/blob/master/resources/chess-board.jpg" />
</p>

PHP Ratchet WebSocket chess server using [PGN Chess](https://github.com/programarivm/pgn-chess) as its chess board representation to play chess games.

### Set Up

Create an `.env` file:

    cp .env.example .env

Bootstrap the environment:

    bash/dev/start.sh

Find out your Docker container's IP address:

    docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' pgn_chess_server_php_fpm

### Create a PGN Chess Database

	docker exec -it --user 1000:1000 pgn_chess_server_php_fpm php cli/db-create.php
	This will remove the current PGN Chess database and the data will be lost.
	Do you want to proceed? (Y/N): y

### Seed the Database with Games

	docker exec -it --user 1000:1000 pgn_chess_server_php_fpm php cli/db-seed.php data/01_games.pgn
	This will search for valid PGN games in the file.
	Large files (for example 50MB) may take a few seconds to be inserted into the database.
	Do you want to proceed? (Y/N): y
	Good! This is a valid PGN file. 512 games were inserted into the database.

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
	Trying 172.23.0.2...
	Connected to 172.23.0.2.
	Escape character is '^]'.
	/start training
	{"message":"Game started in training mode."}
	/play e4
	{"message":"Whoops! This seems to be an invalid command. Did you provide a valid parameter?"}
	/play w e4
	{"legal":true}
	/play w c5
	{"legal":false}
	/play b c5
	{"legal":true}
	/play w Nf3
	{"legal":true}
	/metadata
	{"metadata":{"Event":"TCh-FRA Top 12 2018","Site":"Brest FRA","Date":"2018.05.28","Round":"3.1","White":"Kveinys, Aloyzas","Black":"Bellaiche, Anthony","Result":"1-0","WhiteElo":"2493","BlackElo":"2459","EventDate":"2018.05.26","ECO":"B40","movetext":"1.e4 c5 2.Nf3 e6 3.g3 Nc6 4.Bg2 Nf6 5.Qe2 d5 6.exd5 Nxd5 7.O-O Be7 8.Rd1 Qb6 9.c4 Nf6 10.Na3 O-O 11.Nc2 Re8 12.d3 h6 13.b3 e5 14.Re1 Bf8 15.Bb2 Bg4 16.Qd2 Qc7 17.h3 Bh5 18.Nh4 Rad8 19.g4 Bg6 20.Nxg6 fxg6 21.Re3 Nd7 22.Rae1 Be7 23.f4 exf4 24.Re6 Nf8 25.R6e4 g5 26.Qe2 Qd7 27.d4 cxd4 28.Nxd4 Nxd4 29.Bxd4 Bb4 30.Rxe8 Qxd4+ 31.Kh1 Rxe8 32.Qxe8 Bxe1 33.Bd5+ Kh7 34.Qxf8 Qe5 35.Bg8+ 1-0"}}

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
