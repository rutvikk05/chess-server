#!/bin/bash

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
FILE_PATH="${SCRIPT_DIR}/../storage/pchess.log"
MAX_SIZE=10485760
FILE_SIZE=$(stat -c%s "$FILE_PATH")

if (( FILE_SIZE > MAX_SIZE ))
then
    cat /dev/null > $FILE_PATH
fi
