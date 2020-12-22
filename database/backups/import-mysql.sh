#!/bin/sh

read -p "File to be imported: "  FILE
read -p "DATABASE: "  DATABASE

MYSQL_DIR="/opt/lampp/bin/mysql"
USER="root"
HOST="127.0.0.1" 

`$MYSQL_DIR \
--user=$USER \
--password \
--host=$HOST \
--protocol=tcp \
--port=3306 \
--default-character-set=utf8 \
$DATABASE < \
$PWD/$FILE.sql`
