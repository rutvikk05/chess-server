## PGN Chess Server

Ratchet WebSocket server listening to PGN chess moves.

### Set up the Environment

Create an `.env` file:

    cp .env.example .env

Bootstrap the development environment:

    bash/dev/start.sh

### Start the Server

    docker exec -it --user 1000:1000 pgn_chess_server_php_fpm php src/chess-server.php

### License

The MIT License (MIT) Jordi Bassagañas.

### Contributions

Would you help make this app better?

- Feel free to send a pull request
- Drop an email at info@programarivm.com with the subject "PGN Chess Server"
- Leave me a comment on [Twitter](https://twitter.com/programarivm)

Thank you.
