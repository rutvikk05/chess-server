## PGN Chess Server

[![Build Status](https://travis-ci.org/programarivm/pgn-chess-server.svg?branch=master)](https://travis-ci.org/programarivm/pgn-chess-server)

<p align="center">
	<img src="https://github.com/programarivm/pgn-chess/blob/master/resources/chess-board.jpg" />
</p>

PHP Ratchet WebSocket server using [PHP Chess](https://github.com/programarivm/php-chess).

### Telnet Server

Start the server:

    $ php cli/t-server.php
    Welcome to PGN Chess Server
	Commands available:
	/captures Gets the pieces captured by both players.
	/help Provides information on the commands available.
	/history The current game's history.
	/ischeck Finds out if the game is in check.
	/ismate Finds out if the game is over.
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
	{"d":"w d4","message":"Game started in pvd mode."}
	/history
	{"history":[{"pgn":"d4","color":"w","identity":"P","position":"d2","isCapture":false,"isCheck":false}]}
	/play b d6
	{"I":"b d6","d":"w Nf3"}
	/history
	{"history":[{"pgn":"d4","color":"w","identity":"P","position":"d2","isCapture":false,"isCheck":false},{"pgn":"d6","color":"b","identity":"P","position":"d7","isCapture":false,"isCheck":false},{"pgn":"Nf3","color":"w","identity":"N","position":"g1","isCapture":false,"isCheck":false}]}
	/play b g6
	{"I":"b g6","d":"w c4"}
	/piece g6
	{"piece":{"color":"b","identity":"P","position":"g6","moves":["g5"]}}
	/quit
	{"message":"Good bye!"}

### WebSocket Server

Start the WebSocket server:

    $ php cli/ws-server.php

Open a console in your favorite browser and run commands:

    const ws = new WebSocket('ws://172.23.0.3:8080');
    ws.onmessage = (res) => { console.log(res.data) };
    ws.send('/start pvd b');
    {"d":"w d4","message":"Game started in pvd mode."}
	ws.send('/play b d6');
	{"I":"b d6","d":"w c4"}
	ws.send('/play b a6');
	{"I":"b a6","d":null,"message":"Mmm, sorry. There are no chess moves left in the database."}

### License

The MIT License (MIT) Jordi Bassagañas.

### Contributions

Would you help make this app better?

- Feel free to send a pull request
- Drop an email at info@programarivm.com with the subject "PGN Chess Server"
- Leave me a comment on [Twitter](https://twitter.com/programarivm)

Thank you.
