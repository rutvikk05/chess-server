#!/bin/bash

filename=../../storage/pchess.log
maxsize=10485760 # 10 MB
filesize=$(stat -c%s "$filename")

if (( filesize > maxsize ))
then
    cat /dev/null > ../../storage/pchess.log
fi
