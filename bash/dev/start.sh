#!/bin/bash

read -p "This will bootstrap the development environment. Are you sure to continue? (y|n) " -n 1 -r
echo    # (optional) move to a new line
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    exit 1
fi

# cd the app's root directory
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
APP_PATH="$(dirname $(dirname $DIR))"
cd $APP_PATH

# generate a development SSL certificate
cd ssl
openssl genrsa -des3 -passout pass:foobar -out programarivm.com.pem 2048
openssl req -passin pass:foobar -new -sha256 -key programarivm.com.pem -subj "/C=US/ST=CA/O=Warthog, Inc./CN=programarivm.com" -reqexts SAN -config <(cat /etc/ssl/openssl.cnf <(printf "[SAN]\nsubjectAltName=DNS:programarivm.com,DNS:www.programarivm.com")) -out programarivm.com.csr
openssl x509 -passin pass:foobar -req -days 365 -in programarivm.com.csr -signkey programarivm.com.pem -out programarivm.com.crt
openssl rsa -passin pass:foobar -in programarivm.com.pem -out programarivm.com.key
