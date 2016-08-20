#!/bin/bash

cd "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

function reactor_init() {
    wait-for-service $MYSQL_HOST 3306
    wait-for-service localhost 80
    while ! echo "show databases;" | mysql -B -h $MYSQL_HOST -u $MYSQL_USER --password=$MYSQL_PASSWORD > /dev/null; do
        sleep 1
        echo Waiting mysql user created
    done
    sleep 3
    make build
}

reactor_init &

start-nginx-php-nxlog
