#!/bin/sh

read -p "Database Name: "  DATABASE

TIMESTAMP=$(date +%s)
MYSQLDUMP_DIR="/opt/lampp/bin/mysqldump"
USER="root"
HOST="127.0.0.1" 

`$MYSQLDUMP_DIR \
--databases \
--user=$USER \
--password \
--host=$HOST \
--protocol=tcp \
--port=3306 \
--default-character-set=utf8 \
--no-create-info=TRUE \
--skip-triggers \
--ignore-table=$DATABASE.failed_jobs \
--ignore-table=$DATABASE.migrations \
$DATABASE > \
$PWD/$DATABASE$TIMESTAMP.sql`

