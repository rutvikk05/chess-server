## PGN Chess Server

[![Build Status](https://travis-ci.org/programarivm/pgn-chess-server.svg?branch=master)](https://travis-ci.org/programarivm/pgn-chess-server)

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
	/captures Gets the pieces captured by both players.
	/help Provides information on the commands available.
	/history The current game's history.
	/ischeck Finds out if the game is in check.
	/ismate Finds out if the game is over.
	/metadata Metadata of the current game.
	/piece {"position":"square"} Gets a piece by its position on the board. The "position" parameter is mandatory.
	/pieces {"color":["w","b"]} Gets the pieces on the board by color. The "color" parameter is mandatory.
	/play {"color":["w","b"],"pgn":"move"} Plays a chess move on the board. All parameters are mandatory.
	/quit Quits a game.
	/start {"mode":["pva","pvd","pvp","pvt"],"color":["w","b"]} Starts a new game. The "color" parameter is not required in pvt (player vs themselves) mode.
	/status The current game status.

    Listening to commands...

Open a command prompt and run commands:

	telnet 172.23.0.3 8080
	Trying 172.23.0.3...
	Connected to 172.23.0.3.
	Escape character is '^]'.
	/start pvd b
	{"database":"w d4","message":"Game started in pvd mode."}
	/history
	{"history":[{"pgn":"d4","color":"w","identity":"P","position":"d2","isCapture":false,"isCheck":false}]}
	/play b d6
	{"I":"b d6","database":"w Nf3"}
	/history
	{"history":[{"pgn":"d4","color":"w","identity":"P","position":"d2","isCapture":false,"isCheck":false},{"pgn":"d6","color":"b","identity":"P","position":"d7","isCapture":false,"isCheck":false},{"pgn":"Nf3","color":"w","identity":"N","position":"g1","isCapture":false,"isCheck":false}]}
	/metadata
	{"metadata":{"Event":"3. Sat Djenovici May IM","Site":"Djenovici MNE","Date":"2018.05.27","Round":"9.5","White":"Zherebtsova, Alexandra","Black":"Bozanic, Ivica","Result":"1\/2-1\/2","WhiteElo":"2203","BlackElo":"2176","EventDate":"2018.05.19","ECO":"A42","movetext":"1.d4 d6 2.Nf3 g6 3.c4 Bg7 4.Nc3 f5 5.e4 Nf6 6.exf5 gxf5 7.d5 e5 8.dxe6 Bxe6 9.Bd3 Nc6 10.O-O O-O 11.Re1 Bd7 12.Bg5 Re8 13.c5 Rxe1+ 14.Qxe1 Qf8 15.cxd6 cxd6 16.Bc4+ Kh8 17.Bf4 Re8 18.Qd1 Ne5 19.Bb3 Nxf3+ 20.Qxf3 Bc6 21.Qd1 Ne4 22.Nxe4 fxe4 23.Qxd6 Qxd6 24.Bxd6 Bxb2 25.Rd1 Kg7 26.Bc5 a5 27.Bd4+ Bxd4 28.Rxd4 Kf6 29.Kf1 Ke5 30.Rd2 a4 31.Bc4 b5 32.Be2 b4 33.Ke1 Bd5 34.Bd1 Bc6 35.Rc2 Bd5 36.Rb2 b3 37.axb3 axb3 38.Kd2 Kd4 39.Rb1 Rb8 40.Rb2 Ra8 41.Bxb3 Rb8 42.Kc2 Rxb3 43.Rxb3 Bxb3+ 44.Kxb3 Kd3 45.h4 Ke2 46.f4 exf3 47.gxf3 Kxf3 48.h5 h6 49.Kc3 Kg4 50.Kd3 Kxh5 51.Ke2 Kg4 52.Kf1 1\/2-1\/2"}}
	/play b g6
	{"I":"b g6","database":"w c4"}
	/metadata
	{"metadata":{"Event":"CHN-RUS Summit Blitz 2018","Site":"Qinhuangdao CHN","Date":"2018.05.26","Round":"1.1","White":"Li, Chao2","Black":"Matlakov, Maxim","Result":"1\/2-1\/2","WhiteElo":"2724","BlackElo":"2704","EventDate":"2018.05.26","ECO":"A41","movetext":"1.d4 d6 2.Nf3 g6 3.c4 Bg7 4.Nc3 Bg4 5.e3 Nc6 6.Be2 Nf6 7.h3 Bd7 8.b3 O-O 9.Bb2 e5 10.dxe5 dxe5 11.O-O e4 12.Nd2 Re8 13.a3 h5 14.Qc2 Bf5 15.Rfd1 Qe7 16.Nd5 Nxd5 17.cxd5 Bxb2 18.Qxb2 Ne5 19.Nc4 Rad8 20.Qxe5 1\/2-1\/2"}}
	/piece g6
	{"piece":{"color":"b","identity":"P","position":"g6","moves":["g5"]}}
	/quit
	{"message":"Good bye!"}

### WebSocket Server

Start the WebSocket server:

    docker exec -it --user 1000:1000 pgn_chess_server_php_fpm php cli/ws-server.php

Open a console in your favorite browser and run commands:

    const ws = new WebSocket('ws://172.23.0.3:8080');
    ws.onmessage = (res) => { console.log(res.data) };
    ws.send('/start pvd b');
    {"database":"w d4","message":"Game started in pvd mode."}
	ws.send('/play b d6');
	{"I":"b d6","database":"w c4"}
	ws.send('/play b a6');
	{"I":"b a6","database":null,"message":"Mmm, sorry. There are no chess moves left in the database."}

### Development

Should you want to play around with the development environment follow the steps below.

Run the tests:

	docker exec -it pgn_chess_server_php_fpm vendor/bin/phpunit --configuration phpunit-docker.xml

### License

The MIT License (MIT) Jordi Bassagañas.

### Contributions

Would you help make this app better?

- Feel free to send a pull request
- Drop an email at info@programarivm.com with the subject "PGN Chess Server"
- Leave me a comment on [Twitter](https://twitter.com/programarivm)

Thank you.
