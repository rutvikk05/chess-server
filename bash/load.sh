#!/bin/bash

read -p "This will load the games in the data/prod folder. Are you sure to continue? (y|n) " -n 1 -r
echo    # (optional) move to a new line
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    exit 1
fi

SECONDS=0;

# cd the app's root directory
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
APP_PATH="$(dirname $DIR)"
cd $APP_PATH

for file in data/prod/*
do
  docker exec -it --user 1000:1000 pgn_chess_server_php_fpm php cli/db-seed.php $file --quiet
  echo "$SECONDS s...";
done

echo "The loading of games is completed.";
